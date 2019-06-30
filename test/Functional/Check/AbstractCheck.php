<?php

namespace BultonFr\Annotation\Test\Functional\Check;

use Exception;
use BultonFr\Annotation\Reader;
use bultonFr\Utils\Cli\BasicMsg;

abstract class AbstractCheck
{
    const FCT_TEST_NS = '\BultonFr\Annotation\Test\Functional';

    protected $entityName = '';
    protected $reader;

    public function __construct()
    {
        $this->reader = new Reader(static::FCT_TEST_NS.'\Ref\\'.$this->entityName);

        $this->reader->getParserManager()->addImportedNS(
            static::FCT_TEST_NS.'\Annotations\Column',
            'Column'
        );
        $this->reader->getParserManager()->addImportedNS(
            static::FCT_TEST_NS.'\Annotations\Table',
            'Table'
        );
        $this->reader->getParserManager()->addImportedNS(
            static::FCT_TEST_NS.'\Annotations\AddEntity',
            'AddEntity'
        );
        $this->reader->getParserManager()->addImportedNS(
            static::FCT_TEST_NS.'\Annotations\Route',
            'Route'
        );

        $this->reader->parse();
    }

    public function runTests()
    {
        $this->checkClass();
        $this->checkMethods();
        $this->checkProperties();
    }

    abstract protected function checkClass();
    abstract protected function checkMethods();
    abstract protected function checkProperties();

    protected function check($checkName, $checkReturn)
    {
        if (is_callable($checkReturn)) {
            $checkReturn = $checkReturn();
        }

        BasicMsg::displayMsg('>> Exec test : '.$checkName.' ... ');

        if ($checkReturn === false) {
            BasicMsg::displayMsgNL('FAIL', 'red', 'bold');
            throw new Exception('Check error : '.$checkName);
        }

        BasicMsg::displayMsgNL('OK', 'green', 'bold');
    }

    protected function checkMethodAnnotIsset($methodName)
    {
        try {
            $methodAnnotList = $this->reader->obtainMethodAnnotList($methodName);
        } catch (Exception $e) {
            $this->check('Method '.$methodName.' : not exist', false);
            //Throw exception
        }

        return $methodAnnotList;
    }

    protected function checkMethodAnnotList(
        $methodName,
        $annotList,
        $annotName,
        $nbAnnot
    ) {
        $this->check(
            'Method '.$methodName.' - check annotation '.$annotName,
            isset($annotList[$annotName])
        );

        $annotItemList = $annotList[$annotName];

        $this->check(
            'Method '.$methodName.' - nb '.$annotName.' annotation = '.$nbAnnot,
            (count($annotItemList) === $nbAnnot)
        );

        return $annotItemList;
    }

    protected function checkPropertyIsset($propertyName)
    {
        try {
            $propertyAnnotList = $this->reader->obtainPropertyAnnotList($propertyName);
        } catch (Exception $e) {
            $this->check('Property '.$propertyName.' : not exist', false);
            //Throw exception
        }

        return $propertyAnnotList;
    }
}
