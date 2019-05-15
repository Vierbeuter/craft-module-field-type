# Craft Module Field Type

A library for your Craft 3 plugin/module development.

## Contents

* [What is this? (short version)](#tell-me-in-two-sentences)
* [What is this? (long version)](#i-still-cant-imagine-what-this-is-supposed-to-be)

## What is this?

### Tell me in two sentences.

This library offers an easy way of **defining matrix blocks** for matrix fields that are used **for content modules**.  

Developers will be happy to hear that it reduces the amount of clicks in Craft CP by moving the location of a matrix block's field definitions from CP to PHP code.

### I still can't imagine what this is supposed to be…

#### Matrix blocks instead of richtext field (WYSIWYG)

Let's say you have a Craft 3 installation with **several sections**. There are singles like "Page" or channels like "Use case", "Service", "Blog article", for instance.  
Now, let's assume the entries of some of these sections **don't have a classical richtext field** (e.g. a Redactor field) to define all of the entries' contents.

Instead, the **entries have a matrix field** where each matrix **block represents a content module**.

#### What's a content module?

A content module is a **self-contained content unit** within an entry. It can be any content block of the entry such as:

* a *stage* module (consisting of some moody image, a big headline and a short summary)
* a *listing* module (like one that consists of nice icons and copy texts)
* a *quote* module (an inspirational quote next to the author's photo and name)
* a *call-to-action* module (some detailed instructions followed by a big button)
* a *newsletter-registration* module (a short form for registration)
* a *statistics* module (with numbers and charts and something interesting like that)
* … and so on … (ask your conceptual designer for more ideas)

#### How to achieve that in Craft CMS?

Normally, you'd create a matrix field for that in Craft CMS and create a matrix block for each content module. Then you'd add fields to all matrix blocks (a text field, an asset selection and a textarea to the one block for *stage* modules etc.).

Especially the addition of fields to all matrix blocks can be a hassle in the CP if there are many content modules having many fields. It's getting worse if you don't only need a single matrix field with block definitions for all available content modules but two or more matrix fields with shared and different block definitions (to control that different sections make use of different subsets of all available content modules … e.g. a "Blog article" needs *stage* and *quote* while "Use case" should not have *quote* available and "Service" also needs *listing*).

The more matrix fields you need and the more content modules you have the more complicated it gets to configure all that in the Craft CP.

#### And that's the point where this library comes in?

Exactly.

--

<!-- TODO: add usage to docs! -->
