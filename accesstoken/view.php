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
use ThirstPlugin\Tables\AccessTokenDetailsTable;
use ThirstPlugin\Tables\AccessTokenProjectsTable;

$title = get_string('access_token_details', 'local_thirst');
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/thirst/accesstoken/view.php');
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

// Check if the refresh token request has been sent
$refresh = optional_param('refresh', false, PARAM_BOOL);
if ($refresh) {
    $accessToken->refresh();
    redirect(new \moodle_url('/local/thirst/accesstoken/view.php?id='.$accessToken->id));
}

// Render Page
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2, 'display-inline-block');

echo '<div class="btn-group" role="group">';
echo \html_writer::tag('a', get_string('go_back_to_configuration', 'local_thirst'), ['href' => '/local/thirst/configure', 'class' => 'btn btn-primary']);
// Add refresh parameter to the view page
echo \html_writer::tag('a', get_string('refresh_token', 'local_thirst'), ['href' => $GLOBALS['ME'] . '&refresh=true', 'class' => 'btn btn-primary']);
// Add delete token button
echo \html_writer::tag('a', get_string('delete', 'local_thirst'), [
    'href'      => $accessToken->deleteUrl(),
    'class'     => 'btn btn-primary',
    'onclick'   => 'return confirm("Are you sure you want to delete this token?")'
]);
echo '</div>';

// Show access token details
(new AccessTokenDetailsTable())
    ->setAccessToken($accessToken)
    ->setAddedBy($accessToken->getAddedByUser())
    ->output();

// Token has been confirmed. Get all projects
$projects = $accessToken->getProjects();
if (!$projects) {
    echo html_writer::tag('h4', get_string('no_projects_found', 'local_thirst'));
} else {
    echo html_writer::tag('h4', get_string('available_projects', 'local_thirst'));
    (new AccessTokenProjectsTable())
        ->setProjects($accessToken->getProjects())
        ->output();
}

// Render footer
echo $OUTPUT->footer();
