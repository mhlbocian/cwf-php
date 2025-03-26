<?php

namespace Framework\Database;

use Exception;

enum Operation: string {

    case CREATE = "create";
    case DELETE = "delete";
    case INSERT = "insert";
    case UPDATE = "update";
    case SELECT = "select";
}

class Query {

    private bool $distinct = false;
    private string $table;
    private string $sql;
    private array $columns = [];
    private array $colspec = [];
    private Operation $operation;

    public function __construct(Operation $operation) {
        $this->operation = $operation;
    }

    public function Columns(string ...$columns): Query {
        $this->columns = $columns;

        return $this;
    }

    public function Distinct(): Query {
        $this->distinct = true;

        return $this;
    }

    public function Table(string $table): Query {
        $this->table = $table;

        return $this;
    }

    public function ColSpec(array $colspec): Query {
        $this->colspec = $colspec;

        return $this;
    }

    private function BuildCreate(): void {
        $this->sql = "create table {$this->table} (";
        foreach ($this->colspec as $col => $type) {
            $this->sql .= "{$col} {$type}, ";
        }
        $this->sql = substr($this->sql, 0, -2) . ")";
    }

    private function BuildSelect(): void {
        $this->sql = "select " . (($this->distinct) ? "distinct " : "");
        if (empty($this->columns)) {
            $this->sql .= "*";
        } else {
            $this->sql .= implode(", ", $this->columns);
        }
        $this->sql .= " from {$this->table}";
    }

    public function QueryString(): string {
        switch ($this->operation) {
            case Operation::CREATE:
                $this->BuildCreate();
                break;
            case Operation::DELETE:
            case Operation::INSERT:
            case Operation::UPDATE:
                throw new Exception("Unimplemented SQL operation");
            case Operation::SELECT:
                $this->BuildSelect();
                break;
        }

        return $this->sql;
    }

    public function __toString(): string {
        return $this->QueryString();
    }
}
