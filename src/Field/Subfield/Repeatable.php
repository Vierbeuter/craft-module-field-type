<?php

namespace Vierbeuter\Craft\Field\Subfield;

use Vierbeuter\Craft\Field\Subfield;

/**
 * The Multiply class is a subfield group for statically multiplying inner subfields X times (user cannot customize X).
 *
 * @package Vierbeuter\Craft\Field\Subfield
 */
class Repeatable extends Group
{

    /**
     * Subfield constructor.
     *
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param array $config config for this field
     * @param Subfield[] $subfields group of inner subfields that is repeatable
     * @param array $rules custom validation rules to be applied to the subfield
     */
    public function __construct(
        string $key,
        array $subfields,
        array $config = ['min' => 0, 'max' => '1'],
        array $rules = []
    ) {
        $groups = [];
        //for ($i = 1; $i <= $times; $i++) {
        //    $groups[] = new Group($i, array_map(function (Subfield $subfield) {
        //        return clone $subfield;
        //    }, $subfields));
        //}

        parent::__construct($key, $groups, Subfield::TYPE_REPEATABLE, $rules);
    }
}
+
