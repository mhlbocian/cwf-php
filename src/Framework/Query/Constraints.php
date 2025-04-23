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
    
    private array $constraints = [];
    
    #[\Override]
    public function ForeginKey(string $column, string $reference): Query {
        if (!\key_exists($column, $this->cols_type)) {
            throw new \Exception("QUERY: unknown FOREIGN KEY column");
        }

        $this->constraints["foreign_key"][$column] = $reference;

        return $this;
    }
    
    #[\Override]
    public function PrimaryKey(string $column): Query {
        if (!\key_exists($column, $this->cols_type)) {
            throw new \Exception("QUERY: invalid PRIMARY KEY column");
        }

        $this->constraints["primary_key"] = $column;

        return $this;
    }
    
    #[\Override]
    public function Unique(string ...$columns): Query {
        $this->constraints["unique"][] = $columns;

        return $this;
    }
    
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
