<?php

namespace Framework\Auth;

trait Group {

    /**
     * Create new group
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    public static function GroupAdd(
            string $groupname,
            string $description): Status {

        if (self::GroupExists($groupname)) {

            return Status::EXISTS;
        }

        // TODO: Check input correctness via regex (Auth\Status::INVALID_INPUT)

        return self::CallDriver("GroupAdd", $groupname, $description);
    }

    /**
     * Change description for group
     * 
     * @param string $groupname
     * @param string $description
     * @return Status
     */
    public static function GroupChDesc(
            string $groupname,
            string $description): Status {

        if (!self::GroupExists($groupname)) {

            return Status::INVALID_INPUT;
        }

        return self::CallDriver("GroupChDesc", $groupname, $description);
    }

    /**
     * Delete group
     * 
     * @param string $groupname
     * @return Status
     */
    public static function GroupDel(string $groupname): Status {
        if (!self::GroupExists($groupname)) {

            return Status::INVALID_INPUT;
        }

        return self::CallDriver("GroupDel", $groupname);
    }

    /**
     * Check, if group exists for given name
     * 
     * @param string $groupname
     * @return bool
     */
    public static function GroupExists(string $groupname): bool {

        return self::CallDriver("GroupExists", $groupname);
    }

    /**
     * Return an array of groups
     * 
     * @return array ["groupname1"=>"description1", ...]
     */
    public static function GroupFetch(): array {

        return self::CallDriver("GroupFetch");
    }
}
