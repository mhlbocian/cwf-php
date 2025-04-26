<?php

/*
 * CWF-PHP Framework
 * 
 * File: Interfaces\Query.php
 * Description: Query builder interface
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Interfaces;

use CwfPhp\CwfPhp\Query\Operator;
use CwfPhp\CwfPhp\Query\Statement;

interface Query {

    public function __construct(Statement $operation);
    
    public function And(
            string $column,
            Operator $op,
            mixed $value): Query;

    public function ColType(
            string $name,
            string $type): Query;

    public function Columns(string ...$columns): Query;

    public function Distinct(): Query;

    public function ForeginKey(
            string $column,
            string $reference): Query;

    public function IfNotExists(): Query;

    public function InnerJoin(
            string $table,
            string $column1,
            string $column2): Query;

    public function Join(
            string $table,
            string $column1,
            string $column2): Query;

    public function LeftJoin(
            string $table,
            string $column1,
            string $column2): Query;

    public function Limit(int $limit): Query;

    public function Offset(int $offset): Query;
    
    public function Or(
            string $column,
            Operator $op,
            mixed $value): Query;

    public function OrderBy(
            string $column,
            bool $asc = true): Query;

    public function Params(): array;

    public function PrimaryKey(string $column): Query;

    public function RightJoin(
            string $table,
            string $column1,
            string $column2): Query;

    public function Table(string $table): Query;

    public function Unique(string ...$columns): Query;
    
    public function Where(
            string $column,
            Operator $op,
            mixed $value): Query;

    public function Values(mixed ...$values): Query;

    public function __toString(): string;
}
