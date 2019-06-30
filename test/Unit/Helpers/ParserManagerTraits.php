<?php

namespace BultonFr\Annotation\Test\Unit\Helpers;

trait ParserManagerTraits
{
    protected $parserManager;

    protected function executeCreateReflectionObject()
    {
        $callMethod = function () {
            $this->createReflectionObject();
        };

        $callMethod = $callMethod->bindTo(
            $this->parserManager,
            $this->parserManager
        );

        $callMethod();
    }

    protected function addImportedNSToParser($all = false)
    {
        $this->parserManager->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\AddEntity',
            'AddEntity'
        );
        $this->parserManager->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\Column',
            'Column'
        );
        $this->parserManager->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\Route',
            'Route'
        );

        if ($all === false) {
            return;
        }
        
        $this->parserManager->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\Security',
            'Security'
        );
        $this->parserManager->addImportedNS(
            '\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod',
            'HTTPMethod'
        );
    }
}
