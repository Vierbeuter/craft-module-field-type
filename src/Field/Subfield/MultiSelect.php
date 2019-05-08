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
     * MultiSelect constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object
     * @param string $suffix the suffix being added to the module field's name to identify this subfield
     * @param array $options list of available options, each entry is an array with the keys `label` and `value`
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     */
    public function __construct(string $label, string $key, string $suffix, array $options, array $config = [])
    {
        parent::__construct(static::TYPE_MULTISELECT, $label, $key, $suffix, $config);

        $this->options = $options;
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
        $config['values'] = !empty($value->{$this->key}) ? $value->{$this->key} : null;
        $config['options'] = $this->options;

        return $config;
    }
}
