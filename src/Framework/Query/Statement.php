<?php

/*
 * CWF-PHP Framework
 *
 * File: Statement.php
 * Description: Query: Statements
 * Author: Michał Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Query;

enum Statement {

    case CREATE;
    case DELETE;
    case INSERT;
    case UPDATE;
    case SELECT;
}
