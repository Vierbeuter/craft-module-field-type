<?php

namespace Vierbeuter\Craft\Field\Subfield;

use Vierbeuter\Craft\Field\Subfield;

/**
 * The Date class is a subfield implementation of type `date`.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_DATE
 */
class Date extends Subfield
{

    /**
     * Date constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $config custom config array being passed down to the subfield's Twig template
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $label, string $key, array $config = [], array $rules = [])
    {
        parent::__construct(static::TYPE_DATE, $label, $key, $config, $rules);
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

        return $config;
    }

    /**
     * Returns the actual subfield data for given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function getData($value)
    {
        try {
            return new \DateTime($value);
        } catch (\Exception $e) {
            //  just ignore malformed date
        }

        return parent::getData($value);
    }
}
