<?php

namespace Vierbeuter\Craft\Field\Subfield;

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
     * @param string $key the field name as used in the ModuleField's value object
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     */
    public function __construct(string $label, string $key, array $config = [])
    {
        parent::__construct('craft\\elements\\Entry', $label, $key, $config);
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
        $entryIds = [];

        if (!empty($value->{$this->key})) {
            $entryIds = $value->{$this->key};

            if (!is_array($entryIds)) {
                $entryIds = [$entryIds];
            }
        }

        $config['elements'] = array_map(function ($entryId) {
            return \Craft::$app->entries->getEntryById($entryId);
        }, $entryIds);

        return parent::initConfig($config, $value);
    }
}
