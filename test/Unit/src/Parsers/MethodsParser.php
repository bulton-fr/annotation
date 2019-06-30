<?php

namespace BultonFr\Annotation\Test\Unit\Parsers;

use atoum;
use BultonFr\Annotation\Parsers\MethodsParser as TestedClass;
use BultonFr\Annotation\Test\Unit\Helpers\ParserManagerTraits;

class MethodsParser extends atoum
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

        $this->mock = new \mock\BultonFr\Annotation\Parsers\MethodsParser(
            $this->parserManager,
            $this->parserManager->getReflection()
        );

        $this->addImportedNSToParser(true);
    }

    public function testRun()
    {
        $this->assert('test Parsers\MethodsParser::run')
            ->integer($this->mock->count())
                ->isEqualTo(0)
            ->variable($this->mock->run())
                ->isNull()
            ->integer($this->mock->count())
                ->isGreaterThan(0)
            ->object($this->mock->current())
                ->isInstanceOf('\BultonFr\Annotation\Parsers\MethodParser')
        ;
    }
}
