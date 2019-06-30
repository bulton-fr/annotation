# How it's work internally

## From beginning to the parser start :

* `Reader::__construct` : 
  * Instanciate `ParserManager`
* `Reader::parse`
  * `ParserManager::run`
    * `ParserManager::createReflectionObject`
      * Instanciate `\ReflactionClass`
    * `ParserManager::createParserList`
      * Instanciate (in this order) :
        * `Parsers\ClassParser` (`Parsers\AbstractParser`)
        * `Parsers\MethodsParser` (`Parsers\AbstractManyParser`)
        * `Parsers\PropertiesParser` (`Parsers\AbstractManyParser`)
    * `ParserManager::execAllParser`
      * Call (in this order) :
        * `Parsers\ClassParser::run` : `Parsers\AbstractParser::run`
        * `Parsers\MethodsParser::run` : `Parsers\AbstractManyParser::run`
          * Get all methods with `Reflection`
          * For all methods :
            * Instanciate `Parsers\MethodParser` (`Parsers\AbstractParser`)
            * Execute `Parsers\MethodParser::run` : `Parsers\AbstractParser::run`
            * Add the `Parsers\MethodParser` instance to items list.
        * `Parsers\PropertiesParser::run` : `Parsers\AbstractManyParser::run`
          * Get all properties with `Reflection`
          * For all properties :
            * Instanciate `Parsers\PropertyParser` (`Parsers\AbstractParser`)
            * Execute `Parsers\PropertyParser::run` : `Parsers\AbstractParser::run`
            * Add the `Parsers\PropertyParser` instance to items list.

## Into `Parsers\AbstractParser::run` :

* `Parsers\AbstractParser::execAnnotReader`
  * Instanciate `Parsers\Annotations\Reader`
  * Obtain the docblock from `Reflection` object
  * Call `Parsers\Annotations\Reader::parse`
* `Parsers\AbstractParser::execAddNS` (`Parsers\ClassParser` only)
  * Obtain all `AddNS` annotations
  * For each `AddNS` annotations
    * Instanciate `Annotations\AddNS` to obtain data from the annotation
    * Call `ParserManager::addImportedNS`
* `Parsers\AbstractParser::generateAllAnnotObject`
  * For each item into the `Parsers\Annotations\Reader` list
    * Instanciate the dedicated class for annotation
    * Add the object to the `Parsers\AbstractParser::annotList` property

## Into `Parsers\Annotations\Reader::parse` :

* Call `Parsers\Annotations\Reader::findAnnotations`
  * Extract each annotation from the docblock
  * For each annotation find :
    * Instanciate a `Parsers\Annotations\Info` object
    * Yield the `Parsers\Annotations\Info` object
* For each item find by `findAnnotations` (`\Generator`)
  * Call `Parsers\Annotations\Reader::parseAnnotation`
    * Call `Parsers\Annotations\Reader::parseValue` to parse the value
      * Extract all values with `Parsers\Annotations\Reader::parseValueObject` if it's a attribute/value format
      * Parse the value to the PHP format with `Parsers\Annotations\Reader::parseValueData`
      * Call `Parsers\Annotations\Info::addValue` to add value to the item
    * Add the item to the property `annotationList`.
