<?php

/*
 * CWF-PHP Framework
 *
 * File: Query\Statement.php
 * Description: Query - statements
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Query;

enum Statement {

    case CREATE;
    case DELETE;
    case INSERT;
    case UPDATE;
    case SELECT;
}
