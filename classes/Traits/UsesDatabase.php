<?php

namespace ThirstPlugin\Traits;

use moodle_database;

trait UsesDatabase
{
    /**
     * Get the database instance from global
     *
     * @return moodle_database
     */
    public static function db(): moodle_database
    {
        return $GLOBALS['DB'];
    }
}