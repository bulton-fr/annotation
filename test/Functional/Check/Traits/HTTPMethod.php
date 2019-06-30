<?php

namespace BultonFr\Annotation\Test\Functional\Check\Traits;

trait HTTPMethod
{
    protected function checkHTTPMethod($routeInfo, $checkMsgPrefix, $methods)
    {
        $this->check(
            $checkMsgPrefix.'check methods='.$methods,
            ($routeInfo->getMethods() === $methods)
        );
    }
}
