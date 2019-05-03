<?php

namespace Vierbeuter\Craft\Field;

use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\services\Fields;
use craft\web\View;
use modules\gambit\fields\modules\DividerModuleField;
use modules\gambit\fields\modules\QuoteModuleField;
use yii\base\Event;

/**
 * The ModuleFields class bundles a collection of module fields and is responsible for registering them as well as
 * registering template paths.
 *
 * @package Vierbeuter\Craft\Field
 */
class ModuleFields
{

    /**
     * identifier for the templates' root directory
     */
    const TEMPLATE_ROOT_ID = 'vierbeuter-module-fields';

    /**
     * array of classnames for module fields
     *
     * @var string[]
     */
    protected $fields;

    /**
     * ModuleFields constructor.
     *
     * @param string[] $fields classnames of module fields
     *
     * @see \Vierbeuter\Craft\Field\ModuleField
     */
    public function __construct(array $fields = [])
    {
        $this->setFields($fields);
    }

    /**
     * Returns all classnames of registered module fields. THe returned classnames correspond subclasses of
     * `ModuleField`.
     *
     * @return string[]
     *
     * @see \Vierbeuter\Craft\Field\ModuleField
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Sets the list of classnames for module fields. The given classnames should correspond subclasses of
     * `ModuleField`.
     *
     * @param string[] $fields
     *
     * @see \Vierbeuter\Craft\Field\ModuleField
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Adds the given classname of a module field to the list of all fields. The given classname should correspond a
     * subclass of `ModuleField`.
     *
     * @param string $field
     *
     * @see \Vierbeuter\Craft\Field\ModuleField
     */
    public function addField(string $field)
    {
        $this->fields[] = $field;
    }

    /**
     * Registers the root directory for the module fields' templates.
     *
     * This method needs to be called within a Craft plugin's or Craft module's constructor.
     */
    public function registerTemplatesDir()
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $event) {
            $baseDir = self::getTemplatesBaseDir();
            if (is_dir($baseDir)) {
                $event->roots[static::TEMPLATE_ROOT_ID] = $baseDir;
            }
        });
    }

    /**
     * Returns the base directory for the module fields' templates.
     *
     * This method may be overridden by any sub-class in case of a custom templates path shall be used.
     *
     * @return string
     */
    protected static function getTemplatesBaseDir(): string
    {
        return dirname(__FILE__) . '/templates';
    }

    /**
     * Registers all fields to be available in Craft CP.
     *
     * This method needs to be called within a Craft plugin's or Craft module's `init()` method.
     */
    public function registerFields()
    {
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                foreach ($this->fields as $field) {
                    $event->types[] = $field;
                }
            }
        );
    }
}
