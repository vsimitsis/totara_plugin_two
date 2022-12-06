<?php
/**
 * Show projects page.
 *
 * @package    local_thirst
 * @copyright  2019 BuildEmpire Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once __DIR__ . '/../bootstrap.php';

use ThirstPlugin\Auth;
use ThirstPlugin\Model\AccessToken;
use ThirstPlugin\Model\Activity;
use ThirstPlugin\Helpers\AlertRenderer;

$title = get_string('access_token_delete', 'local_thirst');
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/thirst/accesstoken/delete.php');
$PAGE->requires->css('/local/thirst/assets/css/bscallouts.css');
$PAGE->set_title($title);

require_login();

if (!is_siteadmin()) {
    redirect(Auth::thirstLoginLink());
}

// Confirm access token
$accessToken = AccessToken::find(required_param('id', PARAM_INT));
if (!$accessToken) {
    throw new \moodle_exception(get_string('access_token_not_found', 'local_thirst'));
}


// Check if there are any activities assigned to it
$activities = null;
$activitiesTableExist = Activity::tableExists();
if ($activitiesTableExist) {
    $activities = $accessToken->getActivities();
}

if (!$activities || !$activitiesTableExist) {
    // Perform token deletion
    $accessToken->delete();
    redirect(new \moodle_url('/local/thirst/configure/index.php'));
}

// Render Page
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2, 'display-inline-block');
echo \html_writer::tag('a', get_string('go_back_to_configuration', 'local_thirst'), ['href' => '/local/thirst/configure']);
$alertRenderer = new AlertRenderer;
$alertRenderer->addError(sprintf('Unable to delete access tokens as it is linked to %d %s.', count($activities), count($activities) == 1 ? 'activity' : 'activities' ));
echo $alertRenderer->renderErrors();
// Render footer
echo $OUTPUT->footer();

