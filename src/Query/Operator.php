<?php

/*
 * CWF-PHP Framework
 *
 * File: Query\Operator.php
 * Description: Query - operators
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Query;

enum Operator: string {

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
