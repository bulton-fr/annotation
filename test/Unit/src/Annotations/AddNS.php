<?php

namespace BultonFr\Annotation\Test\Unit\Annotations;

use atoum;
use BultonFr\Annotation\Annotations\AddNS as TestedClass;

class AddNS extends atoum
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
            'AddNS',
            'ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod", alias="HTTPMethod"'
        );
        $this->info
            ->addValue('ns', '\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod')
            ->addValue('alias', 'Method')
        ;

        $this->mockGenerator
            ->makeVisible('parseValue')
            ->makeVisible('checkClassExist')
        ;

        $this->mock = new \mock\BultonFr\Annotation\Annotations\AddNS(
            $this->reader,
            'id',
            $this->info
        );
    }

    public function testGetNs()
    {
        $this->assert('test Annotations\AddNS::getNs')
            ->string($this->mock->getNs())
                ->isEqualTo('\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod')
        ;
    }

    public function testGetAlias()
    {
        $this->assert('test Annotations\AddNS::getAlias')
            ->string($this->mock->getAlias())
                ->isEqualTo('Method')
        ;
    }

    public function testCheckClassExist()
    {
        $this->assert('test Annotations\AddNS::checkClassExist - existing class')
            ->variable($this->mock->checkClassExist())
                ->isNull()
        ;

        $this->assert('test Annotations\AddNS::checkClassExist - not existing class')
            ->given($setNs = function ($newNS) {
                $this->ns = $newNS;
            })
            ->and($setNs = $setNs->bindTo($this->mock, $this->mock))
            ->and($setNs('NotExistingClass'))
            ->then
            ->exception(function () {
                $this->mock->checkClassExist();
            })
                ->hasCode(TestedClass::EXCEP_CLASS_NOT_EXIST)
        ;
    }

    public function testParseValue()
    {
        $this->assert('test Annotations\AddNS::parseValue - prepare')
            ->given($resetProperties = function () {
                $this->alias = '';
                $this->ns    = '';
            })
            ->and($resetProperties = $resetProperties->bindTo($this->mock, $this->mock))
        ;

        $this->assert('test Annotations\AddNS::parseValue - with alias')
            ->given($resetProperties())
            ->string($this->mock->getAlias())
                ->isEmpty()
            ->string($this->mock->getNs())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValue())
                ->isNull()
            ->string($this->mock->getAlias())
                ->isEqualTo('Method')
            ->string($this->mock->getNs())
                ->isEqualTo('\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod')
        ;

        $this->assert('test Annotations\AddNS::parseValue - without alias')
            ->given($resetProperties())
            ->string($this->mock->getAlias())
                ->isEmpty()
            ->string($this->mock->getNs())
                ->isEmpty()
            ->then
            ->if($this->info->setValues([
                'ns' => '\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod'
            ]))
            ->then
            ->variable($this->mock->parseValue())
                ->isNull()
            ->string($this->mock->getAlias())
                ->isEqualTo('HTTPMethod')
            ->string($this->mock->getNs())
                ->isEqualTo('\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod')
        ;
    }
}
