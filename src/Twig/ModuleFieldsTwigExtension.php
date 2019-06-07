<?php

namespace Vierbeuter\Craft\Twig;

use craft\elements\MatrixBlock;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vierbeuter\Craft\Service\ModuleData;

/**
 * The ModuleFieldsTwigExtension class adds some helper functions and filters for Twig.
 *
 * @package Vierbeuter\Craft\Twig
 */
class ModuleFieldsTwigExtension extends AbstractExtension
{

    /**
     * @var \Vierbeuter\Craft\Service\ModuleData
     */
    protected $moduleData;

    /**
     * ModuleFieldsTwigExtension constructor.
     */
    public function __construct()
    {
        $this->moduleData = new ModuleData();
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('module_data', [$this, 'getModuleData']),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('module_data', [$this, 'getModuleData']),
        ];
    }

    /**
     * Returns the module data for given matrix block (respectively for given module).
     *
     * @param \craft\elements\MatrixBlock $matrixBlock
     *
     * @return \stdClass|null
     */
    public function getModuleData(MatrixBlock $matrixBlock)
    {
        return $this->moduleData->getModuleData($matrixBlock);
    }
}
