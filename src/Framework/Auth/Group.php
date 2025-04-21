<?php

/*
 * CWF-PHP Framework
 * 
 * File: Framework\Auth\Auth.php
 * Description: Auth API - group methods
 * Author: Michal Bocian <bocian.michal@outlook.com>
 * License: 3-Clause BSD
 */

namespace Framework\Auth;

trait Group {

    /**
     * Create new group
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    #[\Override]
    public static function GroupAdd(
            string $groupname,
            string $description): Status {

        if (self::GroupExists($groupname)) {

            return Status::EXISTS;
        }

        if (!self::CheckFmt($groupname, self::$groupname_fmt) ||
                !self::CheckFmt($description, self::$description_fmt)) {

            return Status::INVALID_INPUT;
        }
        // as description may contain HTML specific characters, filter it
        $description = htmlspecialchars($description);

        return self::CallDriver("GroupAdd", $groupname, $description);
    }

    /**
     * Change description for group
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    #[\Override]
    public static function GroupChDesc(
            string $groupname,
            string $description): Status {

        if (!self::GroupExists($groupname) ||
                !self::CheckFmt($description, self::$description_fmt)) {

            return Status::INVALID_INPUT;
        }
        // as description may contain HTML specific characters, filter it
        $description = htmlspecialchars($description);

        return self::CallDriver("GroupChDesc", $groupname, $description);
    }

    /**
     * Delete group
     * 
     * @param string $groupname
     * @return Status
     */
    #[\Override]
    public static function GroupDel(string $groupname): Status {
        if (!self::GroupExists($groupname)) {

            return Status::NOTEXISTS;
        }

        return self::CallDriver("GroupDel", $groupname);
    }

    /**
     * Check, if group exists for given name
     * 
     * @param string $groupname
     * @return bool
     */
    #[\Override]
    public static function GroupExists(string $groupname): bool {

        return self::CallDriver("GroupExists", $groupname);
    }

    /**
     * Return an array of groups
     * 
     * @return array ["groupname1"=>"description1", ...]
     */
    #[\Override]
    public static function GroupFetch(): array {

        return self::CallDriver("GroupFetch");
    }
}
