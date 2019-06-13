<?php

namespace Vierbeuter\Craft\Field\Subfield;

use Vierbeuter\Craft\Field\Subfield;

/**
 * The MultiSelect class is a subfield implementation of type `multiselect`.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_MULTISELECT
 */
class MultiSelect extends Subfield
{

    /**
     * @var array
     */
    private $options;
    /**
     * @var array
     */
    private $optionsAssociative;

    /**
     * MultiSelect constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $options list of available options, each entry is an array with the keys `label` and `value`
     * @param array $config custom config array being passed down to the subfield's Twig template
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $label, string $key, array $options, array $config = [], array $rules = [])
    {
        parent::__construct(static::TYPE_MULTISELECT, $label, $key, $config, $rules);

        $this->options = $options;
        $this->optionsAssociative = [];

        foreach ($options as $option) {
            if (isset($option['value']) && isset($option['label'])) {
                $this->optionsAssociative[$option['value']] = $option['label'];
            }
        }
    }

    /**
     * Configures the subfield with given default config and the module field's value. Returns the resulting config
     * array.
     *
     * @param array $config the config object to be passed to the Twig macro for rendering this field
     * @param \stdClass|null $value the module field's value, you can access the subfield's value by calling
     *     `$value->{$this->key}`
     *
     * @return array
     */
    public function configure(array $config, \stdClass $value = null): array
    {
        $config['values'] = !empty($value->{$this->key}) ? $value->{$this->key} : null;
        $config['options'] = $this->options;

        return $config;
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
        if (is_array($value)) {
            return array_map(function ($optionValue) {
                if (!empty($this->optionsAssociative[$optionValue])) {
                    return $this->optionsAssociative[$optionValue];
                }

                return parent::getData($optionValue);
            }, $value);
        }

        return parent::getData($value);
    }
}
