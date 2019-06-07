<?php

namespace Vierbeuter\Craft\Field\Subfield;

use craft\base\Element;
use craft\base\ElementInterface;
use Vierbeuter\Craft\Field\Subfield;

/**
 * The ElementSelect class is a subfield implementation of type `elementSelect`.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_ELEMENTSELECT
 */
abstract class ElementSelect extends Subfield
{

    private $elementType;

    /**
     * ElementSelect constructor.
     *
     * @param string $elementType the element's type, a fully qualified class name such as `craft\\elements\\Entry`
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $config custom config array being passed down to the subfield's Twig template
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $elementType, string $label, string $key, array $config = [], array $rules = [])
    {
        parent::__construct(static::TYPE_ELEMENTSELECT, $label, $key, $config, $rules);

        $this->elementType = $elementType;
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
        $config['elementType'] = $this->elementType;
        $config['value'] = !empty($value->{$this->key}) ? $value->{$this->key} : null;

        return $config;
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
        return $this->getData($value);
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
        if (is_array($value)) {
            return array_map(function ($singleValue) {
                return $this->getElement($singleValue);
            }, $value);
        }

        return $this->getElement($value);
    }

    /**
     * Returns the element for given single value.
     *
     * @param mixed $value
     *
     * @return \craft\base\Element|null
     */
    protected function getElement($value): ?Element
    {
        if ($value instanceof Element) {
            return $value;
        }

        if (is_numeric($value)) {
            return $this->getElementById($value);
        }

        if ($value instanceof \stdClass && !empty($value->id)) {
            return $this->getElementById($value->id);
        }

        if (empty($value)) {
            return null;
        }

        throw new \InvalidArgumentException('Expected $data of type "' . Element::class . '", but got "' . gettype($value) . '" instead.');
    }

    /**
     * Returns the element for given ID.
     *
     * @param int $id
     *
     * @return \craft\base\Element|null
     */
    abstract protected function getElementById(int $id): ?Element;
}
