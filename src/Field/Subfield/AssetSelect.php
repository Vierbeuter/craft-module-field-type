<?php

namespace Vierbeuter\Craft\Field\Subfield;

/**
 * The AssetSelect class is a subfield implementation of type `elementSelect` using a single Asset.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_ELEMENTSELECT
 * @see \craft\elements\Asset
 */
class AssetSelect extends AssetsSelect
{

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
        $config['limit'] = 1;

        return parent::configure($config, $value);
    }
}

