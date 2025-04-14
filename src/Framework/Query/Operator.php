<?php

/*
 * CWF-PHP Framework
 *
 * File: Operator.php
 * Description: Query: Operators
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

enum Operator: string {

    // define operators and its symbols
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
