<?php

namespace BultonFr\Annotation\Test\Functional\Annotations;

use BultonFr\Annotation\Annotations\AbstractAnnotation;

class Table extends AbstractAnnotation
{
    protected $name;
    
    public function getName()
    {
        return $this->name;
    }

    protected function parseValue()
    {
        $this->detectName();
    }
    
    protected function detectName()
    {
        if ($this->hasValueKey('name') === true) {
            $this->name = (string) $this->obtainValueKey('name');
        }
    }
}
