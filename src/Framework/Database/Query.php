<?php

/*
 * CWF-PHP Framework
 * 
 * File: Query.php
 * Description: Framework\Database\Query class
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Database;

use Exception;

enum Operation {

    case CREATE;
    case DELETE;
    case INSERT;
    case UPDATE;
    case SELECT;
}

class Query {

    private bool $distinct = false;
    private bool $if_not_exists = false;
    private ?int $limit = null;
    private ?int $offset = null;
    private int $values_count = 0;
    private int $param_ptr = 0;
    private string $table;
    private string $sql;
    private array $columns = [];
    private array $col_spec = [];
    private array $orders = [];
    private array $params = [];
    private Operation $operation;

    /**
     * Query constructor. Defines SQL operation
     * 
     * @param Operation $operation
     */
    public function __construct(Operation $operation) {
        $this->operation = $operation;
    }
    
    /**
     * Returns parameters array for later bindings
     * 
     * @return array
     */
    public function Get_Params(): array{
        return $this->params;
    }

    /**
     * Set columns and their types. Used in CREATE TABLE
     * 
     * @param array $colspec
     * @return Query
     */
    public function Colspec(string $name, string $type): Query {
        $this->col_spec[] = [$name, $type];

        return $this;
    }

    /**
     * Set columns
     * 
     * @param string $columns
     * @return Query
     */
    public function Columns(string ...$columns): Query {
        $this->columns = $columns;

        return $this;
    }

    /**
     * If invoked, add DISTINCT keyword to SELECT operation
     * 
     * @return Query
     */
    public function Distinct(): Query {
        $this->distinct = true;

        return $this;
    }

    /**
     * If invoked, add IF NOT EXISTS keyword to CREATE TABLE operation
     * @return Query
     */
    public function IfNotExists(): Query {
        $this->if_not_exists = true;

        return $this;
    }

    /**
     * Set LIMIT
     * 
     * @param int $limit
     * @return Query
     */
    public function Limit(int $limit): Query {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set OFFSET
     * 
     * @param int $offset
     * @return Query
     */
    public function Offset(int $offset): Query {
        $this->offset = $offset;

        return $this;
    }

    public function OrderBy(string $column, bool $asc = true): Query {
        $this->orders[] = [$column, ($asc) ? "ASC" : "DESC"];

        return $this;
    }

    /**
     * Set table for query
     * 
     * @param string $table
     * @return Query
     */
    public function Table(string $table): Query {
        $this->table = $table;

        return $this;
    }

    /**
     * Add values to INSERT INTO operation. Can be invoked for each column
     * separately or with multiple parameters for more columns.
     * In the sum, the count of columns and values must be equal and non-zero
     * 
     * @param mixed $values
     * @return Query
     */
    public function Values(mixed ...$values): Query {
        $this->values_count += count($values);

        foreach ($values as $value) {
            $this->params[] = $value;
        }

        return $this;
    }

    /**
     * Makes SQL query for CREATE TABLE operation
     * 
     * @return void
     */
    private function Query_Create(): void {
        if (empty($this->col_spec)) {
            throw new Exception("SQL_CREATE: Empty column(s) specification");
        }

        $this->sql = "CREATE TABLE";

        if ($this->if_not_exists) {
            $this->sql .= " IF NOT EXISTS";
        }

        $this->sql .= " {$this->table} (";

        foreach ($this->col_spec as $col) {
            $this->sql .= "{$col[0]} {$col[1]}, ";
        }

        $this->sql = substr($this->sql, 0, -2) . ")";
    }

    /**
     * Makes SQL query for DELETE operation
     * 
     * @return void
     */
    private function Query_Delete(): void {
        $this->sql = "NOT IMPLEMENTED";
    }

    /**
     * Makes SQL query for INSERT INTO operation
     * 
     * @return void
     */
    private function Query_Insert(): void {
        if (count($this->columns) != $this->values_count || !$this->values_count) {
            throw new Exception("SQL_INSERT: cols and vals size mismatch");
        }

        $this->sql = "INSERT INTO {$this->table} (";
        $this->sql .= implode(", ", $this->columns);
        $this->sql .= ") VALUES (";

        foreach ($this->params as $id => $param) {
            $this->sql .= ":{$id}, ";
        }

        $this->sql = substr($this->sql, 0, -2) . ")";
    }

    /**
     * Makes SQL query for UPDATE operation
     * 
     * @return void
     */
    private function Query_Update(): void {
        $this->sql = "NOT IMPLEMENTED";
    }

    /**
     * Makes SQL query for SELECT operation
     * @return void
     */
    private function Query_Select(): void {
        $this->sql = "SELECT " . (($this->distinct) ? "DISTINCT " : "");
        if (empty($this->columns)) {
            $this->sql .= "*";
        } else {
            $this->sql .= implode(", ", $this->columns);
        }
        $this->sql .= " FROM {$this->table}";

        // [TODO]: WHERE
        // SQL: ORDER BY
        if (!empty($this->orders)) {
            $this->sql .= " ORDER BY";
            foreach ($this->orders as $order) {
                $this->sql .= " {$order[0]} {$order[1]},";
            }
            $this->sql = substr($this->sql, 0, -1);
        }
        // SQL: LIMIT
        if (!is_null($this->limit)) {
            $this->sql .= " LIMIT {$this->limit}";
        }
        // SQL: OFFSET
        if (!is_null($this->offset)) {
            $this->sql .= " OFFSET {$this->offset}";
        }
    }

    /**
     * Returns generated query
     * 
     * @return string
     * @throws Exception
     */
    public function Make_Query(): string {
        switch ($this->operation) {
            case Operation::CREATE:
                $this->Query_Create();
                break;
            case Operation::DELETE:
                $this->Query_Delete();
                break;
            case Operation::INSERT:
                $this->Query_Insert();
                break;
            case Operation::UPDATE:
                $this->Query_Update();
                break;
            case Operation::SELECT:
                $this->Query_Select();
                break;
        }

        return $this->sql;
    }

    public function __toString(): string {
        return $this->Make_Query();
    }
}
