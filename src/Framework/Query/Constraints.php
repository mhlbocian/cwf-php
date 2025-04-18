<?php

/*
 * CWF-PHP Framework
 *
 * File: Framework\Query\Constraints.php
 * Description: Query - constraints methods
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

use Framework\Query;

trait Constraints {

    /**
     * 
     * @var array Constraints array
     */
    private array $constraints = [];

    /**
     * CREATE TABLE: Set Foreign Key
     * 
     * @param string $column
     * @param string $reference
     * @return Query
     * @throws Exception
     */
    public function ForeginKey(string $column, string $reference): Query {
        if (!\key_exists($column, $this->cols_type)) {
            throw new \Exception("QUERY: unknown FOREIGN KEY column");
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
        if (!\key_exists($column, $this->cols_type)) {
            throw new \Exception("QUERY: invalid PRIMARY KEY column");
        }

        $this->constraints["primary_key"] = $column;

        return $this;
    }

    /**
     * CREATE TABLE: Set Unique columns
     * 
     * @param string $columns
     * @return Query
     */
    public function Unique(string ...$columns): Query {
        $this->constraints["unique"][] = $columns;

        return $this;
    }

    /**
     * Helper function to return constraints string
     * 
     * @return string
     */
    private function Make_Constraints(): string {
        $output = "";
        // SQL: PRIMARY KEY
        if (isset($this->constraints["primary_key"])) {
            $output = "PRIMARY KEY ({$this->constraints["primary_key"]}), ";
        }
        // SQL: FOREIGN KEYS
        foreach ($this->constraints["foreign_key"] ?? [] as $key => $ref) {
            $output .= "FOREIGN KEY ({$key}) REFERENCES {$ref}, ";
        }
        // SQL: UNIQUE
        foreach ($this->constraints["unique"] ?? [] as $no => $columns) {
            if (empty($columns)) {
                continue;
            }

            $output .= "UNIQUE (";

            foreach ($columns as $column) {
                $output .= $this->Format($column) . ", ";
            }

            $output = \substr($output, 0, -2) . "), ";
        }

        return \substr($output, 0, -2);
    }
}
