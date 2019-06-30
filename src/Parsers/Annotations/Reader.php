<?php

namespace BultonFr\Annotation\Parsers\Annotations;

use Generator;

/**
 * Parse a docblock to find annotation, Instanciate Info object and sent all
 * info about the annotation to him.
 *
 * @package BultonFr\Annotation
 */
class Reader
{
    /**
     * @const REGEX_START_ANNOT The regex used to find the annotation's start
     *
     * @see README.md for code format
     */
    const REGEX_START_ANNOT = '/^\*( ?)@(\w+)(.*)/m';

    /**
     * @const REGEX_CUT_ANNOT The regex used to cut attributes into the
     * annotation value.
     *
     * @see README.md for code format
     */
    const REGEX_CUT_ANNOT = '/(\w+)=(.*)/m';
    
    /**
     * The list of Annotations\Info objects.
     * The format is an array, where keys are the annotations name, and values
     * are an numeric array which contains all Info object for this
     * annotation name.
     *
     * @var Info[<int>][<string>]
     */
    protected $annotationList = [];
    
    /**
     * The list of all annotation to ignore.
     *
     * @var string[]
     */
    protected static $ignoredAnnotations = [
        'api',
        'author',
        'category',
        'copyright',
        'deprecated',
        'example',
        'filesource',
        'global',
        'ignore',
        'internal',
        'license',
        'link',
        'method',
        'package',
        'param',
        'property',
        'property-read',
        'property-write',
        'return',
        'see',
        'since',
        'source',
        'subpackage',
        'throws',
        'todo',
        'uses',
        'used-by',
        'var',
        'version'
    ];
    
    /**
     * Get the list of Annotations\Info objects.
     * The format is an array, where keys are the annotations name, and values
     * are an numeric array which contains all Info object for this
     * annotation name.
     *
     * @return array
     */
    public function getAnnotationList(): array
    {
        return $this->annotationList;
    }

    /**
     * Get the list of all annotation to ignore.
     *
     * @return array
     */
    public static function getIgnoredAnnotations(): array
    {
        return self::$ignoredAnnotations;
    }
    
    /**
     * Add a new annotation to ignore
     *
     * @param string $annotationName The annotation's name to ignore
     *  It's the part after the @.
     *
     * @return void
     */
    public static function addIgnoredAnnotation(string $annotationName)
    {
        static::$ignoredAnnotations[] = $annotationName;
    }
    
    /**
     * Find all annotations and parse them.
     *
     * @param string $docComment The docblock to parse
     *
     * @return void
     */
    public function parse(string $docComment)
    {
        $annotationFind = $this->findAnnotations($docComment);
        
        foreach ($annotationFind as $annotationObj) {
            $this->parseAnnotation($annotationObj);
        }
    }
    
    /**
     * Find all annotations into $docComment
     *
     * @param string $docComment The docblock to parse
     *
     * @return \Generator
     */
    protected function findAnnotations(string $docComment): Generator
    {
        $lines        = explode("\n", $docComment);
        $inAnnotation = false;
        $annotInfo    = null;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            //Docblock's end
            if ($line === '*/') {
                break;
            }
            
            $annotMatch   = [];
            $isAnnotStart = preg_match(self::REGEX_START_ANNOT, $line, $annotMatch);
            
            if ($inAnnotation === false && $isAnnotStart !== 1) {
                //Not complete an annotation, and not new annotation to read.
                continue;
            } elseif ($inAnnotation === false && $isAnnotStart == 1) {
                //The first annotation to read :)

                $inAnnotation = true;
                $annotInfo    = $this->newAnnotation($annotMatch);
            } elseif ($inAnnotation === true && $isAnnotStart !== 1) {
                //The next of a multi-line annotation

                $parsedLine = trim($line);
                if ($parsedLine[0] === '*') {
                    $parsedLine = substr($parsedLine, 1);
                }

                $annotInfo->concatValueStr($parsedLine);
            } elseif ($inAnnotation === true && $isAnnotStart === 1) {
                //A new annotation to read.

                yield $annotInfo;
                $annotInfo = $this->newAnnotation($annotMatch);
            }
        }
        
        //Send previously readed annotation.
        //(only send when a new annotation is find into the loop).
        if ($annotInfo !== null) {
            yield $annotInfo;
        }
    }
    
    /**
     * Instanciate a new Info object
     *
     * @param array $annotMatch Info extracted by preg_match
     *
     * @return Info
     */
    protected function newAnnotation(array $annotMatch): Info
    {
        return new Info($annotMatch[2], $annotMatch[3]);
    }
    
    /**
     * Parse the value of an annotation value and add it to the property
     * $annotationList
     *
     * @param Info $annotationObj
     *
     * @return void
     */
    protected function parseAnnotation(Info $annotationObj)
    {
        $annotName = $annotationObj->getName();
        
        if (in_array($annotName, static::$ignoredAnnotations)) {
            return;
        }
        
        if (array_key_exists($annotName, $this->annotationList) === false) {
            $this->annotationList[$annotName] = [];
        }
        
        $this->parseValue($annotationObj);
        $this->annotationList[$annotName][] = $annotationObj;
    }
    
    /**
     * Parse the value of an annotation
     *
     * @param Info $annotationObj
     *
     * @return void
     */
    protected function parseValue(Info $annotationObj)
    {
        if ($annotationObj->getValueStr()[0] === '(') {
            $this->parseValueObject($annotationObj);
        } else {
            $annotationObj->addValue(
                null,
                $this->parseValueData($annotationObj->getValueStr())
            );
        }
    }
    
    /**
     * Parse an annotation value when it's a format (attribute=value, ...)
     *
     * @param Info $annotationObj
     *
     * @return void
     */
    protected function parseValueObject(Info $annotationObj)
    {
        //Remove parentheses
        $annotValueStr  = substr($annotationObj->getValueStr(), 1, -1);
        $annotValueList = explode(',', $annotValueStr);
        
        $prevAnnotValue = '';
        $valueName      = '';
        $valueData      = null;
        
        foreach ($annotValueList as $annotValue) {
            $annotValue = trim($annotValue);
            $matches    = [];
            
            //Cut the valueStr on the comma to obtain each attribute/value.
            $cutAnnot = preg_match(self::REGEX_CUT_ANNOT, $annotValue, $matches);
            
            if ($prevAnnotValue !== '') {
                //If the real value has been cuted before because contain comma
                $valueData = $prevAnnotValue.','.$annotValue;
            } elseif ($cutAnnot !== 1) {
                //No value has been found by preg_match
                continue;
            } else {
                //A new value has been found
                $valueName = $matches[1];
                $valueData = $matches[2];
            }
            
            //Check if we have all values (sometimes value can contain a comma)
            $lastPos = strlen($valueData) - 1;
            if (
                ($valueData[0] === '"' && $valueData[$lastPos] !== '"') ||
                ($valueData[0] === '\'' && $valueData[$lastPos] !== '\'')
            ) {
                $prevAnnotValue = $valueData;
                continue;
            }
            
            $parsedData = $this->parseValueData($valueData);
            
            $annotationObj->addValue($valueName, $parsedData);
            $prevAnnotValue = '';
        }
    }
    
    /**
     * Remove quote (double and simple) if exist
     * Parse the value to obtain the correct php type for the value
     *
     * @param string $valueData The value to transform
     *
     * @return void
     */
    protected function parseValueData(string $valueData)
    {
        if ($valueData[0] === '"' || $valueData[0] === '\'') {
            return substr($valueData, 1, -1); //String
        }
        
        $valueData = strtolower($valueData);

        if ($valueData === 'null') {
            return null;
        } elseif ($valueData === 'true') {
            return true;
        } elseif ($valueData === 'false') {
            return false;
        } elseif (strpos($valueData, '.') !== false) {
            return (float) $valueData;
        } else {
            return (int) $valueData;
        }
    }
}
