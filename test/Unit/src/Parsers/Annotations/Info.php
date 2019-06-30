<?php

namespace BultonFr\Annotation\Test\Unit\Parsers\Annotations;

use atoum;
use BultonFr\Annotation\Parsers\Annotations\Info as TestedClass;
use BultonFr\Annotation\Test\Unit\Helpers\ParserManagerTraits;

class Info extends atoum
{
    protected $mock;

    public function beforeTestMethod($methodName)
    {
        $this->mock = new \mock\BultonFr\Annotation\Parsers\Annotations\Info(
            'Column',
            'type="int"'
        );
    }

    public function testConstruct()
    {
        $this->assert('test Parsers\Annotations\Info::__construct')
            ->given($this->mock = new \mock\BultonFr\Annotation\Parsers\Annotations\Info(
                'Column',
                'type="int"'
            ))
            ->string($this->mock->getName())
                ->isEqualTo('Column')
            ->string($this->mock->getValueStr())
                ->isEqualTo('type="int"')
        ;
    }

    public function testGetterDefaultValues()
    {
        $this->assert('test Parsers\Annotations\Info - getters default values')
            ->array($this->mock->getValues())
                ->isEmpty()
        ;
    }

    public function testSetValueStr()
    {
        $this->assert('test Parsers\Annotations\Info::setValueStr')
            ->string($this->mock->getValueStr())
                ->isEqualTo('type="int"')
            ->object($this->mock->setValueStr('type="int", primary=true'))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getValueStr())
                ->isEqualTo('type="int", primary=true')
        ;
    }

    public function testSetValues()
    {
        $this->assert('test Parsers\Annotations\Info::setValues')
            ->array($this->mock->getValues())
                ->isEmpty()
            ->object($this->mock->setValues([
                'type'    => 'int',
                'primary' => true
            ]))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getValues())
                ->isEqualTo([
                    'type'    => 'int',
                    'primary' => true
                ])
        ;
    }

    public function testConcatValueStr()
    {
        $this->assert('test Parsers\Annotations\Info::concatValueStr')
            ->string($this->mock->getValueStr())
                ->isEqualTo('type="int"')
            ->object($this->mock->concatValueStr('   , primary=true   '))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getValueStr())
                ->isEqualTo('type="int" , primary=true')
        ;
    }

    public function testAddValue()
    {
        $this->assert('test Parsers\Annotations\Info::addValue')
            ->array($this->mock->getValues())
                ->isEmpty()
            ->object($this->mock->addValue('type', 'int'))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getValues())
                ->isEqualTo(['type' => 'int'])
            ->then

            ->object($this->mock->addValue('primary', true))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getValues())
                ->isEqualTo([
                    'type'    => 'int',
                    'primary' => true
                ])
            ->then

            ->object($this->mock->addValue(null, 'unit-test'))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getValues())
                ->isEqualTo([
                    'type'    => 'int',
                    'primary' => true,
                    0         => 'unit-test'
                ])
        ;
    }
}
