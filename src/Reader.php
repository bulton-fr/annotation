<?php

namespace BultonFr\Annotation;

use Exception;
use BultonFr\Annotation\Parsers\AbstractManyParser;

/**
 * Main Reader class.
 * Take a class name, use parser to obtain annotations, and have methods
 * to obtains annotations objects.
 *
 * @package BultonFr\Annotation
 */
class Reader
{
    /**
     * @const EXCEP_PARSE_NOT_EXECUTED Exception code if annotation is asked
     * before a call to parse()
     */
    const EXCEP_PARSE_NOT_EXECUTED = 102001;

    /**
     * @const EXCEP_METHOD_NOT_EXIST Exception code if user ask annotations
     * for a specific method which not exist
     */
    const EXCEP_METHOD_NOT_EXIST = 102002;

    /**
     * @const EXCEP_PROPERTY_NOT_EXIST Exception code if user ask annotations
     * for a specific property which not exist
     */
    const EXCEP_PROPERTY_NOT_EXIST = 102003;

    /**
     * The full class name to read
     *
     * @var string
     */
    protected $className = '';

    /**
     * The parser manager system
     *
     * @var \BultonFr\Annotation\ParserManager
     */
    protected $parserManager;

    /**
     * Construct
     *
     * @param string $className The class name
     */
    public function __construct(string $className)
    {
        $this->className     = $className;
        $this->parserManager = new ParserManager($this);
    }

    /**
     * Get the full class name to read
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Get the parser manager system
     *
     * @return \BultonFr\Annotation\ParserManager
     */
    public function getParserManager(): ParserManager
    {
        return $this->parserManager;
    }

    /**
     * Run all parser
     *
     * @return void
     */
    public function parse()
    {
        $this->parserManager->run();
    }

    /**
     * Return an array with all annotations objects declared on the class level
     *
     * @return array
     */
    public function obtainClassAnnotList(): array
    {
        $parserList = $this->parserManager->getParserList();
        if (isset($parserList['class']) === false) {
            throw new Exception(
                'Please call parse() method before obtain annotations',
                static::EXCEP_PARSE_NOT_EXECUTED
            );
        }

        return $parserList['class']->getAnnotList();
    }

    /**
     * Return an object which contain all methods parser
     *
     * @return BultonFr\Annotation\Parsers\AbstractManyParser
     */
    public function obtainMethodsList(): AbstractManyParser
    {
        $parserList = $this->parserManager->getParserList();
        if (isset($parserList['methods']) === false) {
            throw new Exception(
                'Please call parse() method before obtain annotations',
                static::EXCEP_PARSE_NOT_EXECUTED
            );
        }

        return $parserList['methods'];
    }

    /**
     * Return an object which contain all properties parser
     *
     * @return BultonFr\Annotation\Parsers\AbstractManyParser
     */
    public function obtainPropertiesList(): AbstractManyParser
    {
        $parserList = $this->parserManager->getParserList();
        if (isset($parserList['properties']) === false) {
            throw new Exception(
                'Please call parse() method before obtain annotations',
                static::EXCEP_PARSE_NOT_EXECUTED
            );
        }

        return $parserList['properties'];
    }

    /**
     * Return an array with all annotations objects declared for a method
     *
     * @param string $methodName The method name
     *
     * @return array
     *
     * @throws Exception If the method has not been found
     */
    public function obtainMethodAnnotList(string $methodName): array
    {
        $methodList = $this->obtainMethodsList()->getList();
        if (array_key_exists($methodName, $methodList) === false) {
            throw new Exception(
                'Method '.$methodName.' not exist in the list',
                static::EXCEP_METHOD_NOT_EXIST
            );
        }

        $methodParser = $methodList[$methodName];
        return $methodParser->getAnnotList();
    }

    /**
     * Return an array with all annotations objects declared for a property
     *
     * @param string $propertyName The property name
     *
     * @return array
     *
     * @throws Exception If the property has not been found
     */
    public function obtainPropertyAnnotList(string $propertyName): array
    {
        $propertiesList = $this->obtainPropertiesList()->getList();
        if (array_key_exists($propertyName, $propertiesList) === false) {
            throw new Exception(
                'Method '.$propertyName.' not exist in the list',
                static::EXCEP_PROPERTY_NOT_EXIST
            );
        }

        $propertyParser = $propertiesList[$propertyName];
        return $propertyParser->getAnnotList();
    }
}
