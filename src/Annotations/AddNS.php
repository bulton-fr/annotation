<?php

namespace BultonFr\Annotation\Annotations;

use Exception;
use ReflectionClass;

/**
 * The dedicated object for AddNS annotation
 */
class AddNS extends AbstractAnnotation
{
    /**
     * @const EXCEP_CLASS_NOT_EXIST Exception code if the class to import
     * not exist
     *
     * @see README.md for code format
     */
    const EXCEP_CLASS_NOT_EXIST = 202001;
    
    /**
     * The value of attribute "ns".
     * It's the full namespace of the class
     *
     * @var string
     */
    protected $ns = '';
    
    /**
     * The value of the attribute "alias"
     * It's the annotation name to use (after the @)
     * If not declared, it's the class name
     *
     * @var string
     */
    protected $alias = '';
    
    /**
     * Get the value of attribute "ns".
     * It's the full namespace of the class
     *
     * @return string
     */
    public function getNs(): string
    {
        return $this->ns;
    }

    /**
     * Get the value of the attribute "alias"
     * It's the annotation name to use (after the @)
     * If not declared, it's the class name
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }
    
    /**
     * {@inheritDoc}
     * Obtain the ns and alias values
     */
    protected function parseValue()
    {
        if ($this->hasValueKey('alias') === false) {
            $this->ns = $this->obtainValueKey('ns');
        } else {
            $this->ns    = $this->obtainValueKey('ns');
            $this->alias = $this->obtainValueKey('alias');
        }
        
        $this->checkClassExist();
        
        if (empty($this->alias)) {
            $reflection  = new ReflectionClass($this->ns);
            $this->alias = $reflection->getShortName();
        }
    }
    
    /**
     * Check if the declared class on "ns" exist
     *
     * @return void
     *
     * @throws Exception If the class no exist
     */
    protected function checkClassExist()
    {
        if (class_exists($this->ns) === false) {
            throw new Exception(
                'Class '.$this->ns.' not exist.',
                self::EXCEP_CLASS_NOT_EXIST
            );
        }
    }
}
