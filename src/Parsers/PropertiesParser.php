<?php

namespace BultonFr\Annotation\Parsers;

/**
 * Do action for annotation declared on a method
 *
 * @package BultonFr\Annotation
 */
class PropertiesParser extends AbstractManyParser
{
    /**
     * {@inheritDoc}
     *
     * Instanciate the parser object for all properties into the class, and
     * add it to the list on AbstractManyParser.
     */
    public function run()
    {
        $propertyList = $this->reflection->getProperties();

        foreach ($propertyList as $propertyInfo) {
            $parser = new PropertyParser($this->parserManager, $propertyInfo);
            $parser->run();

            $this->addItem($propertyInfo->getName(), $parser);
        }
    }
}
