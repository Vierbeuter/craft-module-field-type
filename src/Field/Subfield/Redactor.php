<?php

namespace Vierbeuter\Craft\Field\Subfield;

use craft\base\ElementInterface;
use craft\redactor\Field;
use Vierbeuter\Craft\Field\Subfield;

/**
 * The Redactor class is a subfield implementation for richtext fields using the Redactor .
 *
 * @package Vierbeuter\Craft\Field\Subfield
 *
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_TEXTAREA
 */
class Redactor extends Subfield
{

    /**
     * @var string
     */
    protected $label;

    /**
     * Redactor constructor.
     *
     * @param string $label the subfield's label to be shown in Craft CP (pass empty string to omit)
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param string $redactorConfig the config file (like "Simple.json" or "Standard.json") to define buttons and
     *     behaviour of the richtext editor
     * @param array $config custom config array which overrides the resulting config of `initConfig()` method
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(string $label, string $key, string $redactorConfig, array $config = [], array $rules = [])
    {
        parent::__construct(static::TYPE_REDACTOR, '', $key, $config, $rules);

        //  we need a label field instead of passing it to the parent constructor because Redactor field implementation
        //  doesn't like to get a label via config object (that causes an error) --> so, let's just walk around a bit
        $this->label = $label;
        //  set JSON file for configuring the redactor field's buttons and its behaviour
        $this->config['redactorConfig'] = $redactorConfig;
    }

    /**
     * Initializes the subfield with given module field data.
     *
     * Sets suffixed name and ID, for instance.
     *
     * @param string $name the module field's handle
     * @param \craft\base\ElementInterface $element the element the module field is associated with, if there is one
     * @param string $namespacedId the module field's input ID
     * @param \stdClass|null $value the module field's value, you can access the sub-field's value by calling
     *     `$value->{$this->key}`
     */
    public function init(string $name, ElementInterface $element, string $namespacedId, \stdClass $value = null)
    {
        parent::init($name, $element, $namespacedId, $value);

        //  determine subfield value
        $value = !empty($value->{$this->key}) ? $value->{$this->key} : null;

        //  create redactor field
        $redactor = new Field($this->config);
        $redactor->handle = $this->config['id'];

        //  pass resulting HTML markup to config array to later simply grab it in the template
        $this->config['label'] = $this->label;
        $this->config['markup'] = $redactor->getInputHtml($value, $element);
    }
}
