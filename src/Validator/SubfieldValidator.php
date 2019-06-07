<?php

namespace Vierbeuter\Craft\Validator;

use craft\helpers\Json;
use yii\base\Model;
use yii\validators\Validator;

/**
 * The SubfieldValidator class validates a module field's value by validating each subfield value using the subfield's
 * validation rules.
 *
 * @package Vierbeuter\Craft\Validator
 */
class SubfieldValidator extends Validator
{

    /**
     * @var \Vierbeuter\Craft\Field\ModuleField
     */
    public $field;
    /**
     * @var \Vierbeuter\Craft\Field\Subfield[]
     */
    public $subfields;

    /**
     * Validates a value module field's value.
     *
     * @param string $value the module field's value which is expected to be a JSON-formatted string
     *
     * @return array|null the error message and the array of parameters to be inserted into the error message or `NULL`
     *     in case the given value is valid
     */
    protected function validateValue($value)
    {
        $model = new Model();

        if (!$value instanceof \stdClass) {
            $message = 'Value of field "{field}" is expected to be of type stdClass, but isn\'t. "{value}"';
            throw new \InvalidArgumentException($this->formatMessage($message, [
                'field' => $this->field->handle,
                'value' => $value,
            ]), 500);
        }

        //  validate all subfields values
        foreach ($this->subfields as $subfield) {
            if (isset($value->{$subfield->getKey()})) {
                $subfieldValue = $value->{$subfield->getKey()};

                //  apply validation rules on current subfield
                foreach ($subfield->getRules() as $rule) {
                    $validator = is_array($rule) ?
                        Validator::createValidator(array_shift($rule), $model, [], $rule) :
                        Validator::createValidator($rule, $model, []);
                    $result = $validator->validateValue($subfieldValue);

                    //  on the very first error of the very first invalid subfield
                    //  return the error and stop validation of all other subfields
                    if (!empty($result)) {
                        //  substitute `attribute` with label
                        if (!empty($subfield->getConfig()['label'])) {
                            $result[0] = $this->formatMessage($result[0], [
                                'attribute' => $subfield->getConfig()['label'],
                            ]);
                        }

                        return $result;
                    }
                }
            }
        }

        return null;
    }
}
