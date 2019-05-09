<?php

namespace Vierbeuter\Craft\Field\Subfield;

/**
 * The AssetsSelect class is a subfield implementation of type `elementSelect` using multiple Asset objects.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_ELEMENTSELECT
 * @see \craft\elements\Asset
 */
class AssetsSelect extends ElementSelect
{

    /**
     * AssetsSelect constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     */
    public function __construct(string $label, string $key, array $config = [])
    {
        parent::__construct('craft\\elements\\Asset', $label, $key, $config);
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
        $assetIds = [];

        if (!empty($value->{$this->key})) {
            $assetIds = $value->{$this->key};

            if (!is_array($assetIds)) {
                $assetIds = [$assetIds];
            }
        }

        $config['elements'] = array_map(function ($assetId) {
            return \Craft::$app->assets->getAssetById($assetId);
        }, $assetIds);

        return parent::initConfig($config, $value);
    }
}
