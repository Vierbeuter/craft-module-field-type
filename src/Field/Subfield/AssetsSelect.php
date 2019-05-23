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
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $config custom config array being passed down to the subfield's Twig template
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $label, string $key, array $config = [], array $rules = [])
    {
        parent::__construct('craft\\elements\\Asset', $label, $key, $config, $rules);
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
        $assetIds = [];

        if (!empty($value->{$this->key})) {
            $assetIds = $value->{$this->key};

            if (!is_array($assetIds)) {
                $assetIds = [$assetIds];
            }
        }

        $config['elements'] = $this->getData($assetIds);

        return parent::configure($config, $value);
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
            return array_map(function ($assetId) {
                return \Craft::$app->assets->getAssetById($assetId);
            }, $value);
        }

        if (is_numeric($value)) {
            return \Craft::$app->assets->getAssetById($value);
        }

        return parent::getData($value);
    }
}
