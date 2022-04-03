<?php

namespace Kibo\Phast\Cache\Sqlite;

class Connection extends \PDO {
    private $statements;

    public function prepare($query, $options = null): \PDOStatement {
        if ($options) {
            return parent::prepare($query, $options);
        }
        if (!isset($this->statements[$query])) {
            $this->statements[$query] = parent::prepare($query);
        }
        return $this->statements[$query];
    }

    public function getPageSize(): int {
        return (int) $this->query('PRAGMA page_size')->fetchColumn();
    }
}
