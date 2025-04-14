<?php

/*
 * CWF-PHP Framework
 *
 * File: SQL.php
 * Description: Query: Functions for generating queries
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

trait SQL {

    /**
     * Helper function to check the number of specified columns and values.
     * Must be equal and non-zero
     *
     * @return void
     * @throws Exception
     */
    private function Colval_Check(): void {
        if (count($this->columns) != count($this->params) || !count($this->params)) {
            throw new Exception("QUERY: column(s) and value(s) count mismatch or zero");
        }
    }

    /**
     * Make SQL query for CREATE TABLE operation
     *
     * @return void
     */
    private function Query_Create(): void {
        if (empty($this->cols_type)) {
            throw new Exception("QUERY: empty column(s) specification");
        }
        // SQL: OPERATION HEADER
        $this->sql = "CREATE TABLE";

        if ($this->if_not_exists) {
            $this->sql .= " IF NOT EXISTS";
        }

        $this->sql .= " `{$this->table}` (";
        // SQL: COLUMN SPECIFICATION
        foreach ($this->cols_type as $col => $type) {
            $this->sql .= "`{$col}` {$type}, ";
        }
        // SQL: PRIMARY KEY
        if (isset($this->constraints["primary_key"])) {
            $this->sql .= "PRIMARY KEY ({$this->constraints["primary_key"]}), ";
        }
        // SQL: FOREIGN KEYS
        foreach ($this->constraints["foreign_key"] ?? [] as $key => $ref) {
            $this->sql .= "FOREIGN KEY ({$key}) REFERENCES {$ref}, ";
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
        $this->Colval_Check();
        // SQL: OPERATION HEADER
        $this->sql = "INSERT INTO `{$this->table}` (`";
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
        $this->Colval_Check();
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
}
