<?php

/*
 * CWF-PHP Framework
 *
 * File: Constraints.php
 * Description: Query: Functions for constraints
 * Author: Michał Bocian <bocian.michal@outlook.com>
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
        if (!key_exists($column, $this->cols_type)) {
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
        if (!key_exists($column, $this->cols_type)) {
            throw new Exception("QUERY: invalid PRIMARY KEY column");
        }

        $this->constraints["primary_key"] = $column;

        return $this;
    }
}
