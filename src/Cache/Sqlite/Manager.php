<?php

namespace Kibo\Phast\Cache\Sqlite;

use Kibo\Phast\Common\ObjectifiedFunctions;

class Manager {
    private $cacheRoot;

    private $maxSize;

    private $database;

    private $getQuery;

    private $setQuery;

    private $autorecover = true;

    public function __construct(string $cacheRoot, int $maxSize) {
        $this->cacheRoot = $cacheRoot;
        $this->maxSize = $maxSize;
    }

    public function setAutorecover(bool $autorecover): void {
        $this->autorecover = $autorecover;
    }

    public function get(string $key, callable $cb = null, int $expiresIn, ObjectifiedFunctions $functions) {
        return $this->autorecover(function () use ($key, $cb, $expiresIn, $functions) {
            $query = $this->getGetQuery();
            $query->execute([
                'key' => $this->hashKey($key),
                'time' => $functions->time(),
            ]);
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                return unserialize($row['value']);
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
            $this->cleanup();
            $this->getSetQuery()->execute([
                'key' => $this->hashKey($key),
                'value' => serialize($value),
                'expires_at' => $expiresIn > 0 ? $functions->time() + $expiresIn : null,
            ]);
        });
    }

    private function hashKey(string $key): string {
        return sha1($key, true);
    }

    private function randomKey(): string {
        return random_bytes(strlen(sha1('', true)));
    }

    private function getGetQuery(): \PDOStatement {
        $this->getQuery ??= $this->getDatabase()->prepare('
            SELECT value
            FROM cache
            WHERE
                key = :key
                AND (expires_at IS NULL OR expires_at > :time)
        ');
        return $this->getQuery;
    }

    private function getSetQuery(): \PDOStatement {
        $this->setQuery ??= $this->getDatabase()->prepare('
            REPLACE INTO cache (key, value, expires_at)
            VALUES (:key, :value, :expires_at)
        ');
        return $this->setQuery;
    }

    private function getDatabase(): \PDO {
        if (!isset($this->database)) {
            @mkdir(dirname($this->getDatabasePath()), 0700, true);
            $database = new \PDO('sqlite:' . $this->getDatabasePath());
            $database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->upgradeDatabase($database);
            $this->database = $database;
        }
        return $this->database;
    }

    private function getDatabasePath(): string {
        return $this->cacheRoot . '/cache.sqlite3';
    }

    private function upgradeDatabase(\PDO $database): void {
        // If the cache table is already created, there's nothing to do.
        if ($database->query("
            SELECT 1
            FROM sqlite_schema
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
                FROM sqlite_schema
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
        }

        $this->database = null;
        $this->getQuery = null;
        $this->setQuery = null;

        $this->purge();

        return $fn();
    }

    private function purge(): void {
        @unlink($this->getDatabasePath());
    }

    private function cleanup(): void {
        if (!$this->needsCleanup(1.25)) {
            return;
        }

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

        $bytesFreed = 0;

        $this->getDatabase()->exec('BEGIN IMMEDIATE');

        try {
            if (!$this->needsCleanup(1.25)) {
                return;
            }

            while ($bytesFreed < $this->maxSize * .5) {
                $selectQuery->execute(['key' => $this->randomKey()]);
                if (!($row = $selectQuery->fetch(\PDO::FETCH_ASSOC))) {
                    $selectQuery->execute(['key' => '']);
                    if (!($row = $selectQuery->fetch(\PDO::FETCH_ASSOC))) {
                        break;
                    }
                }
                $deleteQuery->execute(['key' => $row['key']]);
                $bytesFreed += $row['length'];
            }

            $this->getDatabase()->exec('COMMIT');
        } catch (\Throwable $e) {
            $this->getDatabase()->exec('ROLLBACK');
            throw $e;
        }
    }

    private function needsCleanup($tolerance = 1): bool {
        if ($this->maxSize <= 0) {
            return false;
        }
        $spaceUsed = ($this->getPageCount() - $this->getFreelistCount()) * $this->getPageSize();
        return $spaceUsed > $this->maxSize * $tolerance;
    }

    private function getPageCount(): int {
        return (int) $this->getDatabase()->query('PRAGMA page_count')->fetchColumn();
    }

    private function getPageSize(): int {
        return (int) $this->getDatabase()->query('PRAGMA page_size')->fetchColumn();
    }

    private function getFreelistCount(): int {
        return (int) $this->getDatabase()->query('PRAGMA freelist_count')->fetchColumn();
    }
}
