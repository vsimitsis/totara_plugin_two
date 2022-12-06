<?php

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'local_thirst\Task\AccessTokensAutoRefresh',
        'blocking'  => 0,
        'minute'    => '0',
        'hour'      => '0',
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*',
    ]
];