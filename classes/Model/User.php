<?php

namespace ThirstPlugin\Model;

use ThirstPlugin\Traits\IsResource;
use ThirstPlugin\Traits\UsesDatabase;
use ThirstPlugin\Traits\UsesUser;

class User extends BaseModel
{
    use UsesDatabase;
    use UsesUser;
    use IsResource;

    /**
     * Get the table name of where the resource is stored
     *
     * @return string
     */
    protected static function getTable(): string
    {
        return 'user';
    }

}