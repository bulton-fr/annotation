<?php

namespace BultonFr\Annotation\Test\Functional\Check\Traits;

trait Route
{
    protected function checkRoute($routeInfo, $checkMsgPrefix, $name, $path)
    {
        $this->check(
            $checkMsgPrefix.'check name='.$name,
            ($routeInfo->getName() === $name)
        );

        $this->check(
            $checkMsgPrefix.'check path='.$path,
            ($routeInfo->getPath() === $path)
        );
    }
}
