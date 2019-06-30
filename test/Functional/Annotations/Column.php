<?php

namespace BultonFr\Annotation\Test\Functional\Annotations;

use Exception;
use BultonFr\Annotation\Annotations\AbstractAnnotation;

class Column extends AbstractAnnotation
{
    protected $name;
    
    protected $type;
    
    protected $primary = false;
    
    protected $nullable = false;
    
    protected $entity;
    
    protected $values;
    
    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function getNullable()
    {
        return $this->nullable;
    }

    public function getEntity()
    {
        return $this->entity;
    }
    
    public function getValues()
    {
        return $this->values;
    }
        
    protected function parseValue()
    {
        $this->detectType();
        $this->detectPrimary();
        $this->detectNullable();
        $this->detectEntity();
        $this->detectValues();
        $this->detectName();
    }
    
    protected function detectType()
    {
        if ($this->hasValueKey('type') === false) {
            throw new Exception(
                'The type key must be define into the annotation'
                .' for '.$this->itemName.' in entity '.$this->reader->getReflection()->getName()
            );
        }
        
        $this->type = $this->obtainValueKey('type');
    }
    
    protected function detectPrimary()
    {
        if ($this->hasValueKey('primary') === true) {
            $this->primary = $this->obtainValueKey('primary');
        }
    }
    
    protected function detectNullable()
    {
        if ($this->hasValueKey('nullable') === true) {
            $this->nullable = (bool) $this->obtainValueKey('nullable');
        }
    }
    
    protected function detectEntity()
    {
        if ($this->hasValueKey('entity') === false) {
            return;
        }
        
        $this->entity = (string) $this->obtainValueKey('entity');
        $entityClass  = $this->entity;
        
        $annotClass = $this->reader->obtainClassAnnotList();
        
        if (array_key_exists('AddEntity', $annotClass)) {
            if (array_key_exists($this->entity, $annotClass['AddEntity'])) {
                $entityClass = $annotClass['AddEntity'][$this->entity]->getNs();
            }
        }

        if (class_exists($entityClass) === false) {
            throw new Exception('Type entity class '.$entityClass.' not exist');
        }
    }
    
    protected function detectValues()
    {
        if ($this->hasValueKey('values') === true) {
            $this->values = $this->obtainValueKey('values');
        }
    }
    
    protected function detectName()
    {
        if ($this->hasValueKey('name') === true) {
            $this->name = (string) $this->obtainValueKey('name');
            return;
        }
        
        if ($this->primary === true) {
            $this->name = 'id';
            return;
        }
        
        if ($this->entity !== null) {
            $this->name = strtolower($this->entity).'_id';
            return;
        }
        
        $this->name = strtolower($this->itemName);
    }
}
