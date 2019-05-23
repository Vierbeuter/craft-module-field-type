<?php

namespace Vierbeuter\Craft\Field\Subfield;

/**
 * The UsersSelect class is a subfield implementation of type `elementSelect` using multiple User objects.
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_ELEMENTSELECT
 * @see \craft\elements\User
 */
class UsersSelect extends ElementSelect
{

    /**
     * UsersSelect constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $label, string $key, array $config = [], array $rules = [])
    {
        parent::__construct('craft\\elements\\User', $label, $key, $config, $rules);
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
        $userIds = [];

        if (!empty($value->{$this->key})) {
            $userIds = $value->{$this->key};

            if (!is_array($userIds)) {
                $userIds = [$userIds];
            }
        }

        $config['elements'] = $this->getData($userIds);

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
            return array_map(function ($userId) {
                return \Craft::$app->users->getUserById($userId);
            }, $value);
        }

        if (is_numeric($value)) {
            return \Craft::$app->users->getUserById($value);
        }

        return parent::getData($value);
    }
}
