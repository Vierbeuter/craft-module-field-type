<?php

namespace Vierbeuter\Craft\Service;

use craft\elements\MatrixBlock;
use craft\helpers\Json;
use Vierbeuter\Craft\Field\ModuleField;

/**
 * The ModuleData service provides methods for retrieving module data.
 *
 * @package Vierbeuter\Craft\Service
 */
class ModuleData
{

    /**
     * Returns the module data for given matrix block (respectively for given module).
     *
     * @param \craft\elements\MatrixBlock $matrixBlock the matrix block to return the module data for
     * @param bool $asArray determines if the return value should be an array or an object (`stdClass`)
     *
     * @return array|\stdClass|null
     */
    public function getModuleData(MatrixBlock $matrixBlock, bool $asArray = false)
    {
        try {
            $field = $this->getModuleField($matrixBlock);
        } catch (\InvalidArgumentException $e) {
            //  no ModuleField found, return empty result … just ignore the exception this time, we can handle that
            return $asArray ? [] : null;
        }

        //  determine the field's value
        return $this->getModuleDataForField($matrixBlock, $field, $asArray);
    }

    /**
     * Returns the module field for given matrix block or throws an InvalidArgumentException if none of the matrix'
     * subfields is of type ModuleField.
     *
     * @param \craft\elements\MatrixBlock $matrixBlock
     *
     * @return \Vierbeuter\Craft\Field\ModuleField
     *
     * @see \Vierbeuter\Craft\Field\ModuleField
     */
    public function getModuleField(MatrixBlock $matrixBlock): ModuleField
    {
        foreach ($matrixBlock->getFieldLayout()->getFields() as $field) {
            //  just return the very first field of type ModuleField … any other fields are not of interest for us
            if ($field instanceof ModuleField) {
                return $field;
            }
        }

        $block = $matrixBlock->type->handle;
        $msg = 'Invalid matrix block given, module field missing. Please add a module field to "' . $block . '"';
        throw new \InvalidArgumentException($msg, 500);
    }

    /**
     * Returns the module data for given matrix block (respectively for given module) and for the specified module
     * field.
     *
     * @param \craft\elements\MatrixBlock $matrixBlock
     * @param \Vierbeuter\Craft\Field\ModuleField $field
     * @param bool $asArray
     *
     * @return array|mixed|null
     */
    public function getModuleDataForField(MatrixBlock $matrixBlock, ModuleField $field, bool $asArray = false)
    {
        $rawValue = $this->getRawModuleFieldData($matrixBlock, $field);

        return $this->getModuleFieldData($field, $rawValue, $asArray);
    }

    /**
     * Retrieves the raw module data (which is a JSON string) from given matrix block and field.
     *
     * @param \craft\elements\MatrixBlock $matrixBlock the matrix block to return the module data for
     * @param \Vierbeuter\Craft\Field\ModuleField $field the module-field whose handle to be used to access the data
     *
     * @return string
     */
    protected function getRawModuleFieldData(MatrixBlock $matrixBlock, ModuleField $field): string
    {
        return $matrixBlock->getFieldValue($field->handle);
    }

    /**
     * Determines the module data for given module-field and from given raw (JSON) value. Result is returned either as
     * `array` or as `stdClass` depending on given boolean flag.
     *
     * @param \Vierbeuter\Craft\Field\ModuleField $field the module-field having knowledge about all subfields to
     *     retrieve the module data from
     * @param string $rawValue JSON-formatted string containing the given module-field's value
     * @param bool $asArray determines if the return value should be an array or an object (`stdClass`)
     *
     * @return array|mixed|null
     */
    protected function getModuleFieldData(ModuleField $field, $rawValue, bool $asArray = false)
    {
        $value = Json::decodeIfJson($rawValue, $asArray);

        if (empty($value) || is_string($value)) {
            return $asArray ? [] : null;
        }

        return $asArray ?
            $this->getModuleFieldDataArray($field, $value) :
            $this->getModuleFieldDataObject($field, $value);
    }

    /**
     * Determines the module data as array for given module-field and from given module value.
     *
     * @param \Vierbeuter\Craft\Field\ModuleField $field
     * @param array $value
     *
     * @return array
     */
    protected function getModuleFieldDataArray(ModuleField $field, array $value): array
    {
        foreach ($field->getSubfields() as $subfield) {
            $subFieldValue = !empty($value[$subfield->getKey()]) ? $value[$subfield->getKey()] : null;
            $value[$subfield->getKey()] = $subfield->getData($subFieldValue);
        }

        return $value;
    }

    /**
     * Determines the module data as object (`stdClass`) for given module-field and from given module value.
     *
     * @param \Vierbeuter\Craft\Field\ModuleField $field
     * @param \stdClass $value
     *
     * @return \stdClass
     */
    protected function getModuleFieldDataObject(ModuleField $field, \stdClass $value): \stdClass
    {
        foreach ($field->getSubfields() as $subfield) {
            $subFieldValue = !empty($value->{$subfield->getKey()}) ? $value->{$subfield->getKey()} : null;
            $value->{$subfield->getKey()} = $subfield->getData($subFieldValue);
        }

        return $value;
    }
}
