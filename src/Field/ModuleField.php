<?php

namespace Vierbeuter\Craft\Field;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;
use Vierbeuter\Craft\AssetBundle\ModuleFieldAsset;
use Vierbeuter\Craft\Validator\SubfieldValidator;
use yii\db\Schema;

/**
 * The ModuleField class can be extended by other classes to create custom field types with.
 *
 * @package Vierbeuter\Craft\Field
 */
abstract class ModuleField extends Field
{

    /**
     * Returns all subfields for this module field.
     *
     * @return \Vierbeuter\Craft\Field\Subfield[]
     */
    abstract public function getSubfields(): array;

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, $this->getAttributeValidationRules());

        return $rules;
    }

    /**
     * Returns an array of validation rules that will be applied to this field's attributes.
     *
     * This method can be overridden by any subclass to define the actual validation rules.
     *
     * @return array
     */
    protected function getAttributeValidationRules(): array
    {
        return [
            //['someAttribute', 'string'],
            //['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }

    /**
     * Returns the column type that this field should get within the content table.
     *
     * This method will only be called if [[hasContentColumn()]] returns true.
     *
     * @return string The column type. [[\yii\db\QueryBuilder::getColumnType()]] will be called
     * to convert the give column type to the physical one. For example, `string` will be converted
     * as `varchar(255)` and `string(100)` becomes `varchar(100)`. `not null` will automatically be
     * appended as well.
     * @see \yii\db\QueryBuilder::getColumnType()
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * Normalizes the field’s value for use.
     *
     * This method is called when the field’s value is first accessed from the element. For example, the first time
     * `entry.myFieldHandle` is called from a template, or right before [[getInputHtml()]] is called. Whatever
     * this method returns is what `entry.myFieldHandle` will likewise return, and what [[getInputHtml()]]’s and
     * [[serializeValue()]]’s $value arguments will be set to.
     *
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        $value = is_string($value) ? Json::decodeIfJson($value, false) : $value;

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
     * Prepares the field’s value to be stored somewhere, like the content table or JSON-encoded in an entry revision
     * table.
     *
     * Data types that are JSON-encodable are safe (arrays, integers, strings, booleans, etc).
     * Whatever this returns should be something [[normalizeValue()]] can handle.
     *
     * @param mixed $value The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The serialized field value
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

        return Json::encode($value);
    }

    /**
     * Returns the component’s settings HTML.
     *
     * An extremely simple implementation would be to directly return some HTML:
     *
     * ```php
     * return '<textarea name="foo">'.$this->getSettings()->foo.'</textarea>';
     * ```
     *
     * For more complex settings, you might prefer to create a template, and render it via
     * [[\craft\web\View::renderTemplate()]]. For example, the following code would render a template loacated at
     * craft/plugins/myplugin/templates/_settings.html, passing the settings to it:
     *
     * ```php
     * return Craft::$app->getView()->renderTemplate('myplugin/_settings', [
     *     'settings' => $this->getSettings()
     * ]);
     * ```
     *
     * If you need to tie any JavaScript code to your settings, it’s important to know that any `name=` and `id=`
     * attributes within the returned HTML will probably get [[\craft\web\View::namespaceInputs() namespaced]],
     * however your JavaScript code will be left untouched.
     *
     * For example, if getSettingsHtml() returns the following HTML:
     *
     * ```html
     * <textarea id="foo" name="foo"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * …then it might actually look like this before getting output to the browser:
     *
     * ```html
     * <textarea id="namespace-foo" name="namespace[foo]"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * As you can see, that JavaScript code will not be able to find the textarea, because the textarea’s `id=`
     * attribute was changed from `foo` to `namespace-foo`.
     *
     * Before you start adding `namespace-` to the beginning of your element ID selectors, keep in mind that the actual
     * namespace is going to change depending on the context. Often they are randomly generated. So it’s not quite
     * that simple.
     *
     * Thankfully, [[\craft\web\View]] service provides a couple handy methods that can help you deal
     * with this:
     *
     * - [[\craft\web\View::namespaceInputId()]] will give you the namespaced version of a given ID.
     * - [[\craft\web\View::namespaceInputName()]] will give you the namespaced version of a given input name.
     * - [[\craft\web\View::formatInputId()]] will format an input name to look more like an ID attribute value.
     *
     * So here’s what a getSettingsHtml() method that includes field-targeting JavaScript code might look like:
     *
     * ```php
     * public function getSettingsHtml()
     * {
     *     // Come up with an ID value for 'foo'
     *     $id = Craft::$app->getView()->formatInputId('foo');
     *
     *     // Figure out what that ID is going to be namespaced into
     *     $namespacedId = Craft::$app->getView()->namespaceInputId($id);
     *
     *     // Render and return the input template
     *     return Craft::$app->getView()->renderTemplate('myplugin/_fieldinput', [
     *         'id'           => $id,
     *         'namespacedId' => $namespacedId,
     *         'settings'     => $this->getSettings()
     *     ]);
     * }
     * ```
     *
     * And the _settings.html template might look like this:
     *
     * ```twig
     * <textarea id="{{ id }}" name="foo">{{ settings.foo }}</textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('{{ namespacedId }}');
     * </script>
     * ```
     *
     * The same principles also apply if you’re including your JavaScript code with
     * [[\craft\web\View::registerJs()]].
     *
     * @return string|null
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            $this->getSettingsHtmlTemplate(),
            [
                'field' => $this,
            ]
        );
    }

    /**
     * Returns the field’s input HTML.
     *
     * An extremely simple implementation would be to directly return some HTML:
     *
     * ```php
     * return '<textarea name="'.$name.'">'.$value.'</textarea>';
     * ```
     *
     * For more complex inputs, you might prefer to create a template, and render it via
     * [[\craft\web\View::renderTemplate()]]. For example, the following code would render a template located at
     * craft/plugins/myplugin/templates/_fieldinput.html, passing the $name and $value variables to it:
     *
     * ```php
     * return Craft::$app->getView()->renderTemplate('myplugin/_fieldinput', [
     *     'name'  => $name,
     *     'value' => $value
     * ]);
     * ```
     *
     * If you need to tie any JavaScript code to your input, it’s important to know that any `name=` and `id=`
     * attributes within the returned HTML will probably get [[\craft\web\View::namespaceInputs() namespaced]],
     * however your JavaScript code will be left untouched.
     *
     * For example, if getInputHtml() returns the following HTML:
     *
     * ```html
     * <textarea id="foo" name="foo"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * …then it might actually look like this before getting output to the browser:
     *
     * ```html
     * <textarea id="namespace-foo" name="namespace[foo]"></textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('foo');
     * </script>
     * ```
     *
     * As you can see, that JavaScript code will not be able to find the textarea, because the textarea’s `id=`
     * attribute was changed from `foo` to `namespace-foo`.
     *
     * Before you start adding `namespace-` to the beginning of your element ID selectors, keep in mind that the actual
     * namespace is going to change depending on the context. Often they are randomly generated. So it’s not quite
     * that simple.
     *
     * Thankfully, [[\craft\web\View]] provides a couple handy methods that can help you deal with this:
     *
     * - [[\craft\web\View::namespaceInputId()]] will give you the namespaced version of a given ID.
     * - [[\craft\web\View::namespaceInputName()]] will give you the namespaced version of a given input name.
     * - [[\craft\web\View::formatInputId()]] will format an input name to look more like an ID attribute value.
     *
     * So here’s what a getInputHtml() method that includes field-targeting JavaScript code might look like:
     *
     * ```php
     * public function getInputHtml($value, $element)
     * {
     *     // Come up with an ID value based on $name
     *     $id = Craft::$app->getView()->formatInputId($name);
     *
     *     // Figure out what that ID is going to be namespaced into
     *     $namespacedId = Craft::$app->getView()->namespaceInputId($id);
     *
     *     // Render and return the input template
     *     return Craft::$app->getView()->renderTemplate('myplugin/_fieldinput', [
     *         'name'         => $name,
     *         'id'           => $id,
     *         'namespacedId' => $namespacedId,
     *         'value'        => $value
     *     ]);
     * }
     * ```
     *
     * And the _fieldinput.html template might look like this:
     *
     * ```twig
     * <textarea id="{{ id }}" name="{{ name }}">{{ value }}</textarea>
     *
     * <script type="text/javascript">
     *     var textarea = document.getElementById('{{ namespacedId }}');
     * </script>
     * ```
     *
     * The same principles also apply if you’re including your JavaScript code with
     * [[\craft\web\View::registerJs()]].
     *
     * @param mixed $value The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                               raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\InvalidConfigException
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle($this->getAssetBundleClass());
        // Get our id and namespace
        $id = $view->formatInputId($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        //  determine and initialize subfields
        $subfields = $this->getSubfields();
        foreach ($subfields as $subfield) {
            $subfield->init($this->handle, $element, $namespacedId, $value);
        }

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id . $element->getId(),
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => $view->namespaceInputId(''),
            'subfields' => array_map(function (Subfield $subfield) {
                return $subfield->toArray();
            }, $subfields),
            'init' => empty($element->getId()),
        ];
        $jsonVars = Json::encode($jsonVars);
        $view->registerJs("$('#{$namespacedId}-field').ModuleField(" . $jsonVars . ");");

        $templateParams = [
            'name' => $this->handle,
            'value' => empty($value) ? '{}' : Json::encode($value),
            'field' => $this,
            'id' => $id,
            'namespacedId' => $namespacedId,
            'subfields' => $subfields,
        ];

        // Render the input template
        try {
            return $view->renderTemplate($this->getInputHtmlTemplate(), $templateParams);
        } catch (\Exception $e) {
            //  in case of any exception occurs one of the subfields might not have been rendered
            //  e.g. because of some malformed data or anything like that
            //  --> if this happens try again, but this time in safe_mode (without the subfields)
            $templateParams['safe_mode'] = true;

            return $view->renderTemplate($this->getInputHtmlTemplate(), $templateParams);
        }
    }

    /**
     * Returns the classname for the responsible asset bundle.
     *
     * The returned string corresponds a sub-class of `\craft\web\AssetBundle`. By default this will be the
     * `\Vierbeuter\Craft\AssetBundle\ModuleFieldAsset`.
     *
     * This method may be overridden by any sub-class in case of a custom asset bundle is gonna be used.
     *
     * @return string
     *
     * @see \craft\web\AssetBundle
     * @see \Vierbeuter\Craft\AssetBundle\ModuleFieldAsset
     */
    public function getAssetBundleClass(): string
    {
        return ModuleFieldAsset::class;
    }

    /**
     * Returns the path to the field's settings template.
     *
     * This method may be overridden by any sub-class in case of a custom template is gonna be used.
     *
     * @return string
     */
    protected function getSettingsHtmlTemplate(): string
    {
        return $this->getTemplateRootId() . '/ModuleField_settings.twig';
    }

    /**
     * Returns the path to the field's input template.
     *
     * This method may be overridden by any sub-class in case of a custom template is gonna be used.
     *
     * @return string
     */
    protected function getInputHtmlTemplate(): string
    {
        return $this->getTemplateRootId() . '/ModuleField_input.twig';
    }

    /**
     * Returns the templates' root ID.
     *
     * This method may be overridden by any sub-class in case of a root ID is gonna be used (e.g. the one of a Craft
     * plugin or a Craft module).
     *
     * @return string
     */
    public function getTemplateRootId(): string
    {
        return ModuleFields::TEMPLATE_ROOT_ID;
    }

    /**
     * Returns the validation rules for an element with this field.
     *
     * Rules should be defined in the array syntax required by [[\yii\base\Model::rules()]],
     * with one difference: you can skip the first argument (the attribute list).
     *
     * ```php
     * [
     *     // explicitly specify the field attribute
     *     [$this->handle, 'string', 'min' => 3, 'max' => 12],
     *     // skip the field attribute
     *     ['string', 'min' => 3, 'max' => 12],
     *     // you can only pass the validator class name/handle if not setting any params
     *     'bool',
     * ]
     * ```
     *
     * To register validation rules that should only be enforced for _live_ elements,
     * set the rule [scenario](https://www.yiiframework.com/doc/guide/2.0/en/structure-models#scenarios)
     * to `live`:
     *
     * ```php
     * [
     *     ['string', 'min' => 3, 'max' => 12, 'on' => \craft\base\Element::SCENARIO_LIVE],
     * ]
     * ```
     *
     * @return array
     */
    public function getElementValidationRules(): array
    {
        $rules = parent::getElementValidationRules();

        $rules[] = [SubfieldValidator::class, 'field' => $this, 'subfields' => $this->getSubfields()];

        return $rules;
    }
}
