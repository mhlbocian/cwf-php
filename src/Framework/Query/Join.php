<?php

/*
 * CWF-PHP Framework
 *
 * File: Framework\Query\Join.php
 * Description: Query - join methods
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

use Framework\Query;

trait Join {

    private bool $join = false;
    private string $join_column1;
    private string $join_column2;
    private string $join_table;
    private string $join_type = "";
    
    #[\Override]
    public function Join(
            string $table,
            string $column1,
            string $column2): Query {

        if ($this->join) {

            throw new \Exception("QUERY: Join can be used only once");
        }

        $this->join = true;
        $this->join_table = $table;
        $this->join_column1 = $column1;
        $this->join_column2 = $column2;

        return $this;
    }
    
    #[\Override]
    public function InnerJoin(
            string $table,
            string $column1,
            string $column2): Query {

        $this->Join($table, $column1, $column2);
        $this->join_type = "INNER";

        return $this;
    }
    
    #[\Override]
    public function LeftJoin(
            string $table,
            string $column1,
            string $column2): Query {

        $this->Join($table, $column1, $column2);
        $this->join_type = "LEFT";

        return $this;
    }
    
    #[\Override]
    public function RightJoin(
            string $table,
            string $column1,
            string $column2): Query {

        $this->Join($table, $column1, $column2);
        $this->join_type = "RIGHT";

        return $this;
    }

    private function Make_Join(): string {
        if (!$this->join) {

            return "";
        }

        $ret = " {$this->join_type} JOIN " . $this->Format($this->join_table);
        $ret .= " ON " . $this->Format($this->join_column1) . "=";
        $ret .= $this->Format($this->join_column2);

        return $ret;
    }
}
