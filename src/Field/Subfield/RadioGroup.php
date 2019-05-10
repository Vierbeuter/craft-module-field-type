<?php

namespace Vierbeuter\Craft\Field\Subfield;

use Vierbeuter\Craft\Field\Subfield;

/**
 * The RadioGroup class is a subfield implementation of type `radioGroup`.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_RADIOGROUP
 */
class RadioGroup extends Subfield
{

    /**
     * @var array
     */
    private $options;

    /**
     * RadioGroup constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $options associative array of available radios, the array's key-value relations correspond to the
     *     radio options' input values and their labels
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     */
    public function __construct(string $label, string $key, array $options, array $config = [])
    {
        parent::__construct(static::TYPE_RADIOGROUP, $label, $key, $config);

        $this->options = $options;
    }

    /**
     * Configures the sub-field with given default config and the module field's value. Returns the resulting config
     * array.
     *
     * @param array $config the config object to be passed to the Twig macro for rendering this field
     * @param \stdClass|null $value the module field's value, you can access the sub-field's value by calling
     *     `$value->{$this->key}`
     *
     * @return array
     */
    public function configure(array $config, \stdClass $value = null): array
    {
        $config['value'] = !empty($value->{$this->key}) ? $value->{$this->key} : null;
        $config['options'] = $this->options;

        return $config;
    }
}
