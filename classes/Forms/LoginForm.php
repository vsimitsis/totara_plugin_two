<?php
/**
 * Definition of account settings moodle form for thirst plugin.
 *
 * @package    local_thirst
 * @copyright  2019 BuildEmpire Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace ThirstPlugin\Forms;

class LoginForm extends BaseForm
{
    /**
     * Defines the structure of the settings form
     *
     * @throws \coding_exception
     */
    public function definition()
    {
        $mForm = $this->_form;

        $mForm->addElement('text', 'organisation', get_string('organisation_subdomain', 'local_thirst'), ['class' => 'tags-input-custom']);
        $mForm->setType('organisation', PARAM_NOTAGS);
        $mForm->addHelpButton('organisation', 'organisation_subdomain', 'local_thirst');
        $mForm->addRule('organisation', get_string('organisation_required', 'local_thirst'), 'required', null, 'client');

        $mForm->addElement('text', 'username', get_string('email_address', 'local_thirst'), ['class' => 'tags-input-custom']);
        $mForm->setType('username', PARAM_EMAIL);
        $mForm->addRule('username', get_string('email_required', 'local_thirst'), 'required', null, 'client');

        $mForm->addElement('password', 'password', get_string('password', 'local_thirst'), ['class' => 'tags-input-custom']);
        $mForm->setType('password', PARAM_RAW );
        $mForm->addRule('password', get_string('password_required', 'local_thirst'), 'required', null, 'client');

        $this->add_action_buttons();
    }
}
