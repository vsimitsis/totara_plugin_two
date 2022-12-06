<?php

namespace ThirstPlugin\Model;

use ThirstPlugin\Traits\UsesDatabase;
use ThirstPlugin\Traits\UsesUser;
use ThirstPlugin\Traits\IsResource;

class Activity extends BaseModel
{
    use UsesDatabase;
    use UsesUser;
    use IsResource;

    protected static function getTable(): string
    {
        return 'thirst';
    }

    public static function getByAccessTokenId(int $accessTokenId): ?array
    {
        return self::get(['access_token_id' => $accessTokenId]);
    }

}