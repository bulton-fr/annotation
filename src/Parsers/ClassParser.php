<?php

namespace BultonFr\Annotation\Parsers;

use BultonFr\Annotation\Annotations\AddNS;

/**
 * Do action for annotation declared for all a class
 *
 * @package BultonFr\Annotation
 */
class ClassParser extends AbstractParser
{
    /**
     * {@inheritDoc}
     *
     * Use the annotation reader to obtain all Annotations\Info objects
     * Create AddNS instancies and add there to ParserManager
     * Instanciate all others annotations dedicated object
     */
    public function run()
    {
        $this->execAnnotReader();
        $this->execAddNS();
        $this->generateAllAnnotObject($this->obtainItemName());
    }

    /**
     * Return the item name sent to AbstractAnnotation
     *
     * @return string
     */
    protected function obtainItemName(): string
    {
        return 'class_'.$this->reflection->getName();
    }

    /**
     * Create AddNS's instancies and add the object to parserManager
     *
     * @return void
     */
    protected function execAddNS()
    {
        $annotList = $this->annotReader->getAnnotationList();
        
        if (array_key_exists('AddNS', $annotList) === false) {
            return;
        }
        
        $nsList = $annotList['AddNS'];
        foreach ($nsList as $infoObj) {
            $ns = new AddNS(
                $this->parserManager->getReader(),
                $this->obtainItemName(),
                $infoObj
            );
            
            $this->parserManager->addImportedNS($ns->getNs(), $ns->getAlias());
        }
    }
}
