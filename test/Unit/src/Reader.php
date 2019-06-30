<?php

namespace BultonFr\Annotation\Test\Unit;

use atoum;
use BultonFr\Annotation\Reader as TestedClass;

class Reader extends atoum
{
    protected $mock;

    public function beforeTestMethod($methodName)
    {
        $this->mock = new \mock\BultonFr\Annotation\Reader(
            '\BultonFr\Annotation\Test\Functional\Ref\Category'
        );

        $this->mock->getParserManager()->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\AddEntity',
            'AddEntity'
        );
        $this->mock->getParserManager()->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\Column',
            'Column'
        );
        $this->mock->getParserManager()->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\Route',
            'Route'
        );
    }

    public function testConstruct()
    {
        $this->assert('test Reader::__construct')
            ->given($this->mock = new \mock\BultonFr\Annotation\Reader(
                '\BultonFr\Annotation\Test\Functional\Ref\Category'
            ))
            ->string($this->mock->getClassName())
                ->isEqualTo('\BultonFr\Annotation\Test\Functional\Ref\Category')
            ->object($this->mock->getParserManager())
                ->isInstanceOf('\BultonFr\Annotation\ParserManager')
        ;
    }

    public function testParse()
    {
        $this->assert('test Reader::parse')
            ->array($this->mock->getParserManager()->getParserList())
                ->isEmpty()
            ->variable($this->mock->parse())
                ->isNull()
            ->array($this->mock->getParserManager()->getParserList())
                ->isNotEmpty()
        ;
    }

    public function testObtainClassAnnotList()
    {
        $this->assert('test Reader::obtainClassAnnotList - Exception if parse not called before')
            ->exception(function () {
                $this->mock->obtainClassAnnotList();
            })
                ->hasCode(TestedClass::EXCEP_PARSE_NOT_EXECUTED)
        ;
        
        $this->assert('test Reader::obtainClassAnnotList - Obtain annotations')
            ->given($this->mock->parse())
            ->then
            ->array($this->mock->obtainClassAnnotList())
                ->hasKey('AddEntity')
            ->array($this->mock->obtainClassAnnotList()['AddEntity'])
                ->hasKey('Ref\Category')
            ->object($this->mock->obtainClassAnnotList()['AddEntity']['Ref\Category'])
                ->isInstanceOf('\BultonFr\Annotation\Test\Functional\Annotations\AddEntity')
        ;
    }

    public function testObtainMethodsList()
    {
        $this->assert('test Reader::obtainMethodsList - Exception if parse not called before')
            ->exception(function () {
                $this->mock->obtainMethodsList();
            })
                ->hasCode(TestedClass::EXCEP_PARSE_NOT_EXECUTED)
        ;
        
        $this->assert('test Reader::obtainMethodsList - Obtain annotations')
            ->given($this->mock->parse())
            ->then
            ->object($methodsList = $this->mock->obtainMethodsList())
                ->isInstanceOf('\BultonFr\Annotation\Parsers\MethodsParser')
            ->object($methodsList->obtainForKey('indexAction'))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\MethodParser')
        ;
    }

    public function testObtainMethodAnnotList()
    {
        $this->assert('test Reader::obtainMethodAnnotList - Exception if parse not called before')
            ->exception(function () {
                $this->mock->obtainMethodAnnotList('indexAction');
            })
                ->hasCode(TestedClass::EXCEP_PARSE_NOT_EXECUTED)
        ;
        
        $this->assert('test Reader::obtainMethodAnnotList - Obtain annotations')
            ->given($this->mock->parse())
            ->then
            ->array($annotList = $this->mock->obtainMethodAnnotList('indexAction'))
                ->hasKeys(['Route', 'Security', 'HTTPMethod'])
            ->array($annotList['Route'])
                ->hasKey(0)
            ->object($annotList['Route'][0])
                ->isInstanceOf('\BultonFr\Annotation\Test\Functional\Annotations\Route')
        ;

        $this->assert('test Reader::obtainMethodAnnotList - Exception if method not exist')
            ->exception(function () {
                $this->mock->obtainMethodAnnotList('unitTest');
            })
                ->hasCode(TestedClass::EXCEP_METHOD_NOT_EXIST)
        ;
    }

    public function testObtainPropertiesList()
    {
        $this->assert('test Reader::obtainPropertiesList - Exception if parse not called before')
            ->exception(function () {
                $this->mock->obtainPropertiesList();
            })
                ->hasCode(TestedClass::EXCEP_PARSE_NOT_EXECUTED)
        ;
        
        $this->assert('test Reader::obtainPropertiesList - Obtain annotations')
            ->given($this->mock->parse())
            ->then
            ->object($methodsList = $this->mock->obtainPropertiesList())
                ->isInstanceOf('\BultonFr\Annotation\Parsers\PropertiesParser')
            ->object($methodsList->obtainForKey('id'))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\PropertyParser')
        ;
    }

    public function testObtainPropertyAnnotList()
    {
        $this->assert('test Reader::obtainPropertyAnnotList - Exception if parse not called before')
            ->exception(function () {
                $this->mock->obtainPropertyAnnotList('id');
            })
                ->hasCode(TestedClass::EXCEP_PARSE_NOT_EXECUTED)
        ;
        
        $this->assert('test Reader::obtainPropertyAnnotList - Obtain annotations')
            ->given($this->mock->parse())
            ->then
            ->array($annotList = $this->mock->obtainPropertyAnnotList('id'))
                ->hasKey('Column')
            ->array($annotList['Column'])
                ->hasKey(0)
            ->object($annotList['Column'][0])
                ->isInstanceOf('\BultonFr\Annotation\Test\Functional\Annotations\Column')
        ;

        $this->assert('test Reader::obtainPropertyAnnotList - Exception if method not exist')
            ->exception(function () {
                $this->mock->obtainPropertyAnnotList('unitTest');
            })
                ->hasCode(TestedClass::EXCEP_PROPERTY_NOT_EXIST)
        ;
    }
}
