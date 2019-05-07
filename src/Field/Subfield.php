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
    protected $type;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $id;
    /**
     * @var array
     */
    protected $config;

    /**
     * Subfield constructor.
     *
     * @param string $type one of the `Subfield::TYPE_…` constants
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $id the module field's ID (the full name including the namespace)
     * @param string $name the module field's name to be sent to Craft via form-sbmit
     * @param string $suffix the suffix being added to the module field's name to identify this subfield
     * @param string $key the field name as used in the ModuleField's value object
     * @param array $config the config object to be passed to the Twig macro for rendering this field
     */
    public function __construct(
        string $type,
        string $label,
        string $id,
        string $name,
        string $suffix,
        string $key,
        array $config = []
    ) {
        $defaultConfig = array_filter([
            'label' => $label,
            'id' => $name . $suffix,
            'name' => $name . $suffix,
        ]);

        $this->type = $type;
        $this->key = $key;
        $this->id = $id . $suffix;
        $this->config = array_merge($defaultConfig, $config);
    }

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type.
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Returns the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Sets the key.
     *
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Returns the id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the id.
     *
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
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

    /**
     * Returns an array representation of this sub-field.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'key' => $this->getKey(),
            'id' => $this->getId(),
            'config' => $this->getConfig(),
        ];
    }
}
