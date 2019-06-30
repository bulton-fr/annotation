<?php

namespace BultonFr\Annotation\Test\Unit\Annotations;

use atoum;
use BultonFr\Annotation\Annotations\AbstractAnnotation as TestedClass;

class AbstractAnnotation extends atoum
{
    protected $reader;
    protected $info;
    protected $mock;

    public function beforeTestMethod($methodName)
    {
        $this->reader = new \mock\BultonFr\Annotation\Reader(
            '\BultonFr\Annotation\Test\Functional\Ref\Category'
        );

        $this->info = new \mock\BultonFr\Annotation\Parsers\Annotations\Info(
            'Column',
            'type="int", primary=true'
        );
        $this->info
            ->addValue('type', 'int')
            ->addValue('primary', true)
        ;

        $this->mockGenerator
            ->makeVisible('hasValueKey')
            ->makeVisible('obtainValueKey')
        ;

        $this->mock = new \mock\BultonFr\Annotation\Annotations\AbstractAnnotation(
            $this->reader,
            'id',
            $this->info
        );
    }

    public function testConstruct()
    {
        $this->assert('test Annotations\AbstractAnnotation::__construct')
            ->given($this->mock = new \mock\BultonFr\Annotation\Annotations\AbstractAnnotation(
                $this->reader,
                'id',
                $this->info
            ))
            ->object($this->mock->getReader())
                ->isIdenticalTo($this->reader)
            ->string($this->mock->getItemName())
                ->isEqualTo('id')
            ->object($this->mock->getInfo())
                ->isIdenticalTo($this->info)
            ->mock($this->mock)
                ->call('parseValue')
                    ->once()
        ;
    }

    public function testHasValueKey()
    {
        $this->assert('test Annotations\AbstractAnnotation::hasValueKey')
            ->boolean($this->mock->hasValueKey('type'))
                ->isTrue()
            ->boolean($this->mock->hasValueKey('primary'))
                ->isTrue()
            ->boolean($this->mock->hasValueKey('name'))
                ->isFalse()
        ;
    }

    public function testObtainValueKey()
    {
        $this->assert('test Annotations\AbstractAnnotation::obtainValueKey')
            ->string($this->mock->obtainValueKey('type'))
                ->isEqualTo('int')
            ->boolean($this->mock->obtainValueKey('primary'))
                ->isTrue()
            ->exception(function () {
                $this->mock->obtainValueKey('name');
            })
                ->hasCode(TestedClass::EXCEP_KEY_NOT_EXIST)
        ;
    }
}
