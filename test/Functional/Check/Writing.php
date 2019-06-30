<?php

namespace BultonFr\Annotation\Test\Functional\Check;

class Writing extends AbstractCheck
{
    use Traits\AddEntity;
    use Traits\Column;
    use Traits\Table;

    protected $entityName = 'Writing';

    protected function checkClass()
    {
        $this->checkTableName('writing_list');

        $classAnnotList = $this->reader->obtainClassAnnotList();
        $this->check('AddEntity : exist', isset($classAnnotList['AddEntity']));

        $addEntityList = $classAnnotList['AddEntity'];
        $this->check('AddEntity : nb=2', (count($addEntityList) === 2));

        $this->checkAddEntity(
            'Account',
            $addEntityList,
            "\BultonFr\Annotation\Test\Functional\Ref\Account",
            "Account"
        );
        
        $this->checkAddEntity(
            'Category',
            $addEntityList,
            "\BultonFr\Annotation\Test\Functional\Ref\Category",
            "Category"
        );
    }

    protected function checkMethods()
    {
        //No method in the ref class
    }

    protected function checkProperties()
    {
        $propertiesList = $this->reader->obtainPropertiesList();
        $this->check('propertiesList : nb=8', (count($propertiesList) === 8));

        $this->checkProperty(
            'id',
            ['type' => 'int', 'primary' => true]
        );
        $this->checkProperty(
            'account',
            ['type' => 'entity', 'entity' => 'Account', 'name' => 'account_id']
        );
        $this->checkProperty(
            'category',
            ['type' => 'entity', 'entity' => 'Category', 'name' => 'category_id']
        );
        $this->checkProperty(
            'type',
            ['type' => 'enum', 'values' => 'A,B']
        );
        $this->checkProperty(
            'date',
            ['type' => 'datetime']
        );
        $this->checkProperty(
            'realDate',
            ['type' => 'datetime', 'name' => 'realdate']
        );
        $this->checkProperty(
            'label',
            ['type' => 'string']
        );
        $this->checkProperty(
            'amount',
            ['type' => 'float']
        );
    }

    protected function checkProperty($propertyName, $columnAttr)
    {
        $propertyAnnotList = $this->checkPropertyIsset($propertyName);

        $this->checkColumn($propertyAnnotList, $propertyName, $columnAttr);
    }
}
