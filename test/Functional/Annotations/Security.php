<?php

namespace BultonFr\Annotation\Test\Functional\Annotations;

use BultonFr\Annotation\Annotations\AbstractAnnotation;

class Security extends AbstractAnnotation
{
    protected $fct;
    protected $role;
    
    public function getFct()
    {
        return $this->fct;
    }
    
    public function getRole()
    {
        return $this->role;
    }

    protected function parseValue()
    {
        $this->detectFct();
        $this->detectRole();
    }
    
    protected function detectFct()
    {
        if ($this->hasValueKey('fct') === true) {
            $this->fct = (string) $this->obtainValueKey('fct');
        }
    }
    
    protected function detectRole()
    {
        if ($this->hasValueKey('role') === true) {
            $this->role = (string) $this->obtainValueKey('role');
        }
    }
}
