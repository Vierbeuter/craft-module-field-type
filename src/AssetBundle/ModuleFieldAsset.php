<?php

namespace Vierbeuter\Craft\AssetBundle;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * The ModuleFieldAsset represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * @see http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 */
class ModuleFieldAsset extends AssetBundle
{

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = dirname(__FILE__) . '/assets';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/ModuleField.js',
        ];

        parent::init();
    }
}
