<?php

/*
 * CWF-PHP Framework
 *
 * File: Query.php
 * Description: SQL query builder
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp;

use CwfPhp\CwfPhp\Query\Statement;

final class Query implements Interfaces\Query {

    use Query\Constraints,
        Query\Join,
        Query\Misc,
        Query\SQL,
        Query\Where;

    private array $columns = [];
    private array $cols_type = [];
    private array $orders = [];
    private array $params = [];
    private array $where = [];
    private bool $distinct = false;
    private bool $if_not_exists = false;
    private ?int $limit = null;
    private ?int $offset = null;
    private Statement $operation;
    private string $table;
    private string $sql;
    
    #[\Override]
    public function __construct(Statement $operation) {
        $this->operation = $operation;
    }
    
    #[\Override]
    public function Params(): array {
        $params = $this->params;

        /*
         * walk through $where array, and join their values as parameters with
         * prefix 'w' (like :w0, :w1)
         *
         */
        foreach ($this->where as $id => $statement) {
            $params["w{$id}"] = $statement["value"];
        }

        return $params;
    }
    
    #[\Override]
    public function ColType(string $name, string $type): Query {
        $this->cols_type[$name] = $type;

        return $this;
    }
    
    #[\Override]
    public function Columns(string ...$columns): Query {
        $this->columns = $columns;

        return $this;
    }
    
    #[\Override]
    public function Distinct(): Query {
        $this->distinct = true;

        return $this;
    }
    
    #[\Override]
    public function IfNotExists(): Query {
        $this->if_not_exists = true;

        return $this;
    }
    
    #[\Override]
    public function Limit(int $limit): Query {
        $this->limit = $limit;

        return $this;
    }
    
    #[\Override]
    public function Offset(int $offset): Query {
        $this->offset = $offset;

        return $this;
    }
    
    #[\Override]
    public function OrderBy(string $column, bool $asc = true): Query {
        $this->orders[] = [$column, ($asc) ? "ASC" : "DESC"];

        return $this;
    }
    
    #[\Override]
    public function Table(string $table): Query {
        $this->table = $table;

        return $this;
    }
    
    #[\Override]
    public function Values(mixed ...$values): Query {
        foreach ($values as $value) {
            $this->params[] = $value;
        }

        return $this;
    }
    
    #[\Override]
    public function __toString(): string {
        switch ($this->operation) {
            case Statement::CREATE:
                $this->SQL_Create();
                break;
            case Statement::DELETE:
                $this->SQL_Delete();
                break;
            case Statement::INSERT:
                $this->SQL_Insert();
                break;
            case Statement::UPDATE:
                $this->SQL_Update();
                break;
            case Statement::SELECT:
                $this->SQL_Select();
                break;
        }

        return $this->sql;
    }
}
