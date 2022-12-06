<?php
/**
 * Show thirst plugin login.
 *
 * @package    local_thirst
 * @copyright  2019 BuildEmpire Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use ThirstPlugin\Model\AccessToken;
use ThirstPlugin\Model\User;
use ThirstPlugin\Forms\LoginForm;
use ThirstPlugin\Auth;

// Bootstrap loader for plugin
require_once __DIR__ . '/../bootstrap.php';

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/thirst/configure/index.php');
$PAGE->set_title(get_string('configure_thirst', 'local_thirst'));
$PAGE->requires->css('/local/thirst/assets/css/bscallouts.css');

// Make sure user is logged in
require_login();

// Render configuration form
$loginForm      = new LoginForm;
$auth           = new Auth;
$apiError      = null;
if ($loginForm->is_cancelled()) {
    // Redirect back to the homepage
    redirect(new moodle_url('/'));
} else if ($loginForm->is_submitted() && $formData = $loginForm->get_data()) {
    // Form submitted - authorize
    try {
        if ($auth->authorize($formData)) {
            // Form data has been authorized - redirect to project index page
            redirect(new moodle_url('/local/thirst/configure/index.php'));
        }
    } catch (\Exception $e) {
        $apiError = \html_writer::tag('div', '<h4>' . get_string('error_box_header', 'local_thirst') . '</h4>' . $e->getMessage(), ['class' => 'bs-callout bs-callout-danger']);
    }
}

// Render Page
echo $OUTPUT->header();
echo html_writer::tag('h2', get_string('configure_thirst', 'local_thirst'));

if ($apiError) {
    echo $apiError;
}
echo $auth->renderErrors();

// Check if any tokens are already configured
$accessTokens = AccessToken::all();
if (empty($accessTokens)) {
    echo get_string('no_tokens_configured', 'local_thirst');
} else {
    // Render all available tokens
    // Define the headerData
    $columns = $headers = [];
    foreach (['added_by', 'organisation_name', 'organisation_subdomain', 'expires_at', 'actions'] as $key => $head) {
        $columns[] = 'extracolumn' . $key;
        $headers[] = get_string($head, 'local_thirst');
    }

    $table = new flexible_table('content-pages');
    $table->define_headers($headers);
    $table->define_columns($columns);
    $table->define_baseurl('/');
    $table->setup();

    // Add accessToken data
    foreach ($accessTokens as $accessToken) {
        $table->add_data([
            User::find($accessToken->added_by)->email,
            $accessToken->organisation_name,
            $accessToken->organisation_subdomain,
            (new \DateTime)->setTimestamp($accessToken->expires_at)->format('d F Y, h:i:s A'),
            (
                html_writer::tag('a', get_string('details', 'local_thirst'), ['href' => $accessToken->url()])
            )
        ]);
    }

    $table->finish_output();

    echo html_writer::tag('h3', get_string('add_token', 'local_thirst'));

}

$loginForm->display();
echo $OUTPUT->footer();