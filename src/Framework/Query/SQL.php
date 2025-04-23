<?php

/*
 * CWF-PHP Framework
 *
 * File: Framework\Query\SQL.php
 * Description: Query - SQL query generators
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

trait SQL {
    
    private function SQL_Create(): void {
        if (empty($this->cols_type)) {
            throw new \Exception("QUERY: empty column(s) type specification");
        }
        // SQL: OPERATION HEADER
        $this->sql = "CREATE TABLE ";

        if ($this->if_not_exists) {
            $this->sql .= "IF NOT EXISTS ";
        }

        $this->sql .= $this->Format($this->table) . " (";
        // SQL: COLUMN SPECIFICATION
        foreach ($this->cols_type as $col => $type) {
            $this->sql .= $this->Format($col) . " {$type}, ";
        }

        if (!empty($this->constraints)) {
            $this->sql .= $this->Make_Constraints();
        } else {
            $this->sql = \substr($this->sql, 0, -2);
        }

        $this->sql .= ")";
    }
    
    private function SQL_Delete(): void {
        // SQL: OPERATION HEADER
        $this->sql = "DELETE FROM " . $this->Format($this->table);
        // SQL: WHERE
        $this->sql .= $this->Make_Where();
    }
    
    private function SQL_Insert(): void {
        $this->ColVal_Check();
        // SQL: OPERATION HEADER
        $this->sql = "INSERT INTO " . $this->Format($this->table) . " (";

        foreach ($this->columns as $column) {
            $this->sql .= $this->Format($column) . ", ";
        }

        $this->sql = \substr($this->sql, 0, -2);
        $this->sql .= ") VALUES (";

        foreach (\array_keys($this->params) as $id) {
            $this->sql .= ":{$id}, ";
        }

        $this->sql = \substr($this->sql, 0, -2) . ")";
    }
    
    private function SQL_Update(): void {
        $this->ColVal_Check();
        // SQL: OPERATION HEADER
        $this->sql = "UPDATE " . $this->Format($this->table) . " SET ";

        foreach (\array_keys($this->params) as $id) {
            $this->sql .= $this->Format($this->columns[$id]) . " = :{$id}, ";
        }

        $this->sql = \substr($this->sql, 0, -2);
        // SQL: WHERE
        $this->sql .= $this->Make_Where();
    }
    
    private function SQL_Select(): void {
        // SQL: OPERATION HEADER
        $this->sql = "SELECT " . (($this->distinct) ? "DISTINCT " : "");

        if (empty($this->columns)) {
            $this->sql .= "*";
        } else {
            foreach ($this->columns as $column) {
                $this->sql .= $this->Format($column) . ", ";
            }

            $this->sql = \substr($this->sql, 0, -2);
        }

        $this->sql .= " FROM " . $this->Format($this->table);
        // SQL: JOIN
        $this->sql .= $this->Make_Join();
        // SQL: WHERE
        $this->sql .= $this->Make_Where();
        // SQL: ORDER BY
        if (!empty($this->orders)) {
            $this->sql .= " ORDER BY ";

            foreach ($this->orders as $order) {
                $this->sql .= $this->Format($order[0]) . " {$order[1]}, ";
            }

            $this->sql = \substr($this->sql, 0, -2);
        }
        // SQL: LIMIT
        if (!\is_null($this->limit)) {
            $this->sql .= " LIMIT {$this->limit}";
        }
        // SQL: OFFSET
        if (!\is_null($this->offset)) {
            $this->sql .= " OFFSET {$this->offset}";
        }
    }
}
