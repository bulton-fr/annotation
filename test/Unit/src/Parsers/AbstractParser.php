<?php

namespace BultonFr\Annotation\Test\Unit\Parsers;

use atoum;
use BultonFr\Annotation\Parsers\AbstractParser as TestedClass;
use BultonFr\Annotation\Test\Unit\Helpers\ParserManagerTraits;

class AbstractParser extends atoum
{
    use ParserManagerTraits;

    protected $reader;
    protected $mock;

    public function beforeTestMethod($methodName)
    {
        $this->reader = new \mock\BultonFr\Annotation\Reader(
            '\BultonFr\Annotation\Test\Functional\Ref\Category'
        );

        $this->parserManager = $this->reader->getParserManager();
        $this->executeCreateReflectionObject();

        $this->mockGenerator
            ->makeVisible('execAnnotReader')
            ->makeVisible('generateAllAnnotObject')
            ->makeVisible('createAnnotObject')
        ;

        $this->mock = new \mock\BultonFr\Annotation\Parsers\AbstractParser(
            $this->parserManager,
            $this->parserManager->getReflection()
        );

        $this->addImportedNSToParser();
    }

    protected function execRun()
    {
        //Copy the run from PropertiesParser::run to have many items.
        $runMethod = function () {
            $this->execAnnotReader();
        };

        $runMethod = $runMethod->bindTo($this->mock, $this->mock);
        $runMethod();
    }

    public function testConstruct()
    {
        $this->assert('test Parsers\AbstractParser::__construct')
            ->given($this->mock = new \mock\BultonFr\Annotation\Parsers\AbstractParser(
                $this->parserManager,
                $this->parserManager->getReflection()
            ))
            ->object($this->mock->getParserManager())
                ->isIdenticalTo($this->parserManager)
            ->object($this->mock->getReflection())
                ->isIdenticalTo($this->parserManager->getReflection())
        ;
    }

    public function testGetterDefaultValues()
    {
        $this->assert('test Parsers\AbstractParser - getters default values')
            ->variable($this->mock->getAnnotReader())
                ->isNull()
            ->string($this->mock->getDocBlock())
                ->isEmpty()
            ->array($this->mock->getAnnotList())
                ->isEmpty()
        ;
    }

    public function testObtainDocBlock()
    {
        $this->assert('test Parser\AbstractParser::obtainDocBlock')
            ->string($this->mock->obtainDocBlock())
                ->isNotEmpty()
                ->contains('/**')
                ->contains('*/')
                ->contains('* @Add')
        ;
    }

    public function testExecAnnotReader()
    {
        $this->assert('test Parser\AbstractParser::execAnnotReader')
            ->variable($this->mock->execAnnotReader())
                ->isNull()
            ->object($this->mock->getAnnotReader())
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Reader')
        ;
    }

    public function testGenerateAllAnnotObject()
    {
        $this->assert('test Parser\AbstractParser::generateAllAnnotObject')
            ->given($this->execRun())
            ->then

            ->variable($this->mock->generateAllAnnotObject('class_Category'))
                ->isNull()
            ->array($annotList = $this->mock->getAnnotList())
                ->isNotEmpty()
                ->notHasKey('AddNS')
                ->hasKey('AddEntity')
            ->array($annotList['AddEntity'])
                ->keys
                    ->isEqualTo(['Ref\Category'])
                ->size
                    ->isEqualTo(1)
        ;
    }

    public function testCreateAnnotObject()
    {
        $this->assert('test Parser\AbstractParser::createAnnotObject - create object')
            ->given($this->execRun())
            ->given($annotList = $this->mock->getAnnotReader()->getAnnotationList())
            ->given($annotInfo = $annotList['AddEntity'][0])
            ->then

            ->object($this->mock->createAnnotObject(
                'class_Category',
                'AddEntity',
                $annotInfo
            ))
                ->IsInstanceOf('\BultonFr\Annotation\Annotations\AbstractAnnotation')
        ;

        $this->assert('test Parser\AbstractParser::createAnnotObject - class not exist')
            ->exception(function () use ($annotInfo) {
                $this->mock->createAnnotObject(
                    'class_Category',
                    'NotExistingClass',
                    $annotInfo
                );
            })
                ->hasCode(TestedClass::EXCEP_CLASS_NOT_FOUND)
        ;

        $this->assert('test Parser\AbstractParser::createAnnotObject - class not extends abstract')
            ->exception(function () use ($annotInfo) {
                $this->mock->createAnnotObject(
                    'class_Category',
                    'stdClass',
                    $annotInfo
                );
            })
                ->hasCode(TestedClass::EXCEP_NO_EXTENDS_ABSTRACT_ANNOTATION)
        ;
    }
}
