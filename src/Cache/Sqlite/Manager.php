<?php

namespace Kibo\Phast\Cache\Sqlite;

use Kibo\Phast\Common\ObjectifiedFunctions;

class Manager {
    private $cacheRoot;

    private $maxSize;

    private $database;

    private $getQuery;

    private $setQuery;

    public function __construct(string $cacheRoot, int $maxSize) {
        $this->cacheRoot = $cacheRoot;
        $this->maxSize = $maxSize;
    }

    public function get(string $key, callable $cb = null, int $expiresIn, ObjectifiedFunctions $functions) {
        return $this->autorecover(function () use ($key, $cb, $expiresIn, $functions) {
            $query = $this->getGetQuery();
            $query->execute([
                'key' => $key,
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
            $this->getSetQuery()->execute([
                'key' => $key,
                'value' => serialize($value),
                'expires_at' => $expiresIn > 0 ? $functions->time() + $expiresIn : null,
            ]);
        });
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
}
