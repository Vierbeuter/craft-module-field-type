<?php

namespace Vierbeuter\Craft\Field\Subfield\EditableTable;

/**
 * The Col class represents a column definition for an `editableTableField`.
 *
 * @package Vierbeuter\Craft\Field\Subfield\EditableTable
 *
 * @see \Vierbeuter\Craft\Field\Subfield\EditableTable
 * @see \Vierbeuter\Craft\Field\Subfield::TYPE_EDITABLETABLE
 */
class Col
{

    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $heading;

    /**
     * Col constructor.
     *
     * @param string $type
     * @param string $heading
     */
    public function __construct(string $type, string $heading)
    {
        $this->type = $type;
        $this->heading = $heading;
    }

    /**
     * Returns an array representation of this column definition as required by column config which is passed to the
     * `editableTableField` Twig macro.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'heading' => $this->heading,
        ];
    }
}
