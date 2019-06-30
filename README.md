# Annotations reader system

[![Build Status](https://travis-ci.org/bulton-fr/annotation.svg?branch=master)](https://travis-ci.org/bulton-fr/annotation) [![Code Coverage](https://scrutinizer-ci.com/g/bulton-fr/annotation/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bulton-fr/annotation/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bulton-fr/annotation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bulton-fr/annotation/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/bulton-fr/annotation/v/stable.svg)](https://packagist.org/packages/bulton-fr/annotation) [![Latest Unstable Version](https://poser.pugx.org/bulton-fr/annotation/v/unstable.svg)](https://packagist.org/packages/bulton-fr/annotation) [![License](https://poser.pugx.org/bulton-fr/annotation/license.svg)](https://packagist.org/packages/bulton-fr/annotation)

## Install it

With composer `composer require bulton-fr/annotation`.

## Use it

First, the system need to parse your class :

```php
$annotations = new \BultonFr\Annotation\Reader(myClass::class);
$annotations->parse();
```

And to obtain all annotations find, you have methods :

* `public function obtainClassAnnotList(): array`
* `public function obtainMethodsList(): Parser\AbstractManyParser`
* `public function obtainPropertiesList(): Parsers\AbstractManyParser`
* `public function obtainMethodAnnotList(string $methodName): array`
* `public function obtainPropertyAnnotList(string $propertyName): array`

The class `Parser\AbstractManyParser` implement the class `Iterator`, so you use a `foreach` on it. Each item will be an instance of `Parser\AbstractParser`.  
To obtain the array which contain all annotation (like the array returned by `obtain*AnnotList`) from the `Parser\AbstractParser` class, you should use the method `getAnnotList()`.

The array returned by `obtain*AnnotList` has the format : `Annotations\AbstractAnnotation[<int>][<string>]`.

So for example :

```php
/**
 * @Table(name="writing_list")
 * @Security(role="admin", fct="mySecurityCheck")
 * @AddEntity(ns="\BultonFr\Annotation\Test\Functional\Ref\Account")
 * @AddEntity(
 *  ns="\BultonFr\Annotation\Test\Functional\Ref\Category",
 *  alias="Ref\Category"
 * )
 */
```

The annotations list will have the format :

```
array(3) {
    "Table" => array(1) {
        0 => object \BultonFr\Annotation\Test\Functional\Annotations\Table
    },
    "Security" => array(1) {
        0 => object \BultonFr\Annotation\Test\Functional\Annotations\Security
    },
    "AddEntity" => array(2) {
        0 => object \BultonFr\Annotation\Test\Functional\Annotations\AddEntity,
        1 => object \BultonFr\Annotation\Test\Functional\Annotations\AddEntity,
    }
}
```

### All annotations objects

When an annotation is found by the parser, an instance of `Parsers\Annotations\Info` is instanced, which will contain all info about the annotation. There are:

* the "name" (the part just after `@`)
* the value (the part after the name, trimed).
  * the property `valueStr` contain the string value (like into the docblock), without line-break.
  * the property `values` is an array of all values.  
    if the valueStr contain attribute/value format, the attribute will be the array key; else the array key will be numeric.

With info into this object, a new object is instanced. When an annotation is declared, a class name is present. This class will be instanced each time this annotation is found.  
It's this object (not the Info object) you will obtain by methods `obtain*AnnotList`.

The class used for the annotation must extends `Annotations\AbstractAnnotation`.
Into it, you have access to the Reader, the Info object, you can detect if a key has been declared and obtain the value for a key.

You can find many example how to create a new class dedicated to an annotation :

* the class `/src/Annotations/AddNS`
* all classes in `/test/Functional/Annotations/`

### Import namespace

Many annotations systems use classes declared for the class (with php `use` keyword).
I make the choice not to use it, mainly to not read the disk each time.
So all annotation you will use need to be "imported".  
To import an annotation, two choice :
* Declare it on the parser
* Use the annotation `@AddNS`

To declare a new annotation, you need two things :
* The complete class name which will be instanced each time this annotation is found.
* The alias (the part after `@`) to use (by default the class name)

#### Declare the namespace on the parser

To do that, we use the method `addImportedNS` on the `ParserManager`.

```php
/**
 * Add a new imported namespace (obtain by AddNS or manually)
 *
 * @param string $name The class path (with ns) of the class which will be
 *  instancied when the annotation defined by $alias will be found.
 * @param string $alias The annotation key
 *
 * @return void
 */
public function addImportedNS(string $name, string $alias)
```

```php
$annotations = new \BultonFr\Annotation\Reader(myClass::class);
$annotations->getParserManager()->addImportedNS(
    \myApp\myCustomAnnotation::class
    'CustomAnnot'
);
$annotations->parse();
```

Now, into the myClass (annotation in class, methods and properties docblocks), I can use the annotation `@CustomAnnot`.

#### With the annotation `AddNS`

This annotation can be added only on the class docblock. If this annotation is present on properties or methods, they will be no effect.

```php
/**
 * My class description
 *
 * @AddNS(ns="\myApp\myCustomAnnotation", alias="CustomAnnot")
 * @CustomAnnot(...)
 */
class myClass
```

Of course, you can use the imported annotation directly. When the system have all `Info` object, it read all AddNS first, and after it read all other annotations (so the order in docblock is not important).

### Example

```php
$annotReader = new \BultonFr\Annotation\Reader(
    \BultonFr\Annotation\Test\Functional\Ref\Account
);

$annotReader
    ->getParserManager()
        ->addImportedNS('\BultonFr\Annotation\Test\Functional\Annotations\Column')
        ->addImportedNS('\BultonFr\Annotation\Test\Functional\Annotations\Route')
;

$annotReader->parse();

//string "int"
var_dump($annotReader->obtainPropertyAnnotList('id')['Column'][0]->getType());
```

## Ignore an annotation

Some annotations are ignored by the system. For example, all annotations used by docblock like `@param` etc.
The list of ignored annotation is in `Parsers\Annotations\Reader::ignoredAnnotations`.  
The list is based on the "tag reference" in the [phpDocumentor documentation](https://docs.phpdoc.org/references/phpdoc/tags/index.html).  
If you see missing annotation on the list, you can create an issue or a pull-request ;)

You can add a new annotation to ignore with `Parsers\Annotations\Reader::addIgnoredAnnotation`.

The property `ignoredAnnotations` and associated methods (getter and add) are static. So you need to declare it once for all instances.

```php
/**
 * Add a new annotation to ignore
 *
 * @param string $annotationName The annotation's name to ignore
 *  It's the part after the @.
 *
 * @return void
 */
public static function addIgnoredAnnotation(string $annotationName)
```
