<?php

namespace Vierbeuter\Craft\Field;

/**
 * A Subfield defines a field type and its configuration to render a form field with.
 *
 * See available macros in Craft's forms template (./vendor/craftcms/cms/src/templates/_includes/forms.html).
 *
 * @package Vierbeuter\Craft\Field
 */
class Subfield
{

    const TYPE_AUTOSUGGEST = 'autosuggestField';
    const TYPE_CHECKBOX = 'checkboxField';
    const TYPE_CHECKBOXGROUP = 'checkboxGroupField';
    const TYPE_CHECKBOXSELECT = 'checkboxSelectField';
    const TYPE_COLOR = 'colorField';
    const TYPE_DATE = 'dateField';
    const TYPE_DATETIME = 'dateTimeField';
    const TYPE_EDITABLETABLE = 'editableTableField';
    const TYPE_ELEMENTSELECT = 'elementSelectField';
    const TYPE_FILE = 'fileField';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_LIGHTSWITCH = 'lightswitchField';
    const TYPE_MULTISELECT = 'multiselectField';
    const TYPE_PASSWORD = 'passwordField';
    const TYPE_RADIOGROUP = 'radioGroupField';
    const TYPE_SELECT = 'selectField';
    const TYPE_TEXTAREA = 'textareaField';
    const TYPE_TEXT = 'textField';
    const TYPE_TIME = 'timeField';

    /**
     * @var string
     */
    protected $field;
    /**
     * @var array
     */
    protected $config;

    /**
     * Subfield constructor.
     *
     * @param string $field
     * @param array $config
     */
    public function __construct(string $field, array $config = [])
    {
        $this->field = $field;
        $this->config = $config;
    }

    /**
     * Returns the field.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Sets the field.
     *
     * @param string $field
     */
    public function setField(string $field)
    {
        $this->field = $field;
    }

    /**
     * Returns the config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Sets the config.
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}
