<?php

defined('MOODLE_INTERNAL') || die;

require_once $CFG->dirroot . '/lib/navigationlib.php';

/**
 * Extends the Thirst root menu
 *
 * @param settings_navigation $nav
 * @throws coding_exception
 * @throws moodle_exception
 */
function local_thirst_extend_settings_navigation(settings_navigation $nav)
{
    // Find plugin settings
    $pluginsNode = $nav->find('modules', navigation_node::TYPE_SETTING);
    if ($pluginsNode) {
        $pluginsNode->add(
            get_string('configure_thirst', 'local_thirst'),
            new moodle_url('/local/thirst/configure/index.php'),
            navigation_node::TYPE_SETTING,
            null,
            'local_thirst_configuration'
        );
    }
}