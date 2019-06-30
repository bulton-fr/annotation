<?php

namespace BultonFr\Annotation\Test\Unit\Parsers;

use atoum;
use BultonFr\Annotation\Parsers\ClassParser as TestedClass;
use BultonFr\Annotation\Test\Unit\Helpers\ParserManagerTraits;

class ClassParser extends atoum
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
            ->makeVisible('obtainItemName')
            ->makeVisible('execAddNS')
            ->makeVisible('execAnnotReader')
        ;

        $this->mock = new \mock\BultonFr\Annotation\Parsers\ClassParser(
            $this->parserManager,
            $this->parserManager->getReflection()
        );
    }

    public function testObtainItemName()
    {
        $this->assert('test Parsers\ClassParser::obtainItemName')
            ->string($this->mock->obtainItemName())
                ->isEqualTo('class_BultonFr\Annotation\Test\Functional\Ref\Category')
        ;
    }

    public function testExecAddNS()
    {
        $this->assert('test Parsers\ClassParser::execAddNS')
            ->given($this->mock->execAnnotReader())
            ->then
            ->array($this->parserManager->getImportedNS())
                ->isEmpty()
            ->then
            ->variable($this->mock->execAddNS())
                ->isNull()
            ->array($this->parserManager->getImportedNS())
                ->isNotEmpty()
        ;
    }

    public function testRun()
    {
        $this->assert('test Parsers\ClassParser::run')
            ->given($this->addImportedNSToParser())
            ->then
            
            ->array($this->parserManager->getImportedNS())
                ->notHasKey('Security')
            ->array($this->mock->getAnnotList())
                ->isEmpty()
            ->then

            ->variable($this->mock->run())
                ->isNull()
            ->array($this->parserManager->getImportedNS())
                ->hasKey('Security')
            ->array($this->mock->getAnnotList())
                ->isNotEmpty()
        ;
    }
}
