<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Database.php
 * Description: Database interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Mhlbocian\CwfPhp\Interfaces;

use Mhlbocian\CwfPhp\Query;

interface Database {

    public function __construct(string $conn_name = "default");

    public function Query(Query $query): \PDOStatement;
}
