<?php

namespace BultonFr\Annotation\Test\Functional\Check;

class Account extends AbstractCheck
{
    use Traits\Column;
    use Traits\Route;

    protected $entityName = 'Account';

    protected function checkClass()
    {
        //No annotation on this class
    }

    protected function checkMethods()
    {
        $methodList = $this->reader->obtainMethodsList();
        $this->check('methodList : nb=1', (count($methodList) === 1));

        $this->checkMethodIndexAction();
    }

    protected function checkMethodIndexAction()
    {
        $methodName = 'indexAction';
        $annotList  = $this->reader->obtainMethodAnnotList($methodName);

        $this->checkMethodIndexActionRoute();

        $this->check(
            'Method '.$methodName.' - check some ignored annotations',
            (
                isset($annotList['param']) === false &&
                isset($annotList['return']) === false &&
                isset($annotList['throws']) === false
            )
        );
    }

    protected function checkMethodIndexActionRoute()
    {
        $methodName = 'indexAction';
        $annotList  = $this->checkMethodAnnotIsset($methodName);

        $routeList = $this->checkMethodAnnotList(
            $methodName,
            $annotList,
            'Route',
            2
        );

        $this->checkRoute(
            $routeList[0],
            'Method '.$methodName.' - route #0 - ',
            'my-path',
            '/my-path/'
        );

        $this->checkRoute(
            $routeList[1],
            'Method '.$methodName.' - route #1 - ',
            'my-path_index',
            '/my-path/index'
        );
    }

    protected function checkProperties()
    {
        $propertiesList = $this->reader->obtainPropertiesList();
        $this->check('propertiesList : nb=3', (count($propertiesList) === 3));

        $this->checkProperty(
            'id',
            ['type' => 'int', 'primary' => true]
        );
        $this->checkProperty(
            'name',
            ['type' => 'string']
        );
        $this->checkProperty(
            'currentValue',
            ['type' => 'float', 'name' => 'current_value']
        );
    }

    protected function checkProperty($propertyName, $columnAttr)
    {
        $propertyAnnotList = $this->checkPropertyIsset($propertyName);

        $this->checkColumn($propertyAnnotList, $propertyName, $columnAttr);
    }
}
