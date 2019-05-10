<?php

namespace Vierbeuter\Craft\Field\Subfield;

use craft\helpers\Json;
use Vierbeuter\Craft\Field\Subfield;

/**
 * The Multiply class is a subfield group for statically multiplying inner subfields X times (user cannot customize X).
 *
 * @package Vierbeuter\Craft\Field\Subfield
 */
class Multiply extends Group
{

    /**
     * Subfield constructor.
     *
     * @param string $key the field name as used in the ModuleField's value object (ensure it's in "camelCase")
     * @param int $times number of times the set of inner subfields has to be multiplied
     * @param Subfield[] $subfields group of inner subfields that is repeatable
     */
    public function __construct(string $key, int $times, array $subfields)
    {
        $groups = [];
        for ($i = 1; $i <= $times; $i++) {
            $groups[] = new Group($i, array_map(function (Subfield $subfield) {
                return clone $subfield;
            }, $subfields));
        }

        parent::__construct($key, $groups, Subfield::TYPE_MULTIPLY);
    }
}
