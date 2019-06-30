<?php

namespace BultonFr\Annotation\Test\Unit\Parsers\Annotations;

use atoum;
use BultonFr\Annotation\Parsers\Annotations\Reader as TestedClass;
use BultonFr\Annotation\Parsers\Annotations\Info;

class Reader extends atoum
{
    protected $mock;

    public function beforeTestMethod($methodName)
    {
        $this->mockGenerator
            ->makeVisible('findAnnotations')
            ->makeVisible('newAnnotation')
            ->makeVisible('parseAnnotation')
            ->makeVisible('parseValue')
            ->makeVisible('parseValueObject')
            ->makeVisible('parseValueData')
        ;

        $this->mock = new \mock\BultonFr\Annotation\Parsers\Annotations\Reader;
    }

    public function testGetterDefaultValues()
    {
        $this->assert('test Parsers\Annotations\Reader - getters default values')
            ->array($this->mock->getAnnotationList())
                ->isEmpty()
            ->array(TestedClass::getIgnoredAnnotations())
                ->isEqualTo([
                    'api',
                    'author',
                    'category',
                    'copyright',
                    'deprecated',
                    'example',
                    'filesource',
                    'global',
                    'ignore',
                    'internal',
                    'license',
                    'link',
                    'method',
                    'package',
                    'param',
                    'property',
                    'property-read',
                    'property-write',
                    'return',
                    'see',
                    'since',
                    'source',
                    'subpackage',
                    'throws',
                    'todo',
                    'uses',
                    'used-by',
                    'var',
                    'version'
                ])
        ;
    }

    public function testAddIgnoredAnnotation()
    {
        $this->assert('test Parsers\Annotations\Reader::addIgnoredAnnotation')
            ->array(TestedClass::getIgnoredAnnotations())
                ->notContains('unitTest')
            ->variable(TestedClass::addIgnoredAnnotation('unitTest'))
                ->isNull()
            ->array(TestedClass::getIgnoredAnnotations())
                ->contains('unitTest')
        ;
    }

    protected function obtainNextYieldValue($generator)
    {
        $yieldInfo = $generator->yields;

        $getLastValue = function () {
            return $this->lastYieldValue;
        };
        $getLastValue = $getLastValue->bindTo($yieldInfo, $yieldInfo);

        return $getLastValue();
    }

    public function testFindAnnotations()
    {
        $this->assert('test Parsers\Annotations\Reader::findAnnotations - simple annotation')
            ->given($generator = $this->generator(
                $this->mock->findAnnotations(
                    '/**'."\n"
                    .' * @AddNS(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")'."\n"
                    .' */'
                )
            ))
            ->object($annotInfo = $this->obtainNextYieldValue($generator))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->string($annotInfo->getName())
                ->isEqualTo('AddNS')
            ->string($annotInfo->getValueStr())
                ->isEqualTo('(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")')
        ;

        $this->assert('test Parsers\Annotations\Reader::findAnnotations - multi-line annotation')
            ->given($generator = $this->generator(
                $this->mock->findAnnotations(
                    '/**'."\n"
                    .' * @AddEntity('."\n"
                    .' *  ns="\BultonFr\Annotation\Test\Functional\Ref\Category",'."\n"
                    .' *  alias="Ref\Category"'."\n"
                    .' * )'."\n"
                    .' */'
                )
            ))
            ->object($annotInfo = $this->obtainNextYieldValue($generator))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->string($annotInfo->getName())
                ->isEqualTo('AddEntity')
            ->string($annotInfo->getValueStr())
                ->isEqualTo('( ns="\BultonFr\Annotation\Test\Functional\Ref\Category", alias="Ref\Category" )')
        ;

        $this->assert('test Parsers\Annotations\Reader::findAnnotations - many annotations')
            ->given($generator = $this->generator(
                $this->mock->findAnnotations(
                    '/**'."\n"
                    .' * @AddNS(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")'."\n"
                    .' * @AddEntity('."\n"
                    .' *  ns="\BultonFr\Annotation\Test\Functional\Ref\Category",'."\n"
                    .' *  alias="Ref\Category"'."\n"
                    .' * )'."\n"
                    .' */'
                )
            ))
            ->then

            ->object($annotInfo = $this->obtainNextYieldValue($generator))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->string($annotInfo->getName())
                ->isEqualTo('AddNS')
            ->string($annotInfo->getValueStr())
                ->isEqualTo('(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")')
            ->then

            ->object($annotInfo = $this->obtainNextYieldValue($generator))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->string($annotInfo->getName())
                ->isEqualTo('AddEntity')
            ->string($annotInfo->getValueStr())
                ->isEqualTo('( ns="\BultonFr\Annotation\Test\Functional\Ref\Category", alias="Ref\Category" )')
        ;
    }

    protected function testNewAnnotation()
    {
        $this->assert('test Parsers\Annotations\Reader::newAnnotation')
            ->object($annotInfo = $this->mock->newAnnotation([
                0 => '* @AddNS(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")',
                1 => ' ',
                2 => 'AddNS',
                3 => '(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")'
            ]))
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->string($annotInfo->getName())
                ->isEqualTo('AddNS')
            ->string($annotInfo->getValueStr())
                ->isEqualTo('(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")')
        ;
    }

    public function testParseValueData()
    {
        $this->assert('test Parsers\Annotations\Reader::parseValueData - string')
            ->string($this->mock->parseValueData('"int"'))
                ->isEqualTo('int')
            ->string($this->mock->parseValueData('\'int\''))
                ->isEqualTo('int')
            ->integer($this->mock->parseValueData('int'))
                ->isEqualTo(0)
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueData - null')
            ->variable($this->mock->parseValueData('null'))
                ->isNull()
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueData - true')
            ->boolean($this->mock->parseValueData('true'))
                ->isTrue()
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueData - false')
            ->boolean($this->mock->parseValueData('false'))
                ->isFalse()
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueData - float')
            ->float($this->mock->parseValueData('37.5'))
                ->isEqualTo(37.5)
            ->integer($this->mock->parseValueData('37,5'))
                ->isEqualTo(37)
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueData - int')
            ->integer($this->mock->parseValueData('42'))
                ->isEqualTo(42)
        ;
    }

    public function testParseValueObject()
    {
        $this->assert('test Parsers\Annotations\Reader::parseValueObject - no value')
            ->given($info = new Info(
                'test',
                ''
            ))
            ->array($info->getValues())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValueObject($info))
                ->isNull()
            ->array($info->getValues())
                ->isEmpty()
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueObject - one value')
            ->given($info = new Info(
                'test',
                '(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")'
            ))
            ->array($info->getValues())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValueObject($info))
                ->isNull()
            ->array($info->getValues())
                ->isEqualTo([
                    'ns' => '\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod'
                ])
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueObject - one value with comma')
            ->given($info = new Info(
                'test',
                '(values="A,B")'
            ))
            ->array($info->getValues())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValueObject($info))
                ->isNull()
            ->array($info->getValues())
                ->isEqualTo([
                    'values' => 'A,B'
                ])
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValueObject - may values')
            ->given($info = new Info(
                'test',
                '(type="enum", values="A,B", index=true)'
            ))
            ->array($info->getValues())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValueObject($info))
                ->isNull()
            ->array($info->getValues())
                ->isEqualTo([
                    'type'   => 'enum',
                    'values' => 'A,B',
                    'index'  => true
                ])
        ;
    }

    public function testParseValue()
    {
        $this->assert('test Parsers\Annotations\Reader::parseValue - complexe value')
            ->given($info = new Info(
                'test',
                '(type="enum", values="A,B", index=true)'
            ))
            ->array($info->getValues())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValue($info))
                ->isNull()
            ->array($info->getValues())
                ->isEqualTo([
                    'type'   => 'enum',
                    'values' => 'A,B',
                    'index'  => true
                ])
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValue - simple string value')
            ->given($info = new Info(
                'test',
                'hi atoum !'
            ))
            ->array($info->getValues())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValue($info))
                ->isNull()
            ->array($info->getValues())
                ->isEqualTo([
                    0 => 'hi atoum !'
                ])
        ;

        $this->assert('test Parsers\Annotations\Reader::parseValue - simple int value')
            ->given($info = new Info(
                'test',
                '42'
            ))
            ->array($info->getValues())
                ->isEmpty()
            ->then
            ->variable($this->mock->parseValue($info))
                ->isNull()
            ->array($info->getValues())
                ->isEqualTo([
                    0 => 42
                ])
        ;
    }

    public function testParseAnnotation()
    {
        $this->assert('test Parsers\Annotations\Reader::parseAnnotation')
            ->given($infoHttpMethod = new Info(
                'AddNS',
                '(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")'
            ))
            ->array($this->mock->getAnnotationList())
                ->isEmpty()
            ->then

            ->variable($this->mock->parseAnnotation($infoHttpMethod))
                ->isNull()
            ->array($this->mock->getAnnotationList())
                ->isEqualTo([
                    'AddNS' => [
                        0 => $infoHttpMethod
                    ]
                ])
            ->then

            ->given($infoRoute = new Info(
                'AddNS',
                '(ns="\BultonFr\Annotation\Test\Functional\Annotations\Route")'
            ))
            ->then

            ->variable($this->mock->parseAnnotation($infoRoute))
                ->isNull()
            ->array($this->mock->getAnnotationList())
                ->isEqualTo([
                    'AddNS' => [
                        0 => $infoHttpMethod,
                        1 => $infoRoute
                    ]
                ])
            ->then

            ->given($infoTable = new Info(
                'Table',
                '(name="unit_test")'
            ))
            ->then

            ->variable($this->mock->parseAnnotation($infoTable))
                ->isNull()
            ->array($this->mock->getAnnotationList())
                ->isEqualTo([
                    'AddNS' => [
                        0 => $infoHttpMethod,
                        1 => $infoRoute
                    ],
                    'Table' => [
                        0 => $infoTable
                    ]
                ])
        ;

        $this->assert('test Parsers\Annotations\Reader::parseAnnotation - with ignored annotation')
            ->given($infoTable = new Info(
                'param',
                'Info $annotationObj'
            ))
            ->then

            ->variable($this->mock->parseAnnotation($infoTable))
                ->isNull()
            ->array($this->mock->getAnnotationList())
                ->isNotEmpty() //Previously values
                ->hasKeys(['AddNS', 'Table'])
                ->notHasKey('param')
        ;
    }

    public function testParse()
    {
        $this->assert('test Parsers\Annotations\Reader::parse')
            ->variable($this->mock->parse(
                '/**'."\n"
                .' * @AddNS(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")'."\n"
                .' * @AddEntity('."\n"
                .' *  ns="\BultonFr\Annotation\Test\Functional\Ref\Category",'."\n"
                .' *  alias="Ref\Category"'."\n"
                .' * )'."\n"
                .' * @AddNS(ns="\BultonFr\Annotation\Test\Functional\Annotations\Route")'."\n"
                .' * @package myTest.'."\n"
                .' */'
            ))
                ->isNull()
            ->array($annotationList = $this->mock->getAnnotationList())
                ->isNotEmpty()
                ->hasKeys(['AddNS', 'AddEntity'])
                ->notHasKey('param')
            ->then

            ->array($addImportedNS = $annotationList['AddNS'])
                ->size
                    ->isEqualTo(2)
            ->object($addImportedNS[0])
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->array($addImportedNS[0]->getValues())
                ->isEqualTo([
                    'ns' => '\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod'
                ])
            ->object($addImportedNS[1])
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->array($addImportedNS[1]->getValues())
                ->isEqualTo([
                    'ns' => '\BultonFr\Annotation\Test\Functional\Annotations\Route'
                ])
            ->then

            ->array($addEntity = $annotationList['AddEntity'])
                ->size
                    ->isEqualTo(1)
            ->object($addEntity[0])
                ->isInstanceOf('\BultonFr\Annotation\Parsers\Annotations\Info')
            ->array($addEntity[0]->getValues())
                ->isEqualTo([
                    'alias' => 'Ref\Category',
                    'ns'    => '\BultonFr\Annotation\Test\Functional\Ref\Category'
                ])
        ;
    }
}
