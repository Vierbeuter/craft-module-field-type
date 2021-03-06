<?php

namespace Vierbeuter\Craft\Field\Subfield;

use craft\base\ElementInterface;
use craft\helpers\Json;
use Vierbeuter\Craft\Field\Subfield;

/**
 * The Group class is a subfield implementation for grouping inner subfields.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 */
class Group extends Subfield
{

    /**
     * @var \Vierbeuter\Craft\Field\Subfield[]
     */
    protected $subfields;

    /**
     * Subfield constructor.
     *
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param Subfield[] $subfields group of inner subfields that is repeatable
     * @param string $type field type to override the default one with for customization
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $key, array $subfields, $type = Subfield::TYPE_GROUP, array $rules = [])
    {
        parent::__construct($type, '', $key, [], $rules);
        $this->subfields = $subfields;
    }

    /**
     * Initializes the subfield with given module field data.
     *
     * Sets suffixed name and ID, for instance.
     *
     * @param string $name the module field's handle
     * @param \craft\base\ElementInterface $element the element the module field is associated with, if there is one
     * @param string $namespacedId the module field's input ID
     * @param \stdClass|null $value the module field's value, you can access the subfield's value by calling
     *     `$value->{$this->key}`
     */
    public function init(string $name, ElementInterface $element, string $namespacedId, \stdClass $value = null)
    {
        parent::init($name, $element, $namespacedId, $value);
        $this->initSubfields($name, $element, $namespacedId, $value);
    }

    /**
     * Initializes the group's subfields with given module field data.
     *
     * This method may be overridden.
     *
     * @param string $name the module field's handle
     * @param \craft\base\ElementInterface $element the element the module field is associated with, if there is one
     * @param string $namespacedId the module field's input ID
     * @param \stdClass|null $value the module field's value, you can access the subfield's value by calling
     *     `$value->{$this->key}`
     */
    protected function initSubfields(string $name, ElementInterface $element, string $namespacedId, $value)
    {
        foreach ($this->getSubfields() as $subfield) {
            $subfield->suffix = $this->suffix . $subfield->suffix;
            $subfield->init($name, $element, $namespacedId, !empty($value->{$this->key}) ? $value->{$this->key} : null);
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
    protected function configure(array $config, \stdClass $value = null): array
    {
        $config['value'] = !empty($value->{$this->key}) ? Json::encode($value->{$this->key}) : null;

        return $config;
    }

    /**
     * Returns the subfields.
     *
     * @return \Vierbeuter\Craft\Field\Subfield[]
     */
    public function getSubfields(): array
    {
        return $this->subfields;
    }

    /**
     * Returns an array representation of this subfield.
     *
     * @return array
     */
    public function toArray(): array
    {
        $toArray = parent::toArray();

        $toArray['subfields'] = array_map(function (Subfield $subfield) {
            return $subfield->toArray();
        }, $this->getSubfields());

        return $toArray;
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
        if ($value instanceof \stdClass) {
            foreach ($this->getSubfields() as $subfield) {
                if (isset($value->{$subfield->getKey()})) {
                    $value->{$subfield->getKey()} = $subfield->normalizeValue($value->{$subfield->getKey()}, $element);
                }
            }
        }

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
        if ($value instanceof \stdClass) {
            foreach ($this->getSubfields() as $subfield) {
                if (isset($value->{$subfield->getKey()})) {
                    $value->{$subfield->getKey()} = $subfield->serializeValue($value->{$subfield->getKey()}, $element);
                }
            }
        }

        return $value;
    }
}
