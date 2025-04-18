<?php

namespace Framework\Interfaces;

use Framework\Query;

interface Database {

    public function __construct(string $conn_name = "default");

    public function Query(Query $query): \PDOStatement;
}
