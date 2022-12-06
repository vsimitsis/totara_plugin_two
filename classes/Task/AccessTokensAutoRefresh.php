<?php

namespace local_thirst\Task;

require_once __DIR__ . '/../../bootstrap.php';

use ThirstPlugin\Model\AccessToken;

class AccessTokensAutoRefresh extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('thirst_access_tokens_auto_refresh', 'local_thirst');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        // Get all tokens
        $accessTokens = AccessToken::all();
        foreach ($accessTokens as $accessToken) {
            if ($accessToken->needsRefreshing()) {
                $accessToken->refresh();
            }
        }
    }
}