<?php
/**
 * Thirst plugin Auth class.
 *
 * @package    local_thirst
 * @copyright  2019 BuildEmpire Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace ThirstPlugin;

use moodle_url;
use ThirstPlugin\Traits\AlertRenderer;
use ThirstPlugin\Traits\UsesUser;
use ThirstPlugin\ThirstApi;
use ThirstPlugin\Model\AccessToken;

class Auth
{
    use AlertRenderer;
    use UsesUser;

    /** @var AccessToken $accessToken User's access token */
    protected $accessToken = null;

    /** @var mixed[] $data The form data user filled */
    protected $data;

    /**
     * Authorizes the data passed
     *
     * @param \stdClass $credentials
     * @return boolean
     */
    public function authorize(\stdCLass $credentials): bool
    {
        // Validate credentials
        if ($this->validate($credentials)) {
            // Get new access token
            try {
                ThirstApi::setOrganisation($credentials->organisation);
            } catch (\InvalidArgumentException $e) {
                $this->addError($e->getMessage());
                return false;
            }

            $this->accessToken = AccessToken::request($credentials);
            if (!$this->accessToken) {
                $this->addError(get_string('unable_to_request_token', 'local_thirst'));
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Return true if currently logged in user has access token
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function hasValidAccessToken()
    {
        return AccessToken::confirm();
    }

    /**
     * Revoke the key access and delete it from database
     *
     * @return boolean
     * @throws \Exception
     * @todo This may need some checks if the user has been logged out from thirst (if this is even necessary?)
     */
    public function logout(): bool
    {
        ThirstApi::request('GET', 'api/auth/logout');
        AccessToken::getForCurrentUser()->delete();
        return true;
    }

    /**
     * Constructs and returns a moodle url to thirst plugin login
     * @return moodle_url
     * @throws \moodle_exception
     */
    public static function thirstLoginLink(): \moodle_url
    {
        return new moodle_url('/local/thirst/auth/login.php');
    }

    /**
     * Validate the form data user filled
     *
     * @param $data
     * @return bool
     * @throws \coding_exception
     */
    protected function validate(\stdClass $data): bool
    {
        $requiredFields = ['organisation', 'username', 'password'];

        // Iterate over required fields and report any missing ones
        foreach ($requiredFields as $fieldName) {
            if (!isset($data->$fieldName)) {
                // Add error message and return false
                $this->addError(get_string('field_is_required', 'local_thirst', ['field' => $fieldName]));
            }
        }

        // Confirm that all required fields have been passed
        return !$this->hasErrors();
    }

}
