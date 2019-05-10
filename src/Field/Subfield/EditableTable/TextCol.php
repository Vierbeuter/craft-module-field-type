<?php

namespace Vierbeuter\Craft\Field\Subfield\EditableTable;

/**
 * The TextCol class represents a column definition for an `editableTableField`.
 *
 * @package Vierbeuter\Craft\Field\Subfield\EditableTable
 */
class TextCol extends Col
{

    /**
     * TextCol constructor.
     *
     * @param string $heading
     */
    public function __construct(string $heading)
    {
        parent::__construct('text', $heading);
    }
}
