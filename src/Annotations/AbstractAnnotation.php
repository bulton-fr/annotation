<?php

namespace BultonFr\Annotation\Annotations;

use Exception;

use BultonFr\Annotation\Parsers\Annotations\Info;
use BultonFr\Annotation\Reader;

/**
 * Abstract class for all annotation dedicated object
 *
 * @package BultonFr\Annotation
 */
abstract class AbstractAnnotation
{
    /**
     * @const EXCEP_KEY_NOT_EXIST Exception code if a asked attribute not exist
     *
     * @see README.md for code format
     */
    const EXCEP_KEY_NOT_EXIST = 201001;
    
    /**
     * @var Reader The main Reader instance
     */
    protected $reader;
    
    /**
     * The name of the item which belongs annotations
     *
     * @var string
     */
    protected $itemName;
    
    /**
     * @var \BultonFr\Annotation\Parsers\Annotations\Info The annotation info
     * object which provide data about the annotation
     */
    protected $info;
    
    /**
     * Construct
     *
     * @param Reader $reader
     * @param string $itemName
     * @param Info $Info
     */
    public function __construct(
        Reader $reader,
        string $itemName,
        Info $info
    ) {
        $this->reader   = $reader;
        $this->itemName = $itemName;
        $this->info     = $info;
        
        $this->parseValue();
    }
    
    /**
     * Get the main Reader instance
     *
     * @return Reader
     */
    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * Get the name of the item which belongs annotations
     *
     * @return void
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Get the annotation info object which provide data about the annotation
     *
     * @return Info
     */
    public function getInfo(): Info
    {
        return $this->info;
    }

    /**
     * Parse values to populate properties
     *
     * @return void
     */
    abstract protected function parseValue();
    
    /**
     * Obtain the value of a key/attribute
     *
     * @param string $askedKey The asked key/attribute
     *
     * @return mixed
     *
     * @throws Exception If the key not exist
     */
    protected function obtainValueKey(string $askedKey)
    {
        $valueList = $this->info->getValues();
        
        if (array_key_exists($askedKey, $valueList) === false) {
            throw new Exception(
                'The key value '.$askedKey.' not exist into the annotation '.$this->info->getName(),
                self::EXCEP_KEY_NOT_EXIST
            );
        }
        
        return $valueList[$askedKey];
    }
    
    /**
     * Check if a key/attribute exist
     *
     * @param string $askedKey The asked key/attribute to check
     *
     * @return boolean
     */
    protected function hasValueKey(string $askedKey): bool
    {
        $valueList = $this->info->getValues();
        
        return array_key_exists($askedKey, $valueList);
    }
}
