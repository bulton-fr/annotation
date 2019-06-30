<?php

namespace BultonFr\Annotation\Test\Unit\Parsers;

use atoum;
use BultonFr\Annotation\Parsers\PropertyParser as TestedClass;
use BultonFr\Annotation\Test\Unit\Helpers\ParserManagerTraits;

class PropertyParser extends atoum
{
    use ParserManagerTraits;

    protected $reader;
    protected $mock;

    public function beforeTestMethod($methodName)
    {
        $this->reader = new \mock\BultonFr\Annotation\Reader(
            '\BultonFr\Annotation\Test\Functional\Ref\Account'
        );

        $this->parserManager = $this->reader->getParserManager();
        $this->executeCreateReflectionObject();

        $this->mockGenerator
            ->makeVisible('obtainItemName')
            ->makeVisible('execAddNS')
            ->makeVisible('execAnnotReader')
        ;

        $this->mock = new \mock\BultonFr\Annotation\Parsers\PropertyParser(
            $this->parserManager,
            $this->parserManager->getReflection()->getMethod('indexAction')
        );
    }

    public function testRun()
    {
        $this->assert('test Parsers\PropertyParser::run')
            ->given($this->addImportedNSToParser())
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
