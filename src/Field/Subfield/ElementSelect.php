<?php

namespace Vierbeuter\Craft\Field\Subfield;

use Vierbeuter\Craft\Field\Subfield;

/**
 * The ElementSelect class is a subfield implementation of type `elementSelect`.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_ELEMENTSELECT
 */
class ElementSelect extends Subfield
{

    private $elementType;

    /**
     * ElementSelect constructor.
     *
     * @param string $elementType the element's type, a fully qualified class name such as `craft\\elements\\Entry`
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     */
    public function __construct(string $elementType, string $label, string $key, array $config = [])
    {
        parent::__construct(static::TYPE_ELEMENTSELECT, $label, $key, $config);

        $this->elementType = $elementType;
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
        $config['elementType'] = $this->elementType;
        $config['value'] = !empty($value->{$this->key}) ? $value->{$this->key} : null;

        return $config;
    }
}
