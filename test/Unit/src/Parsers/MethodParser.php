<?php

namespace BultonFr\Annotation\Test\Unit\Parsers;

use atoum;
use BultonFr\Annotation\Parsers\MethodParser as TestedClass;
use BultonFr\Annotation\Test\Unit\Helpers\ParserManagerTraits;

class MethodParser extends atoum
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

        $this->mock = new \mock\BultonFr\Annotation\Parsers\MethodParser(
            $this->parserManager,
            $this->parserManager->getReflection()->getMethod('indexAction')
        );
    }

    public function testRun()
    {
        $this->assert('test Parsers\MethodParser::run')
            ->given($this->addImportedNSToParser(true))
            ->then
            
            ->array($this->mock->getAnnotList())
                ->isEmpty()
            ->then

            ->variable($this->mock->run())
                ->isNull()
            ->array($this->mock->getAnnotList())
                ->isNotEmpty()
        ;
    }
}
