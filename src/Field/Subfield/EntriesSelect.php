<?php

namespace Vierbeuter\Craft\Field\Subfield;

use craft\base\Element;

/**
 * The EntriesSelect class is a subfield implementation of type `elementSelect` using multiple Entry objects.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_ELEMENTSELECT
 * @see \craft\elements\Entry
 */
class EntriesSelect extends ElementSelect
{

    /**
     * EntriesSelect constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $config custom config array being passed down to the subfield's Twig template
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $label, string $key, array $config = [], array $rules = [])
    {
        parent::__construct('craft\\elements\\Entry', $label, $key, $config, $rules);
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
        $entryIds = [];

        if (!empty($value->{$this->key})) {
            $entryIds = $value->{$this->key};

            if (!is_array($entryIds)) {
                $entryIds = [$entryIds];
            }
        }

        $config['elements'] = $this->getData($entryIds);

        return parent::configure($config, $value);
    }

    /**
     * Returns the element for given ID.
     *
     * @param int $id
     *
     * @return \craft\base\Element|null
     */
    protected function getElementById(int $id): ?Element
    {
        return \Craft::$app->entries->getEntryById($id);
    }
}
