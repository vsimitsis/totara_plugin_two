<?php
/**
 * Plugin version information
 *
 * @package    local_thirst
 * @copyright  2019 BuildEmpire Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once __DIR__ . '/vendor/autoload.php';

$plugin->version   = 2019120201; // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2016112900; // Requires this Moodle version
$plugin->component = 'local_thirst'; // Full name of the plugin (used for diagnostics)
$plugin->cron      = 0;
