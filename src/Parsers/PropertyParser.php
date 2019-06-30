<?php

namespace BultonFr\Annotation\Parsers;

/**
 * Do action for annotation declared on a method
 *
 * @package BultonFr\Annotation
 */
class PropertyParser extends AbstractParser
{
    /**
     * {@inheritDoc}
     *
     * Use the annotation reader to obtain all Annotations\Info objects
     * Instanciate all others annotations dedicated object
     */
    public function run()
    {
        $this->execAnnotReader();
        $this->generateAllAnnotObject($this->reflection->getName());
    }
}
