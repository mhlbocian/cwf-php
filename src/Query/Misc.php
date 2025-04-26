<?php

/*
 * CWF-PHP Framework
 *
 * File: Query\Misc.php
 * Description: Query - miscellaneous functions
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Query;

trait Misc {
    
    private function ColVal_Check(): void {
        if (\count($this->columns) != \count($this->params) || !\count($this->params)) {
            throw new \Exception("QUERY: column(s) and value(s) count mismatch or zero");
        }
    }
    
    private function Format(string $column): string {

        return "`" . \str_replace(".", "`.`", $column) . "`";
    }
}
