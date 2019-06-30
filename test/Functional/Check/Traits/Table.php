<?php

namespace BultonFr\Annotation\Test\Functional\Check\Traits;

trait Table
{
    protected function checkTableName(string $expectedTableName)
    {
        $tableName = $this->reader->obtainClassAnnotList()['Table'][0]->getName();
        
        $this->check('TableName', ($tableName === $expectedTableName));
    }
}
