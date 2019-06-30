<?php

namespace BultonFr\Annotation\Test\Unit;

use atoum;
use BultonFr\Annotation\ParserManager as TestedClass;

class ParserManager extends atoum
{
    protected $reader;
    protected $mock;

    public function beforeTestMethod($methodName)
    {
        $this->reader = new \mock\BultonFr\Annotation\Reader(
            '\BultonFr\Annotation\Test\Functional\Ref\Category'
        );

        $setParserManager = function ($parserManager) {
            $this->parserManager = $parserManager;
        };
        $setParserManager = $setParserManager->bindTo($this->reader, $this->reader);

        $this->mockGenerator
            ->makeVisible('checkLib')
            ->makeVisible('createReflectionObject')
            ->makeVisible('createParserList')
            ->makeVisible('execAllParser')
            ->generate('BultonFr\Annotation\ParserManager')
        ;

        $this->mock = new \mock\BultonFr\Annotation\ParserManager(
            $this->reader
        );
        $setParserManager($this->mock);

        if ($methodName === 'testGetterDefaultValues' || $methodName === 'testAddNS') {
            return;
        }

        $this->mock->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\AddEntity',
            'AddEntity'
        );
        $this->mock->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\Column',
            'Column'
        );
        $this->mock->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\Route',
            'Route'
        );
    }

    public function testConstruct()
    {
        $this->assert('test ParserManager::__construct')
            ->given($this->mock = new \mock\BultonFr\Annotation\ParserManager(
                $this->reader
            ))
            ->object($this->mock->getReader())
                ->isIdenticalTo($this->reader)
        ;
    }

    public function testGetterDefaultValues()
    {
        //getReader already tested by testConstruct

        $this->assert('test ParserManager::getParserList')
            ->array($this->mock->getParserList())
                ->isEmpty()
        ;

        $this->assert('test ParserManager::getImportedNS')
            ->array($this->mock->getImportedNS())
                ->isEmpty()
        ;
    }

    /*
     * function mock not working here, I don't know why.
    public function testCheckLib()
    {
        $this->assert('test ParserManager::checkLib - without extension loaded')
            ->given($this->function->extension_loaded = false)
            ->given($this->function->ini_get = null)
            ->then
            ->variable($this->mock->checkLib())
                ->isNull()
        ;

        $this->assert('test ParserManager::checkLib - with extension loaded and save comment')
            ->given($this->function->extension_loaded = true)
            ->given($this->function->ini_get = 1)
            ->then
            ->variable($this->mock->checkLib())
                ->isNull()
        ;

        $this->assert('test ParserManager::checkLib - with extension loaded but without save comment')
            ->given($this->function->extension_loaded = true)
            ->given($this->function->ini_get = 0)
            ->then
            ->exception(function () {
                $this->mock->checkLib();
            })
                ->hasCode(TestedClass::EXCEP_SAVE_COMMENTS)
        ;
    }
    */

    public function testRun()
    {
        $this->assert('test ParserManager::run')
            ->given($this->calling($this->mock)->createReflectionObject = null)
            ->given($this->calling($this->mock)->createParserList = null)
            ->given($this->calling($this->mock)->execAllParser = null)
            ->then
            ->variable($this->mock->run())
                ->isNull()
            ->mock($this->mock)
                ->call('createReflectionObject')
                    ->once()
                ->call('createParserList')
                    ->once()
                ->call('execAllParser')
                    ->once()
        ;
    }

    public function testCreateReflectionObject()
    {
        $this->assert('test ParserManager::createReflectionObject')
            ->variable($this->mock->createReflectionObject())
                ->isNull()
            ->object($reflection = $this->mock->getReflection())
                ->isInstanceOf('\ReflectionClass')
            ->string($reflection->getName())
                ->isEqualTo('BultonFr\Annotation\Test\Functional\Ref\Category')
        ;
    }

    public function testCreateParserList()
    {
        $this->assert('test ParserManager::createParserList')
            ->given($this->mock->createReflectionObject())
            ->then
            ->array($this->mock->getParserList())
                ->isEmpty()
            ->variable($this->mock->createParserList())
                ->isNull()
            ->array($parserList = $this->mock->getParserList())
                ->keys
                    ->isEqualTo(['class', 'methods', 'properties'])
            ->object($parserList['class'])
                ->isInstanceOf('\BultonFr\Annotation\Parsers\ClassParser')
            ->object($parserList['methods'])
                ->isInstanceOf('\BultonFr\Annotation\Parsers\MethodsParser')
            ->object($parserList['properties'])
                ->isInstanceOf('\BultonFr\Annotation\Parsers\PropertiesParser')
        ;
    }

    public function testExecAllParser()
    {
        $this->assert('test ParserManager::execAllParser')
            ->given($this->mock->createReflectionObject())
            ->then

            ->given($setParserList = function ($key, $value) {
                $this->parserList[$key] = $value;
            })
            ->and($setParserList = $setParserList->bindTo($this->mock, $this->mock))
            ->then

            ->given($classParser = new \mock\BultonFr\Annotation\Parsers\ClassParser($this->mock, $this->mock->getReflection()))
            ->given($methodsParser = new \mock\BultonFr\Annotation\Parsers\MethodsParser($this->mock, $this->mock->getReflection()))
            ->given($propertiesParser = new \mock\BultonFr\Annotation\Parsers\PropertiesParser($this->mock, $this->mock->getReflection()))
            ->then

            ->if($this->calling($classParser)->run = null)
            ->and($this->calling($methodsParser)->run = null)
            ->and($this->calling($propertiesParser)->run = null)
            ->then

            ->given($setParserList('class', $classParser))
            ->given($setParserList('methods', $methodsParser))
            ->given($setParserList('properties', $propertiesParser))
            ->then

            ->variable($this->mock->execAllParser())
                ->isNull()
            ->mock($classParser)
                ->call('run')
                    ->once()
            ->mock($methodsParser)
                ->call('run')
                    ->once()
            ->mock($propertiesParser)
                ->call('run')
                    ->once()
        ;
    }

    public function testAddNS()
    {
        $this->assert('test ParserManager::addImportedNS - with new ns/alias')
            ->array($this->mock->getImportedNS())
                ->isEmpty()
            ->variable($this->mock->addImportedNS('\atoum', 'unit-test'))
                ->isNull()
            ->array($this->mock->getImportedNS())
                ->keys
                    ->isEqualTo(['unit-test'])
            ->string($this->mock->getImportedNS()['unit-test'])
                ->isEqualTo('\atoum')
        ;

        $this->assert('test ParserManager::addImportedNS - with existing alias but same ns')
            ->array($this->mock->getImportedNS())
                ->isNotEmpty()
            ->variable($this->mock->addImportedNS('\atoum', 'unit-test'))
                ->isNull()
            ->array($this->mock->getImportedNS())
                ->keys
                    ->isEqualTo(['unit-test'])
            ->string($this->mock->getImportedNS()['unit-test'])
                ->isEqualTo('\atoum')
        ;

        $this->assert('test ParserManager::addImportedNS - with existing alias but not same ns')
            ->array($this->mock->getImportedNS())
                ->isNotEmpty()
            ->exception(function () {
                $this->mock->addImportedNS('\atoumTest', 'unit-test');
            })
                ->hasCode(TestedClass::EXCEP_NS_ALREADY_EXIST)
        ;
    }
}
