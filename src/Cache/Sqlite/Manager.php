<?php

namespace Kibo\Phast\Cache\Sqlite;

use Kibo\Phast\Common\ObjectifiedFunctions;
use Kibo\Phast\Logging\LoggingTrait;

class Manager {
    use LoggingTrait;

    private $cacheRoot;

    private $name;

    private $maxSize;

    private $database;

    private $autorecover = true;

    public function __construct(string $cacheRoot, string $name, int $maxSize) {
        $this->cacheRoot = $cacheRoot;
        $this->name = $name;
        $this->maxSize = $maxSize;
    }

    public function setAutorecover(bool $autorecover): void {
        $this->autorecover = $autorecover;
    }

    public function get(string $key, ?callable $cb, int $expiresIn, ObjectifiedFunctions $functions) {
        return $this->autorecover(function () use ($key, $cb, $expiresIn, $functions) {
            $query = $this->getDatabase()->prepare('
                SELECT value
                FROM cache
                WHERE
                    key = :key
                    AND (expires_at IS NULL OR expires_at > :time)
            ');
            $query->execute([
                'key' => $this->hashKey($key),
                'time' => $functions->time(),
            ]);
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            if ($row && $this->unserialize($row['value'], $value)) {
                return $value;
            }
            if ($cb === null) {
                return null;
            }
            $value = $cb();
            $this->set($key, $value, $expiresIn, $functions);
            return $value;
        });
    }

    public function set(string $key, $value, int $expiresIn, ObjectifiedFunctions $functions) {
        return $this->autorecover(function () use ($key, $value, $expiresIn, $functions) {
            $db = $this->getDatabase();
            $tries = 10;
            while ($tries--) {
                try {
                    $db->prepare('
                        REPLACE INTO cache (key, value, expires_at)
                        VALUES (:key, :value, :expires_at)
                    ')->execute([
                        'key' => $this->hashKey($key),
                        'value' => $this->serialize($value),
                        'expires_at' => $expiresIn > 0 ? $functions->time() + $expiresIn : null,
                    ]);
                    return;
                } catch (\PDOException $e) {
                    if (!$this->isFullException($e)) {
                        throw $e;
                    }
                }
                $this->makeSpace();
            }
        });
    }

    private function isFullException(\PDOException $e): bool {
        return preg_match('~13 database or disk is full~', $e->getMessage());
    }

    private function serialize($value): string {
        // gzcompress is always used since it adds a checksum to the data
        return gzcompress(serialize($value));
    }

    private function unserialize(string $value, &$result): bool {
        $value = @gzuncompress($value);
        if ($value === false) {
            return false;
        }
        if ($value === 'b:0;') {
            $result = false;
            return true;
        }
        $value = @unserialize($value);
        if ($value === false) {
            return false;
        }
        $result = $value;
        return true;
    }

    private function hashKey(string $key): string {
        return sha1($key, true);
    }

    private function randomKey(): string {
        return random_bytes(strlen($this->hashKey('')));
    }

    private function getDatabase(): \PDO {
        if (!isset($this->database)) {
            @mkdir(dirname($this->getDatabasePath()), 0700, true);
            $this->checkDirOwner(dirname($this->getDatabasePath()));
            $database = new Connection('sqlite:' . $this->getDatabasePath());
            $database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $database->exec('PRAGMA journal_mode = TRUNCATE');
            $database->exec('PRAGMA synchronous = OFF');
            if (($maxPageCount = $this->getMaxPageCount($database)) !== null) {
                $database->exec(sprintf('PRAGMA max_page_count = %d', $maxPageCount));
            }
            $this->upgradeDatabase($database);
            $this->database = $database;
        }
        return $this->database;
    }

    private function checkDirOwner(string $dir): void {
        $owner = fileowner($dir);
        if ($owner === false) {
            throw new \RuntimeException('Could not get owner of cache dir');
        }
        if (!function_exists('posix_geteuid')) {
            return;
        }
        if ($owner !== posix_geteuid()) {
            throw new \RuntimeException('Cache dir is owner by another user; this is not secure');
        }
    }

    private function getMaxPageCount(Connection $database): ?int {
        return $this->maxSize / $database->getPageSize();
    }

    private function getDatabasePath(): string {
        return $this->cacheRoot . '/' . $this->name . '.sqlite3';
    }

    private function upgradeDatabase(\PDO $database): void {
        // If the cache table is already created, there's nothing to do.
        if ($database->query("
            SELECT 1
            FROM sqlite_master
            WHERE
                type = 'table'
                AND name = 'cache'
        ")->fetchColumn()) {
            return;
        }

        try {
            $database->exec('BEGIN EXCLUSIVE');

            // After acquiring an exclusive lock, check for the table again;
            // it may have been created after the last check and before the lock.
            if ($database->query("
                SELECT 1
                FROM sqlite_master
                WHERE
                    type = 'table'
                    AND name = 'cache'
            ")->fetchColumn()) {
                return;
            }

            $database->exec('
                CREATE TABLE cache (
                    key BLOB PRIMARY KEY,
                    value BLOB NOT NULL,
                    expires_at INT
                ) WITHOUT ROWID
            ');

            $database->exec('COMMIT');
        } catch (\Throwable $e) {
            $database->exec('ROLLBACK');
            throw $e;
        }
    }

    private function autorecover(\Closure $fn) {
        try {
            return $fn();
        } catch (\PDOException $e) {
            if (!$this->autorecover) {
                throw $e;
            }
        } catch (\RuntimeException $e) {
            if (!$this->autorecover) {
                throw $e;
            }
            $this->logger()->error('Caught {exceptionClass} during cache operation: {message}; ignoring it', [
                'exceptionClass' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            return null;
        }

        $this->logger()->error('Caught {exceptionClass} during cache operation: {message}; retrying operation', [
            'exceptionClass' => get_class($e),
            'message' => $e->getMessage(),
        ]);

        $this->database = null;
        $this->purge();

        return $fn();
    }

    private function purge(): void {
        @unlink($this->getDatabasePath());
    }

    private function makeSpace(): void {
        $selectQuery = $this->getDatabase()->prepare('
            SELECT key, LENGTH(key) + LENGTH(value) + LENGTH(expires_at) AS length
            FROM cache
            WHERE key >= :key
            ORDER BY key
            LIMIT 1
        ');

        $deleteQuery = $this->getDatabase()->prepare('
            DELETE FROM cache
            WHERE key = :key
        ');

        $this->getDatabase()->exec('BEGIN IMMEDIATE');

        try {
            for ($i = 0; $i < 100; $i++) {
                $selectQuery->execute(['key' => $this->randomKey()]);
                if (!($row = $selectQuery->fetch(\PDO::FETCH_ASSOC))) {
                    $selectQuery->execute(['key' => '']);
                    if (!($row = $selectQuery->fetch(\PDO::FETCH_ASSOC))) {
                        return;
                    }
                }
                $deleteQuery->execute(['key' => $row['key']]);
            }

            $this->getDatabase()->exec('COMMIT');
        } catch (\Throwable $e) {
            $this->getDatabase()->exec('ROLLBACK');
            throw $e;
        }
    }
}
