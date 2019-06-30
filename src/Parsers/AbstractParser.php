<?php

namespace BultonFr\Annotation\Parsers;

use Exception;
use Reflector;
use BultonFr\Annotation\Annotations\AbstractAnnotation;
use BultonFr\Annotation\ParserManager;
use BultonFr\Annotation\Parsers\Annotations\Info;
use BultonFr\Annotation\Parsers\Annotations\Reader as AnnotReader;

/**
 * Abstract class for all classes which parse a part of a class
 * to find annotation into it.
 *
 * @package BultonFr\Annotation
 */
abstract class AbstractParser
{
    /**
     * @const EXCEP_CLASS_NOT_FOUND Exception code if the class for an
     * annotation is not found
     *
     * @see README.md for code format
     */
    const EXCEP_CLASS_NOT_FOUND = 302001;

    /**
     * @const EXCEP_NO_EXTENDS_ABSTRACT_ANNOTATION Exception code if the
     * annotation object not have AbstractAnnotation into his parents.
     *
     * @see README.md for code format
     */
    const EXCEP_NO_EXTENDS_ABSTRACT_ANNOTATION = 302002;

    /**
     * The parser manager system
     *
     * @var \BultonFr\Annotation\ParserManager
     */
    protected $parserManager;

    /**
     * The Reflection object for the readed item
     *
     * @var \Reflector
     */
    protected $reflection;

    /**
     * The annotation reader system
     *
     * @var \BultonFr\Annotation\Parsers\Annotations\Reader|null
     */
    protected $annotReader;

    /**
     * The docblock content of the parsed item
     *
     * @var string
     */
    protected $docBlock = '';

    /**
     * The list of annotation object.
     *
     * @var array
     */
    protected $annotList = [];

    /**
     * Construct
     *
     * @param ParserManager $parserManager
     * @param Reflector $reflection
     */
    public function __construct(
        ParserManager $parserManager,
        Reflector $reflection
    ) {
        $this->parserManager = $parserManager;
        $this->reflection    = $reflection;
    }

    /**
     * Run the parser
     *
     * @return void
     */
    abstract public function run();

    /**
     * Get the parser manager system
     *
     * @return \BultonFr\Annotation\ParserManager
     */
    public function getParserManager(): ParserManager
    {
        return $this->parserManager;
    }

    /**
     * Get the Reflection object for the readed item
     *
     * @return \Reflector
     */
    public function getReflection(): Reflector
    {
        return $this->reflection;
    }

    /**
     * Get the annotation reader system
     *
     * @return \BultonFr\Annotation\Parsers\Annotations\Reader|null
     */
    public function getAnnotReader(): ?AnnotReader
    {
        return $this->annotReader;
    }

    /**
     * Get the docblock content of the parsed item
     *
     * @return string
     */
    public function getDocBlock(): string
    {
        return $this->docBlock;
    }

    /**
     * Get the value of annotList
     */
    public function getAnnotList(): array
    {
        return $this->annotList;
    }

    /**
     * Obtain the docblock content for the parsed item
     *
     * @return string
     */
    public function obtainDocBlock(): string
    {
        $this->docBlock = $this->reflection->getDocComment();
        return $this->docBlock;
    }

    /**
     * Instanciate and use the annotation reader to parse the docblock and
     * generate a list of Annotations\Info object which contain data about
     * each annotation find.
     *
     * @return void
     */
    protected function execAnnotReader()
    {
        $this->annotReader = new AnnotReader;
        $this->annotReader->parse($this->obtainDocBlock());
    }

    /**
     * Read the list of Annotations\Info object, create the final
     * object for each annotation find (with the method createAnnotObject),
     * and save it into the property $annotList.
     *
     * Nota: Annotation AddNS will not be added to this list
     *
     * @param string $itemName The name of the currently parsed item
     *
     * @return void
     */
    protected function generateAllAnnotObject(string $itemName)
    {
        $allAnnotList = $this->annotReader->getAnnotationList();

        foreach ($allAnnotList as $annotName => $annotList) {
            if ($annotName === 'AddNS') {
                continue;
            }

            if (array_key_exists($annotName, $this->annotList) === false) {
                $this->annotList[$annotName] = [];
            }

            foreach ($annotList as $annotInfo) {
                $annotObj = $this->createAnnotObject(
                    $itemName,
                    $annotName,
                    $annotInfo
                );

                if (method_exists($annotObj, '__toString')) {
                    $keyValue = $annotObj->__toString();

                    $this->annotList[$annotName][$keyValue] = $annotObj;
                } else {
                    $this->annotList[$annotName][] = $annotObj;
                }
            }
        }
    }

    /**
     * Instanciate the dedicated annotation object for the readed annotation
     *
     * @param string $itemName The name of the currently parsed item
     * @param string $className The dedicated class name for this annotation
     * @param Info $annotInfo Info about the readed annotation
     *
     * @return AbstractAnnotation
     *
     * @throws Exception If there is a problem with the dedicated class for
     * the readed annotation.
     */
    protected function createAnnotObject(
        string $itemName,
        string $className,
        Info $annotInfo
    ): AbstractAnnotation {
        $nsList = $this->parserManager->getImportedNS();

        if (isset($nsList[$className])) {
            $className = $nsList[$className];
        }
        
        if (class_exists($className) === false) {
            throw new Exception(
                'No class found for namespace '.$className.', please use AddNS annotation to add the class',
                static::EXCEP_CLASS_NOT_FOUND
            );
        }
        
        $annotation = new $className(
            $this->parserManager->getReader(),
            $itemName,
            $annotInfo
        );

        if ($annotation instanceof AbstractAnnotation === false) {
            throw new Exception(
                'The annotation class must extends of AbstractAnnotation.',
                static::EXCEP_NO_EXTENDS_ABSTRACT_ANNOTATION
            );
        }
        
        return $annotation;
    }
}
