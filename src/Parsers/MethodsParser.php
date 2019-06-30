<?php

namespace BultonFr\Annotation\Parsers;

/**
 * Do action for annotation declared on a method
 *
 * @package BultonFr\Annotation
 */
class MethodsParser extends AbstractManyParser
{
    /**
     * {@inheritDoc}
     *
     * Instanciate the parser object for all methods into the class, and add it
     * to the list on AbstractManyParser.
     */
    public function run()
    {
        $methodList = $this->reflection->getMethods();

        foreach ($methodList as $methodInfo) {
            $parser = new MethodParser($this->parserManager, $methodInfo);
            $parser->run();

            $this->addItem($methodInfo->getName(), $parser);
        }
    }
}
