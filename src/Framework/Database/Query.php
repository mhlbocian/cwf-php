<?php

/*
 * CWF-PHP Framework
 *
 * File: Query.php
 * Description: SQL query builder
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Database;

use Exception;

enum Statement {

    case CREATE;
    case DELETE;
    case INSERT;
    case UPDATE;
    case SELECT;
}

enum Operator: string {

    case Eq = "=";
    case Gt = ">";
    case GEq = ">=";
    case Lt = "<";
    case LEq = "<=";
    case NEq = "<>";
    case Btw = "BETWEEN";
    case In = "IN";
    case Like = "LIKE";
}

class Query {

    private array $columns = [];
    private array $col_spec = [];
    private array $constraints = [];
    private array $orders = [];
    private array $params = [];
    private array $where = [];
    private bool $distinct = false;
    private bool $if_not_exists = false;
    private ?int $limit = null;
    private ?int $offset = null;
    private int $val_count = 0;
    private Statement $operation;
    private string $table;
    private string $sql;

    /**
     * Query constructor. Defines SQL operation
     *
     * @param Operation $operation
     */
    public function __construct(Statement $operation) {
        $this->operation = $operation;
    }

    /**
     * Return parameters array for later bindings
     *
     * @return array
     */
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
     * CREATE TABLE: Set columns and their types
     *
     * @param array $colspec
     * @return Query
     */
    public function Colspec(string $name, string $type): Query {
        $this->col_spec[$name] = $type;

        return $this;
    }

    /**
     * CREATE TABLE: Set Foregin Key
     * 
     * @param string $column
     * @param string $reference
     * @return Query
     * @throws Exception
     */
    public function ForeginKey(string $column, string $reference): Query {
        if (!key_exists($column, $this->col_spec)) {
            throw new Exception("QUERY: unknown PRIMARY KEY column");
        }

        $this->constraints["foreign_key"][$column] = $reference;

        return $this;
    }

    /**
     * CREATE TABLE: Set Primary Key
     * 
     * @param string $column
     * @return Query
     * @throws Exception
     */
    public function PrimaryKey(string $column): Query {
        if (!key_exists($column, $this->col_spec)) {
            throw new Exception("QUERY: invalid PRIMARY KEY column");
        }

        $this->constraints["primary_key"] = $column;

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
     * 
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
     * WHERE statement
     *
     * @param string $column
     * @param Operator $op
     * @param mixed $value
     * @return Query
     * @throws Exception
     */
    public function Where(string $column, Operator $op, mixed $value): Query {
        if (!empty($this->where)) {
            throw new Exception("QUERY: 'Where' method can be invoked only once");
        }

        $this->where[] = [
            "column" => $column,
            "operator" => $op,
            "value" => $value
        ];

        return $this;
    }

    /**
     * AND statement, used after WHERE
     *
     * @param string $column
     * @param Operator $op
     * @param mixed $value
     * @return Query
     */
    public function And(string $column, Operator $op, mixed $value): Query {
        if (empty($this->where)) {
            throw new Exception("QUERY: 'And' method must be invoked after 'Where'");
        }

        $this->where[] = [
            "before" => "AND",
            "column" => $column,
            "operator" => $op,
            "value" => $value
        ];

        return $this;
    }

    /**
     * OR statement, used after WHERE
     *
     * @param string $column
     * @param Operator $op
     * @param mixed $value
     * @return Query
     */
    public function Or(string $column, Operator $op, mixed $value): Query {
        if (empty($this->where)) {
            throw new Exception("QUERY: 'Or' method must be invoked after 'Where'");
        }

        $this->where[] = [
            "before" => "OR",
            "column" => $column,
            "operator" => $op,
            "value" => $value
        ];

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
        $this->val_count += count($values);

        foreach ($values as $value) {
            $this->params[] = $value;
        }

        return $this;
    }

    /**
     * Helper function to check the number of specified columns and values.
     * Must be equal and non-zero
     *
     * @return void
     * @throws Exception
     */
    private function Check_ColVal(): void {
        if (count($this->columns) != $this->val_count || !$this->val_count) {
            throw new Exception("QUERY: column(s) and value(s) count mismatch or zero");
        }
    }

    /**
     * Make WHERE statement
     * 
     * @return string
     */
    private function Make_Where(): string {
        if (empty($this->where)) {
            return "";
        }

        $output = " WHERE ";

        foreach ($this->where as $id => $statement) {
            if (isset($statement["before"])) {
                $output .= $statement["before"] . " ";
            }

            $output .= "`{$statement["column"]}` ";
            $output .= ($statement["operator"])->value;
            $output .= " :w{$id} ";
        }

        return substr($output, 0, -1);
    }

    /**
     * Make SQL query for CREATE TABLE operation
     *
     * @return void
     */
    private function Query_Create(): void {
        if (empty($this->col_spec)) {
            throw new Exception("QUERY: empty column(s) specification");
        }
        // SQL: OPERATION HEADER
        $this->sql = "CREATE TABLE";

        if ($this->if_not_exists) {
            $this->sql .= " IF NOT EXISTS";
        }

        $this->sql .= " `{$this->table}` (";
        // SQL: COLUMN SPECIFICATION
        foreach ($this->col_spec as $col => $type) {
            $this->sql .= "`{$col}` {$type}, ";
        }
        // SQL: PRIMARY KEY
        if (isset($this->constraints["primary_key"])) {
            $this->sql .= "PRIMARY KEY (`{$this->constraints["primary_key"]}`), ";
        }
        // SQL: FOREIGN KEYS
        foreach ($this->constraints["foreign_key"] ?? [] as $key => $ref) {
            $this->sql .= "FOREIGN KEY (`{$key}`) REFERENCES `{$ref}`, ";
        }

        $this->sql = substr($this->sql, 0, -2) . ")";
    }

    /**
     * Make SQL query for DELETE operation
     *
     * @return void
     */
    private function Query_Delete(): void {
        // SQL: OPERATION HEADER
        $this->sql = "DELETE FROM `{$this->table}`";
        // SQL: WHERE
        $this->sql .= $this->Make_Where();
    }

    /**
     * Make SQL query for INSERT INTO operation
     *
     * @return void
     */
    private function Query_Insert(): void {
        $this->Check_ColVal();
        // SQL: OPERATION HEADER
        $this->sql = "INSERT INTO `{$this->table}` ('";
        $this->sql .= implode("`, `", $this->columns);
        $this->sql .= "`) VALUES (";

        foreach (array_keys($this->params) as $id) {
            $this->sql .= ":{$id}, ";
        }

        $this->sql = substr($this->sql, 0, -2) . ")";
    }

    /**
     * Make SQL query for UPDATE operation
     *
     * @return void
     */
    private function Query_Update(): void {
        $this->Check_ColVal();
        // SQL: OPERATION HEADER
        $this->sql = "UPDATE `{$this->table}` SET ";

        foreach (array_keys($this->params) as $id) {
            $this->sql .= "`{$this->columns[$id]}` = :{$id}, ";
        }

        $this->sql = substr($this->sql, 0, -2);
        // SQL: WHERE
        $this->sql .= $this->Make_Where();
    }

    /**
     * Make SQL query for SELECT operation
     * @return void
     */
    private function Query_Select(): void {
        // SQL: OPERATION HEADER
        $this->sql = "SELECT " . (($this->distinct) ? "DISTINCT " : "");

        if (empty($this->columns)) {
            $this->sql .= "*";
        } else {
            $this->sql .= "`" . implode("`, `", $this->columns) . "`";
        }

        $this->sql .= " FROM `{$this->table}`";
        // SQL: WHERE
        $this->sql .= $this->Make_Where();
        // SQL: ORDER BY
        if (!empty($this->orders)) {
            $this->sql .= " ORDER BY";

            foreach ($this->orders as $order) {
                $this->sql .= " `{$order[0]}` {$order[1]},";
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
     * Return generated query
     *
     * @return string
     * @throws Exception
     */
    private function Make_Query(): string {
        // DETERMINE OPERATION
        switch ($this->operation) {
            case Statement::CREATE:
                $this->Query_Create();
                break;
            case Statement::DELETE:
                $this->Query_Delete();
                break;
            case Statement::INSERT:
                $this->Query_Insert();
                break;
            case Statement::UPDATE:
                $this->Query_Update();
                break;
            case Statement::SELECT:
                $this->Query_Select();
                break;
        }

        return $this->sql;
    }

    public function __toString(): string {
        return $this->Make_Query();
    }
}
