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
    protected $id;
    /**
     * @var string
     */
    private $label;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    private $suffix;
    /**
     * @var array
     */
    protected $config;

    /**
     * Subfield constructor.
     *
     * @param string $type one of the `Subfield::TYPE_…` constants
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     */
    public function __construct(string $type, string $label, string $key, array $config = [])
    {
        $this->type = $type;
        $this->label = $label;
        $this->key = $key;
        $this->suffix = strtoupper($key[0]) . substr($key, 1);
        $this->config = $config;
    }

    /**
     * Initializes the sub-field's config with given default config and module field's value. Returns the resulting
     * config array.
     *
     * @param array $config the config object to be passed to the Twig macro for rendering this field
     * @param \stdClass|null $value the module field's value, you can access the sub-field's value by calling
     *     `$value->{$this->key}`
     *
     * @return array
     */
    public function initConfig(array $config, \stdClass $value = null): array
    {
        return $config;
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
     * Returns the label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the label.
     *
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
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
     * Sets the id (will be suffixed automatically).
     *
     * @param string $id the module field's ID (the full name including the namespace)
     */
    public function setId(string $id)
    {
        $this->id = $id . $this->suffix;
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
     * Sets the field name by updating the config arrray (name will be suffixed automatically).
     *
     * @param string $name the module field's name to be sent to Craft via form-sbmit
     */
    public function setName(string $name)
    {
        $this->config['id'] = $name . $this->suffix;
        $this->config['name'] = $name . $this->suffix;
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
