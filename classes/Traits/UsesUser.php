<?php

namespace ThirstPlugin\Traits;

trait UsesUser
{
    /**
     * Gets the user from global
     *
     * @return stdClass
     */
    public static function user(): \stdClass
    {
        return $GLOBALS['USER'];
    }
}