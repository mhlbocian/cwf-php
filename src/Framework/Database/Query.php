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

enum Operation: string {

    case CREATE = "create";
    case DELETE = "delete";
    case INSERT = "insert";
    case UPDATE = "update";
    case SELECT = "select";
}

class Query {

    private bool $distinct = false;
    private ?int $limit = null;
    private ?int $offset = null;
    private string $table;
    private string $sql;
    private array $columns = [];
    private array $colspec = [];
    private array $orders = [];
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
     * Set columns and their types. Used in CREATE TABLE
     * 
     * @param array $colspec
     * @return Query
     */
    public function Colspec(string $name, string $type): Query {
        $this->colspec[] = [$name, $type];

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
     * Makes SQL query for CREATE TABLE operation
     * 
     * @return void
     */
    private function Query_Create(): void {
        if (empty($this->colspec)) {
            throw new Exception("SQL_CREATE: Empty column(s) specification");
        }

        $this->sql = "CREATE TABLE {$this->table} (";

        foreach ($this->colspec as $col) {
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
        $this->sql = "NOT IMPLEMENTED";
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
    public function Show_Query(): string {
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
        return $this->Show_Query();
    }
}
