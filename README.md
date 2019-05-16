# Craft Module Field Type

A library for your Craft 3 plugin/module development.

## Contents

* [What is this?](#what-is-this)
	* [Short version](#tell-me-in-two-sentences)
	* [Long version](#i-still-cant-get-it)

## What is this?

### Tell me in two sentences…

This library provides an easy way of **defining matrix blocks** for matrix fields that are used **for content modules**.

Its usage reduces the amount of clicks in Craft CP by moving the location of a matrix block's field definitions from CP to PHP code.

### I still don't get it…

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

--

<!-- TODO: add usage to docs! -->
