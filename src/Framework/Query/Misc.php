<?php

/*
 * CWF-PHP Framework
 *
 * File: Framework\Query\Misc.php
 * Description: Query - miscellaneous functions
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

trait Misc {

    /**
     * Helper function to check the number of specified columns and values.
     * Must be equal and non-zero
     *
     * @return void
     * @throws Exception
     */
    private function ColVal_Check(): void {
        if (\count($this->columns) != \count($this->params) || !\count($this->params)) {
            throw new \Exception("QUERY: column(s) and value(s) count mismatch or zero");
        }
    }

    /**
     * Format string (like table or column name). Escape it with ``, when the
     * dot in string is present, surround it with ``
     * 
     * Eg. input: table.column1 output: `table`.`column1`
     * 
     * @param string $column
     * @return string
     */
    private function Format(string $column): string {

        return "`" . \str_replace(".", "`.`", $column) . "`";
    }
}
