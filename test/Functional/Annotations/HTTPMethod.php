<?php

namespace BultonFr\Annotation\Test\Functional\Annotations;

use BultonFr\Annotation\Annotations\AbstractAnnotation;

class HTTPMethod extends AbstractAnnotation
{
    protected $methods;
    
    public function getMethods()
    {
        return $this->methods;
    }

    protected function parseValue()
    {
        $this->detectMethods();
    }
    
    protected function detectMethods()
    {
        if ($this->hasValueKey('methods') === true) {
            $this->methods = (string) $this->obtainValueKey('methods');
        }
    }
}
