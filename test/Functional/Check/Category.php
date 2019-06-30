<?php

namespace BultonFr\Annotation\Test\Functional\Check;

class Category extends AbstractCheck
{
    use Traits\AddEntity;
    use Traits\Column;
    use Traits\HTTPMethod;
    use Traits\Route;
    use Traits\Security;
    
    protected $entityName = 'Category';

    protected function checkClass()
    {
        $classAnnotList = $this->reader->obtainClassAnnotList();
        $this->check('AddEntity : exist', isset($classAnnotList['AddEntity']));

        $addEntityList = $classAnnotList['AddEntity'];
        $this->check('AddEntity : nb=1', (count($addEntityList) === 1));

        $this->checkAddEntity(
            'Ref\Category',
            $addEntityList,
            "\BultonFr\Annotation\Test\Functional\Ref\Category",
            "Ref\Category"
        );

        //AddNS is not added to the list
        $this->check('AddNS : not exist', !isset($classAnnotList['AddNS']));
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

        $this->checkMethodIndexActionHTTPMethod();
        $this->checkMethodIndexActionRoute();
        $this->checkMethodIndexActionSecurity();

        $this->check(
            'Method '.$methodName.' - check some ignored annotations',
            (isset($annotList['param']) === false)
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
            1
        );

        $this->checkRoute(
            $routeList[0],
            'Method '.$methodName.' - route #0 - ',
            'category',
            '/category'
        );
    }

    protected function checkMethodIndexActionHTTPMethod()
    {
        $methodName = 'indexAction';
        $annotList  = $this->checkMethodAnnotIsset($methodName);

        $httpMethodList = $this->checkMethodAnnotList(
            $methodName,
            $annotList,
            'HTTPMethod',
            1
        );

        $this->checkHTTPMethod(
            $httpMethodList[0],
            'Method '.$methodName.' - method #0 - ',
            'GET'
        );
    }

    protected function checkMethodIndexActionSecurity()
    {
        $methodName = 'indexAction';
        $annotList  = $this->checkMethodAnnotIsset($methodName);

        $securityList = $this->checkMethodAnnotList(
            $methodName,
            $annotList,
            'Security',
            1
        );

        $this->checkSecurity(
            $securityList[0],
            'Method '.$methodName.' - Security #0 - ',
            'mySecurity',
            'ADMIN'
        );

        $annotValues = $securityList[0]->getInfo()->getValues();
        $this->check(
            'Method '.$methodName.' - Security #0 - check if test attribute exist',
            isset($annotValues['test'])
        );
        $this->check(
            'Method '.$methodName.' - Security #0 - check test attribute value',
            ($annotValues['test'] === 'not-to-be-here')
        );
    }

    protected function checkProperties()
    {
        $propertiesList = $this->reader->obtainPropertiesList();
        $this->check('propertiesList : nb=4', (count($propertiesList) === 4));

        $this->checkProperty(
            'id',
            ['type' => 'int', 'primary' => true]
        );
        $this->checkProperty(
            'account',
            [
                'type'   => 'entity',
                'entity' => '\BultonFr\Annotation\Test\Functional\Ref\Account',
                'name'   => '\bultonfr\annotation\test\functional\ref\account_id'
            ]
        );
        $this->checkProperty(
            'name',
            ['type' => 'string']
        );
        $this->checkProperty(
            'parent',
            [
                'type'   => 'entity',
                'entity' => 'Ref\Category',
                'name'   => 'parent_id'
            ]
        );
    }

    protected function checkProperty($propertyName, $columnAttr)
    {
        $propertyAnnotList = $this->checkPropertyIsset($propertyName);

        $this->checkColumn($propertyAnnotList, $propertyName, $columnAttr);
    }
}
