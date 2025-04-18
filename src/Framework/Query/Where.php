<?php

/*
 * CWF-PHP Framework
 *
 * File: Framework\Query\Where.php
 * Description: Query - where statement methods
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

use Framework\Query;

trait Where {

    /**
     * WHERE statement
     *
     * @param string $column
     * @param Operator $op
     * @param mixed $value
     * @return Query
     * @throws Exception
     */
    #[\Override]
    public function Where(string $column, Operator $op, mixed $value): Query {
        if (!empty($this->where)) {
            throw new \Exception("QUERY: 'Where' method can be invoked only once");
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
    #[\Override]
    public function And(string $column, Operator $op, mixed $value): Query {
        if (empty($this->where)) {
            throw new \Exception("QUERY: 'And' method must be invoked after 'Where'");
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
    #[\Override]
    public function Or(string $column, Operator $op, mixed $value): Query {
        if (empty($this->where)) {
            throw new \Exception("QUERY: 'Or' method must be invoked after 'Where'");
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

            $output .= $this->Format($statement["column"]) . " ";
            $output .= ($statement["operator"])->value;
            $output .= " :w{$id} ";
        }

        return \substr($output, 0, -1);
    }
}
