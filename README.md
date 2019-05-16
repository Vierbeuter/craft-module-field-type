# Craft Module Field Type

A library for your Craft 3 plugin/module development.

## Contents

* [What is this?](#what-is-this)
	* [Short version](#tell-me-in-two-sentences)
	* [Long version](#i-still-dont-get-it)
* [How to use it?](#how-to-use-it)
	* [Install dependency](#composer)
	* [Prepare content modules](#php)
	* [Configure section(s)](#craft-cms)
	* [Implement templates](#twig)
* [License](#license)

## What is this?

### Tell me in two sentences…

This library provides an easy way of **defining matrix blocks** for matrix fields that are used **for content modules**.

Its usage reduces the amount of clicks in Craft CP by moving the location of a matrix block's field definitions from CP to PHP code.

⬆️ [back to top](#contents)

### I still don't get it…

Alright, let us build a scenario to explain what this lib is for.

#### Matrix blocks instead of richtext field (WYSIWYG)

Let's say you have a Craft 3 installation with **several sections**. There are singles like "Page" or channels like "Use case", "Service" and "Blog article".  
Now, let's assume the entries of some of these sections **don't need a classical richtext field** (e.g. a Redactor field) because richtext is too unstructured. You as a programmer have no influence on where an editor adds headlines, lists etc.

Instead, the sections need some kind of structured content blocks. There is no richtext field, but a selection of "content things" you can add block-wise like "add a summary block here" or "add an image gallery there".  
Each block has only those fields the block actually needs. A teaser-like content block has a headline, an image and a link target, for example.

That for the **sections get a matrix field** where each **matrix block represents a content block**. We prefer calling those content blocks **content modules**.

#### What's a content module?

A content module is a **self-contained content unit** within an entry. It's a part of an entry's content, it can be:

* a *stage* module (consisting of some moody image, a big headline and a short summary)
* a *listing* module (like one that consists of nice icons and copy texts)
* a *quote* module (an inspirational quote next to the author's photo and name)
* a *call-to-action* module (some detailed instructions followed by a big button)
* a *newsletter-registration* module (a short form for registration)
* a *statistics* module (with numbers and charts and something interesting like that)
* … and so on … (ask your conceptual designer for more ideas)

#### How to achieve that in Craft CMS?

As already said, you'd create a matrix field for that in Craft CMS and create a matrix block for each content module. Then you'd add fields to all matrix blocks (a text field, an asset selection and a textarea to the one block for *stage* modules etc.).

Especially the click-heavy addition of fields to all matrix blocks can be a hassle in the CP if there are many content modules having many fields.  
It's getting worse if you don't only need a single matrix field with block definitions for all available content modules but two or more matrix fields with shared and different block definitions (to control that different sections make use of different subsets of all available content modules … e.g. a "Blog article" needs *stage* and *quote* while "Use case" should not have *quote* available and "Service" also needs *listing*).

The more matrix fields you need and the more content modules you have the more complicated it gets to configure all that in the Craft CP.

#### And that's the point where this library comes in?

Exactly.

⬆️ [back to top](#contents)

## How to use it?

### Composer

First of all, to make this lib available in your Craft plugin¹ you need to add it to your dependencies:

```bash
composer require vierbeuter/craft-module-field-type dev-develop
```

¹ When mentioning "plugins" we're talking about both Craft plugins *and* modules. From now on we omit the word "module" to not accidentally mix it up with content modules (which are realized using matrix blocks).

⬆️ [back to top](#contents)

### PHP

#### Create content module classes

Add a new folder to your `src/` directory to place all your content modules in. Name it `contentmodules/` or whatever you like to name it. Create PHP class files for each content module and save them into the new directory.

You should now have a directory structure similar to the following:

```bash
# ./ is your project root

plugins/your-awesome-plugin
└── src
    ├── YourAwesomePlugin.php
    ├── contentmodules
    │   ├── Conclusion.php
    │   ├── ImageGallery.php
    │   ├── Quote.php
    │   ├── Stage.php
    │   ├── Teaser.php
    |   └── …
    …
```

Implement your content modules as follows:

* `plugins/your-awesome-plugin/src/contentmodules/ImageGallery.php`

```php
<?php

namespace plugins\yourawesomeplugin\contentmodules;

use Vierbeuter\Craft\Field\ModuleField;
use Vierbeuter\Craft\Field\Subfield\AssetsSelect;

class ImageGallery extends ModuleField
{

    /**
     * Returns all sub-fields for this module field.
     *
     * @return \Vierbeuter\Craft\Field\Subfield[]
     */
    public function getSubfields(): array
    {
        return [
            new AssetsSelect(\Craft::t('yourawesomeplugin', 'Images'), 'images', [
                'viewMode' => 'large',
            ]),
        ];
    }
}
```

* `plugins/your-awesome-plugin/src/contentmodules/Teaser.php`

```php
<?php

namespace plugins\yourawesomeplugin\contentmodules;

use Vierbeuter\Craft\Field\ModuleField;
use Vierbeuter\Craft\Field\Subfield\AssetSelect;
use Vierbeuter\Craft\Field\Subfield\EntrySelect;
use Vierbeuter\Craft\Field\Subfield\Text;
use Vierbeuter\Craft\Field\Subfield\Textarea;

class Teaser extends ModuleField
{

    /**
     * Returns all sub-fields for this module field.
     *
     * @return \Vierbeuter\Craft\Field\Subfield[]
     */
    public function getSubfields(): array
    {
        return [
            new AssetSelect(\Craft::t('yourawesomeplugin', 'Teaser Image'), 'image', [
                'viewMode' => 'large',
            ]),
            new Textarea(\Craft::t('yourawesomeplugin', 'Teaser Text'), 'text'),
            new EntrySelect(\Craft::t('yourawesomeplugin', 'Button Target'), 'target'),
            new Text(\Craft::t('yourawesomeplugin', 'Button Label'), 'label'),
        ];
    }
}
```

… You get the idea. Go on that way with all other modules.

##### Some last notes about the code above

* You get an overview of all available `Subfield` implementations [&rarr; here](https://github.com/Vierbeuter/craft-module-field-type/tree/develop/src/Field/Subfield).
* You can pass an optional `config` array to each `Subfield` constructor (always the last parameter). That `config` array will then be passed down to the [&rarr; field's template](https://github.com/craftcms/cms/tree/develop/src/templates/_includes/forms).
* The module classes extend the class `ModuleField` as you maybe noticed … Why it's "field" and not just "module", you ask? – Well, we're building custom field types actually and not custom matrix blocks.

#### Register content module classes

Open your Craft plugin class (which is `plugins/your-awesome-plugin/src/YourAwesomePlugin.php` in the previously shown sample file tree). We have to register the module fields and features provided by this lib.

Then add following import and property the class:

```php
use Vierbeuter\Craft\Field\ModuleFields;
```

```php
/**
 * @var \Vierbeuter\Craft\Field\ModuleFields
 */
protected $moduleFields;
```

Now, head to the class' `__construct()` and add these lines:

```php
// define a list of all content modules (respectively all field types being used for modules)
$this->moduleFields = new ModuleFields([
    Conclusion::class,
    ImageGallery::class,
    Quote::class,
    Stage::class,
    Teaser::class,
    // …
]);
// register the lib's templates directory (for being able to render the sub-fields)
$this->moduleFields->registerTemplatesDir();
```

Find the plugin's `init()` method and add the following:

```php
// register the content modules (which actually are custom field types as we learnt before)
$this->moduleFields->registerFields();
// also register some Twig extensions
$this->moduleFields->registerTwigExtension();
```

That's basically everything you have to do in your PHP sources. Nothing else.

⬆️ [back to top](#contents)

### Craft CMS

Log into the admin panel (Craft CP), navigate to **Settings &gt; Fields** and add a **new field of type `Matrix`**. Name its handle something like `contentModules` (we'll access it soon in our Twig templates).

Add a **new block** to the matrix field. Name it `Image Gallery`, for example. Add just a **single field**, name it whatever you want and **select the field type** `ImageGallery`.  
Keep in mind the matrix block's handle which is `imageGallery` in this case. We'll need that very soon.

Repeat these steps for every content module aaaaaand … That's all you need to do in the matrix field.

Go to **Settings &gt; Sections**, open any **Entry type** to change its field layout and **register the field `contentModules`** to that Entry type. Save it and switch back from your browser to your favorite development software again (may it be an editor or an IDE…).

⬆️ [back to top](#contents)

### Twig

Last, but not least implement the templates. – A pretty good practice is to have a bunch of sub-templates for inclusion: one template for each content module.

Do so, create a template file for all modules. Please name them the exactly same as the matrix blocks (remember you should have kept the names in mind?).

```bash
# ./ is your project root

templates/
├── index.twig
├── contentmodules
│   ├── conclusion.twig
│   ├── imageGallery.twig
│   ├── quote.twig
│   ├── stage.twig
│   ├── teaser.twig
│   └── …
…
```

In the `index.twig` template (or the one that is set for your sections) you can add the following snippet:

```twig
{% for module in entry.contentModules.all() %}
    <section class="module {{ module.type }}">
        {%- include 'contentmodules/' ~ module.type with { 'module': module | module_data } -%}
    </section>
{% endfor %}
```

The `module_data` filter comes with the library's Twig extension, by the way.

One of the templates could then look like this:

```twig
{# templates/contentmodules/teaser.twig #}

{# @var module stdClass|array #}
{# @see plugins\yourawesomeplugin\contentmodules\Teaser #}

<img src="{{ module.image.url }}" />
<p>{{ module.text }}</p>
<a href="{{ module.target.url }}">{{ module.label }}</a>
```

⬆️ [back to top](#contents)

### That's all folks!

Congratulations. You made it! Whenever you need to remove, change or add any content module related sub-fields, you now don't have to do that in Craft CP. You can do that directly in your PHP sources.

## License

This library is licensed under the terms of the **MIT license**. See also the project's [license file](./LICENSE).

⬆️ [back to top](#contents)
