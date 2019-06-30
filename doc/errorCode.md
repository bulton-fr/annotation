# Error and exception code format

All error and exception code have the same format : `XYYZZZ`

The format is exploded like that :

* X: System
* YY : Class
* ZZZ : code into the system

## `BultonFr\Annotation`

Format : `1|YY|ZZZ`

* ParserManager: `1|01|ZZZ`
* Reader: `1|02|ZZZ`

## `BultonFr\Annotation\Annotations`

Format : `2|YY|ZZZ`

* AbstractAnnotation: `2|01|ZZZ`
* AddNS: `2|02|ZZZ`

## `BultonFr\Annotation\Parsers`

Format : `3|YY|ZZZ`

* AbstractManyParser: `3|01|ZZZ`
* AbstractParser: `3|02|ZZZ`
* ClassParser: `3|03|ZZZ`
* MethodParser: `3|04|ZZZ`
* MethodsParser: `3|05|ZZZ`
* PropertiesParser: `3|06|ZZZ`
* PropertyParser: `3|07|ZZZ`

## `BultonFr\Annotation\Parsers\Annotations`

Format : `4|YY|ZZZ`

* Info: `4|01|ZZZ`
* Reader: `4|02|ZZZ`
