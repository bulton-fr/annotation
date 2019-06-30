<?php

namespace BultonFr\Annotation\Parsers\Annotations;

/**
 * All info about an annotation.
 *
 * This class is used by Parsers\Annotations\Reader to save all data about an
 * annotation. While dev, it was a anonymous class. But I move it to a real
 * class to improve performences (definition is the more longer).
 *
 * @package BultonFr\Annotation
 */
class Info
{
    /**
     * The annotation name (the part just after the @)
     *
     * @var string
     */
    protected $name = '';

    /**
     * The annotation values on the string format.
     * (like writed into the class, without line-break)
     * It's the part after the annotation name.
     *
     * @var string
     */
    protected $valueStr = '';

    /**
     * All values parsed.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Construct
     *
     * @param string $name
     * @param string $valueStr
     */
    public function __construct(string $name, string $valueStr)
    {
        $this->name     = $name;
        $this->valueStr = trim($valueStr);
    }
    
    /**
     * Get the annotation name (the part just after the @)
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the annotation values on the string format.
     * (like writed into the class, without line-break)
     * It's the part after the annotation name.
     *
     * @return string
     */
    public function getValueStr(): string
    {
        return $this->valueStr;
    }

    /**
     * Get all values parsed.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Set the annotation values on the string format.
     * (like writed into the class, without line-break)
     * It's the part after the annotation name.
     *
     * @param string $valueStr
     *
     * @return self
     */
    public function setValueStr(string $valueStr): self
    {
        $this->valueStr = $valueStr;
        return $this;
    }

    /**
     * Set all values parsed.
     *
     * @param array $values
     *
     * @return self
     */
    public function setValues(array $values): self
    {
        $this->values = $values;
        return $this;
    }
    
    /**
     * Add a value at the end of valueStr.
     * The new value will be added with a space at the beggining
     *
     * @param string $addedValue
     *
     * @return self
     */
    public function concatValueStr(string $addedValue): self
    {
        $addedValue = trim($addedValue);
        
        if (empty($addedValue) === false) {
            $this->valueStr .= ' '.$addedValue;
        }
        
        return $this;
    }
    
    /**
     * Add a new value to $values
     *
     * @param string|null $valueName The value attribute name
     * @param mixed $value
     *
     * @return self
     */
    public function addValue(?string $valueName, $value): self
    {
        if ($valueName === null) {
            $this->values[] = $value;
        } else {
            $this->values[$valueName] = $value;
        }

        return $this;
    }
}
