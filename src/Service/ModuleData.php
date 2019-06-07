<?php

namespace Vierbeuter\Craft\Service;

use craft\elements\MatrixBlock;
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
     * @return \stdClass|null
     */
    public function getModuleData(MatrixBlock $matrixBlock)
    {
        try {
            $field = $this->getModuleField($matrixBlock);
        } catch (\InvalidArgumentException $e) {
            //  no ModuleField found, return empty result … just ignore the exception this time, we can handle that
            return null;
        }

        //  determine the field's value
        return $this->getModuleDataForField($matrixBlock, $field);
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
     *
     * @return \stdClass|null
     */
    public function getModuleDataForField(MatrixBlock $matrixBlock, ModuleField $field): \stdClass
    {
        return $matrixBlock->getFieldValue($field->handle);
    }
}
