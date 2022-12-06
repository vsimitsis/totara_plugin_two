<?php

namespace ThirstPlugin\Traits;

trait AlertRenderer
{
    /** @var array The errors array */
    protected $errors = [];

    public function addError(string $errorMsg)
    {
        $this->errors[] = $errorMsg;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Render the errors
     *
     * @return bool|string
     * @throws \coding_exception
     */
    public function renderErrors()
    {
        $html = '';

        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $html .= \html_writer::tag('div', '<h4>' . get_string('error_box_header', 'local_thirst') . '</h4>' . $error, ['class' => 'bs-callout bs-callout-danger']);
            }
        }

        return $html;
    }

    /**
     * Render the success message
     *
     * @param $message
     * @return string
     * @throws \coding_exception
     */
    public static function renderSuccessAlert($message)
    {
        return \html_writer::tag('div', '<h4>' . get_string('changes_applied_msg', 'local_thirst') . '</h4>' . $message, ['class' => 'bs-callout bs-callout-success']);
    }

}