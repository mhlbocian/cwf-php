<?php

/*
 * CWF-PHP Framework
 *
 * File: Framework\Query.php
 * Description: SQL query builder
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework;

use Framework\Query\Statement;

final class Query implements Interfaces\Query {

    use Query\Constraints,
        Query\Join,
        Query\Misc,
        Query\SQL,
        Query\Where;

    /**
     * 
     * @var array SQL columns
     */
    private array $columns = [];

    /**
     * 
     * @var array SQL columns and types (used in CREATE TABLE)
     */
    private array $cols_type = [];

    /**
     * 
     * @var array Orders array
     */
    private array $orders = [];

    /**
     * 
     * @var array PDO query parameters
     */
    private array $params = [];

    /**
     * 
     * @var array Array of WHERE conditions
     */
    private array $where = [];

    /**
     * 
     * @var bool True, when SELECT DISTINCT
     */
    private bool $distinct = false;

    /**
     * 
     * @var bool True, when CREATE TABLE IF NOT EXISTS
     */
    private bool $if_not_exists = false;

    /**
     * 
     * @var int|null Limit value (for LIMIT)
     */
    private ?int $limit = null;

    /**
     * 
     * @var int|null Offset value (for OFFSET)
     */
    private ?int $offset = null;

    /**
     * 
     * @var Statement SQL Operation (like INSERT, SELECT)
     */
    private Statement $operation;

    /**
     * 
     * @var string Query table
     */
    private string $table;

    /**
     * 
     * @var string SQL query string
     */
    private string $sql;

    /**
     * Query constructor. Defines the SQL operation
     *
     * @param Operation $operation
     */
    #[\Override]
    public function __construct(Statement $operation) {
        $this->operation = $operation;
    }

    /**
     * Return the parameters array for later bindings
     *
     * @return array
     */
    #[\Override]
    public function Params(): array {
        $params = $this->params;

        /*
         * walk through $where array, and join their values as parameters with
         * prefix 'w' (like :w0, :w1)
         *
         */
        foreach ($this->where as $id => $statement) {
            $params["w{$id}"] = $statement["value"];
        }

        return $params;
    }

    /**
     * Define columns and types
     *
     * @param string $name
     * @param string $type
     * @return Query
     */
    #[\Override]
    public function ColType(string $name, string $type): Query {
        $this->cols_type[$name] = $type;

        return $this;
    }

    /**
     * Set columns
     *
     * @param string $columns
     * @return Query
     */
    #[\Override]
    public function Columns(string ...$columns): Query {
        $this->columns = $columns;

        return $this;
    }

    /**
     * If invoked, add DISTINCT keyword to SELECT operation
     *
     * @return Query
     */
    #[\Override]
    public function Distinct(): Query {
        $this->distinct = true;

        return $this;
    }

    /**
     * If invoked, add IF NOT EXISTS keyword to CREATE TABLE operation
     * 
     * @return Query
     */
    #[\Override]
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
    #[\Override]
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
    #[\Override]
    public function Offset(int $offset): Query {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set ORDER BY
     * 
     * @param string $column
     * @param bool $asc True (ASC), false (DESC)
     * @return Query
     */
    #[\Override]
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
    #[\Override]
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
    #[\Override]
    public function Values(mixed ...$values): Query {
        foreach ($values as $value) {
            $this->params[] = $value;
        }

        return $this;
    }

    /**
     * Magic method that generates the SQL query
     * 
     * @return string SQL Query
     */
    #[\Override]
    public function __toString(): string {
        switch ($this->operation) {
            case Statement::CREATE:
                $this->SQL_Create();
                break;
            case Statement::DELETE:
                $this->SQL_Delete();
                break;
            case Statement::INSERT:
                $this->SQL_Insert();
                break;
            case Statement::UPDATE:
                $this->SQL_Update();
                break;
            case Statement::SELECT:
                $this->SQL_Select();
                break;
        }

        return $this->sql;
    }
}
