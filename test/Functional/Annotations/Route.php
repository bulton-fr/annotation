<?php

namespace BultonFr\Annotation\Test\Functional\Annotations;

use BultonFr\Annotation\Annotations\AbstractAnnotation;

class Route extends AbstractAnnotation
{
    protected $name;
    protected $path;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getPath()
    {
        return $this->path;
    }

    protected function parseValue()
    {
        $this->detectName();
        $this->detectPath();
    }
    
    protected function detectName()
    {
        if ($this->hasValueKey('name') === true) {
            $this->name = (string) $this->obtainValueKey('name');
        }
    }
    
    protected function detectPath()
    {
        if ($this->hasValueKey('path') === true) {
            $this->path = (string) $this->obtainValueKey('path');
        }
    }
}
