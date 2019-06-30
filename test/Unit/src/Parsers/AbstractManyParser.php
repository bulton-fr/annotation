<?php

namespace BultonFr\Annotation\Test\Unit\Parsers;

use atoum;
use BultonFr\Annotation\Parsers\AbstractManyParser as TestedClass;
use BultonFr\Annotation\Test\Unit\Helpers\ParserManagerTraits;

class AbstractManyParser extends atoum
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

        $this->mock = new \mock\BultonFr\Annotation\Parsers\AbstractManyParser(
            $this->parserManager,
            $this->parserManager->getReflection()
        );

        $this->addImportedNSToParser();
    }

    protected function execRun()
    {
        //Copy the run from PropertiesParser::run to have many items.
        $runMethod = function () {
            $propertyList = $this->reflection->getProperties();

            foreach ($propertyList as $propertyInfo) {
                $parser = new \BultonFr\Annotation\Parsers\PropertyParser(
                    $this->parserManager,
                    $propertyInfo
                );

                $this->addItem($propertyInfo->getName(), $parser);
            }
        };

        $runMethod = $runMethod->bindTo($this->mock, $this->mock);
        $runMethod();
    }

    public function testConstruct()
    {
        $this->assert('test Parsers\AbstractManyParser::__construct')
            ->given($this->mock = new \mock\BultonFr\Annotation\Parsers\AbstractManyParser(
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
        $this->assert('test Parsers\AbstractManyParser - getters default values')
            ->array($this->mock->getList())
                ->isEmpty()
            ->array($this->mock->getItemKeys())
                ->isEmpty()
            ->integer($this->mock->getIndex())
                ->isEqualTo(0)
        ;
    }

    public function testIterator()
    {
        $this->assert('test Parsers\AbstractManyParser - Iterator')
            ->boolean($this->mock->valid())
                ->isFalse()
            ->integer($this->mock->getIndex())
                ->isEqualTo(0)
            ->then

            ->if($this->execRun())
            ->then

            ->boolean($this->mock->valid())
                ->isTrue()
            ->integer($this->mock->getIndex())
                ->isEqualTo(0)
            ->string($this->mock->key())
                ->isEqualTo('id')
            ->object($parserObj = $this->mock->current())
                ->isInstanceOf('\BultonFr\Annotation\Parsers\AbstractParser')
            ->string($parserObj->getReflection()->name)
                ->isEqualTo('id')
            ->then

            ->variable($this->mock->next())
                ->isNull()
            ->boolean($this->mock->valid())
                ->isTrue()
            ->integer($this->mock->getIndex())
                ->isEqualTo(1)
            ->string($this->mock->key())
                ->isEqualTo('account')
            ->object($parserObj = $this->mock->current())
                ->isInstanceOf('\BultonFr\Annotation\Parsers\AbstractParser')
            ->string($parserObj->getReflection()->name)
                ->isEqualTo('account')
            ->then

            ->if($this->mock->next()) //name
            ->integer($this->mock->getIndex())
                ->isEqualTo(2)
            ->then

            ->if($this->mock->next()) //parent
            ->integer($this->mock->getIndex())
                ->isEqualTo(3)
            ->then

            ->if($this->mock->next()) //nothing
            ->integer($this->mock->getIndex())
                ->isEqualTo(4)
            ->then

            ->boolean($this->mock->valid())
                ->isFalse()
            ->then

            ->variable($this->mock->rewind())
                ->isNull()
            ->integer($this->mock->getIndex())
                ->isEqualTo(0)
            ->string($this->mock->key())
                ->isEqualTo('id')
        ;
    }

    public function testCountable()
    {
        $this->assert('test Parsers\AbstractManyParser - Countable')
            ->integer($this->mock->count())
                ->isEqualTo(0)
            ->then
            ->if($this->execRun())
            ->integer($this->mock->count())
                ->isEqualTo(4)
        ;
    }

    public function testAddItem()
    {
        $this->assert('test Parsers\AbstractManyParser::addItem')
            ->given($this->execRun())
            ->given($parserClass = '\BultonFr\Annotation\Parsers\AbstractParser')
            ->then

            ->array($itemList = $this->mock->getList())
                ->keys
                    ->isEqualTo(['id', 'account', 'name', 'parent'])
            ->array($this->mock->getItemKeys())
                ->isEqualTo(['id', 'account', 'name', 'parent'])

            ->object($itemList['id'])
                ->isInstanceOf($parserClass)
            ->string($itemList['id']->getReflection()->name)
                ->isEqualTo('id')

            ->object($itemList['account'])
                ->isInstanceOf($parserClass)
            ->string($itemList['account']->getReflection()->name)
                ->isEqualTo('account')

            ->object($itemList['name'])
                ->isInstanceOf($parserClass)
            ->string($itemList['name']->getReflection()->name)
                ->isEqualTo('name')

            ->object($itemList['parent'])
                ->isInstanceOf($parserClass)
            ->string($itemList['parent']->getReflection()->name)
                ->isEqualTo('parent')
        ;
    }

    public function testHasKey()
    {
        $this->assert('test Parsers\AbstractManyParser::hasKey')
            ->boolean($this->mock->hasKey('id'))
                ->isFalse()
            ->then

            ->given($this->execRun())
            ->then

            ->boolean($this->mock->hasKey('id'))
                ->isTrue()
            ->boolean($this->mock->hasKey('unitTest'))
                ->isFalse()
        ;
    }

    public function testObtainForKey()
    {
        $this->assert('test Parsers\AbstractManyParser::obtainForKey')
            ->given($this->execRun())
            ->then

            ->object($this->mock->obtainForKey('id'))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\AbstractParser')
            ->exception(function () {
                $this->mock->obtainForKey('unitTest');
            })
                ->hasCode(TestedClass::EXCEP_KEY_NOT_EXIST)
        ;
    }
}
