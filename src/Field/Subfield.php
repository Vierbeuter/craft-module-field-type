<?php

namespace Vierbeuter\Craft\Field;

use craft\base\ElementInterface;
use craft\helpers\StringHelper;

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
    const TYPE_CHECKBOXSELECT = 'checkboxSelectField';
    const TYPE_COLOR = 'colorField';
    const TYPE_DATE = 'dateField';
    const TYPE_DATETIME = 'dateTimeField';
    const TYPE_EDITABLETABLE = 'editableTableField';
    const TYPE_ELEMENTSELECT = 'elementSelectField';
    const TYPE_GROUP = 'groupField';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_LIGHTSWITCH = 'lightswitchField';
    const TYPE_MULTIPLY = 'multiplyField';
    const TYPE_MULTISELECT = 'multiselectField';
    const TYPE_PASSWORD = 'passwordField';
    const TYPE_RADIOGROUP = 'radioGroupField';
    const TYPE_REDACTOR = 'redactorField';
    const TYPE_SELECT = 'selectField';
    const TYPE_TEXT = 'textField';
    const TYPE_TEXTAREA = 'textareaField';
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
    protected $key;
    /**
     * @var string
     */
    protected $suffix;
    /**
     * @var array
     */
    protected $config;
    /**
     * @var array
     */
    protected $rules;

    /**
     * Subfield constructor.
     *
     * @param string $type one of the `Subfield::TYPE_…` constants
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $config custom config array being passed down to the subfield's Twig template
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $type, string $label, string $key, array $config = [], array $rules = [])
    {
        $this->type = $type;
        $this->key = StringHelper::camelCase($key);
        $this->suffix = StringHelper::toPascalCase($this->key);
        $this->config = array_merge(array_filter([
            'label' => $label,
        ]), $config);
        $this->rules = array_merge(array_filter([
            !empty($config['required']) ? 'required' : null,
        ]), $rules);
    }

    /**
     * Initializes the subfield with given module field data.
     *
     * Sets suffixed name and ID, for instance.
     *
     * @param string $name the module field's handle
     * @param \craft\base\ElementInterface $element the element the module field is associated with, if there is one
     * @param string $namespacedId the module field's input ID
     * @param \stdClass|null $value the module field's value, you can access the sub-field's value by calling
     *     `$value->{$this->key}`
     */
    public function init(string $name, ElementInterface $element, string $namespacedId, \stdClass $value = null)
    {
        //  init some basic data that is needed before finish configuration
        //  (because `$this->configure()` may rely on these fields)
        $id = empty($element->getId()) ? $namespacedId : str_replace('__BLOCK__', $element->getId(), $namespacedId);
        $this->id = $id . $this->suffix;
        $this->config['id'] = $name . $this->suffix;
        $this->config['name'] = $name . $this->suffix;

        //  finish configuration (that'll be later passed to the field's Twig macro) by overriding current config with
        //  subclass-specific customizations (depends on implementation of `$this->configure()` and given field value)
        $this->config = $this->configure($this->getConfig(), $value);
    }

    /**
     * Configures the sub-field with given default config and the module field's value. Returns the resulting config
     * array.
     *
     * Method can be overridden.
     *
     * @param array $config the config object to be passed to the Twig macro for rendering this field
     * @param \stdClass|null $value the module field's value, you can access the sub-field's value by calling
     *     `$value->{$this->key}`
     *
     * @return array
     */
    protected function configure(array $config, \stdClass $value = null): array
    {
        //  by default just return the config as is
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
     * Returns the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
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
     * Returns the config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Returns the validation rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
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

    /**
     * Returns the actual subfield data for given value.
     *
     * This method may be overridden by any sub-class in case of the given value shall be customized.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function getData($value)
    {
        return $value;
    }

    /**
     * Normalizes the given subfield value after being loaded.
     *
     * It's gonna be called in the "outer" field's `normalizeValue()` method.
     *
     * @param $value
     * @param \craft\base\ElementInterface|null $element
     *
     * @return string|mixed
     *
     * @see \Vierbeuter\Craft\Field\ModuleField::normalizeValue()
     * @see \craft\base\FieldInterface::normalizeValue()
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    /**
     * Serializes the given subfield value before being stored.
     *
     * It's gonna be called in the "outer" field's `serializeValue()` method.
     *
     * @param $value
     * @param \craft\base\ElementInterface|null $element
     *
     * @return string|mixed
     *
     * @see \Vierbeuter\Craft\Field\ModuleField::serializeValue()
     * @see \craft\base\FieldInterface::serializeValue()
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return $value;
    }
}
