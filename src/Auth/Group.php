<?php

/*
 * CWF-PHP Framework
 * 
 * File: Auth\Group.php
 * Description: Auth API - group methods
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace CwfPhp\CwfPhp\Auth;

use CwfPhp\CwfPhp\Auth\Status;

trait Group {
    
    #[\Override]
    public function GroupAdd(
            string $groupname,
            string $description): Status {

        if (!$this->configured) {
            return Status::FAILED;
        }

        if ($this->GroupExists($groupname)) {

            return Status::EXISTS;
        }

        if (!$this->Format_Check($groupname, $this->groupname_fmt) ||
                !$this->Format_Check($description, $this->description_fmt)) {

            return Status::INVALID_INPUT;
        }
        // as description may contain HTML specific characters, filter it
        $filter_desc = \htmlspecialchars($description);

        return $this->driver->GroupAdd($groupname, $filter_desc);
    }
    
    #[\Override]
    public function GroupChDesc(
            string $groupname,
            string $description): Status {

        if (!$this->configured) {
            return Status::FAILED;
        }

        if (!$this->GroupExists($groupname) ||
                !$this->Format_Check($description, $this->description_fmt)) {

            return Status::INVALID_INPUT;
        }
        // as description may contain HTML specific characters, filter it
        $filter_desc = \htmlspecialchars($description);

        return $this->driver->GroupChDesc($groupname, $filter_desc);
    }
    
    #[\Override]
    public function GroupDel(string $groupname): Status {
        if (!$this->configured) {
            return Status::FAILED;
        }

        if (!$this->GroupExists($groupname)) {

            return Status::NOTEXISTS;
        }

        return $this->driver->GroupDel($groupname);
    }
    
    #[\Override]
    public function GroupExists(string $groupname): bool {
        if (!$this->configured) {
            return false;
        }

        return $this->driver->GroupExists($groupname);
    }
    
    #[\Override]
    public function GroupFetch(): array {
        if (!$this->configured) {
            return [];
        }

        return $this->driver->GroupFetch();
    }
}
