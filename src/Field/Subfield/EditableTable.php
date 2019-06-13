<?php

namespace Vierbeuter\Craft\Field\Subfield;

use Vierbeuter\Craft\Field\Subfield;
use Vierbeuter\Craft\Field\Subfield\EditableTable\Col;

/**
 * The EditableTable class is a subfield implementation of type `editableTable`.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_EDITABLETABLE
 */
class EditableTable extends Subfield
{

    /**
     * @var \Vierbeuter\Craft\Field\Subfield\EditableTable\Col[]
     */
    protected $cols;

    /**
     * EditableTable constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param \Vierbeuter\Craft\Field\Subfield\EditableTable\Col[] $cols column dfinitions
     * @param array $config custom config array being passed down to the subfield's Twig template
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $label, string $key, array $cols, array $config = [], array $rules = [])
    {
        parent::__construct(static::TYPE_EDITABLETABLE, $label, $key, $config, $rules);
        $this->cols = $cols;
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
        $config['cols'] = array_map(function (Col $col) {
            return $col->toArray();
        }, $this->cols);
        $config['rows'] = !empty($value->{$this->key}) ? $value->{$this->key} : null;

        return $config;
    }
}
