<?php

namespace BultonFr\Annotation\Test\Functional\Check\Traits;

trait Security
{
    protected function checkSecurity($routeInfo, $checkMsgPrefix, $fct, $role)
    {
        $this->check(
            $checkMsgPrefix.'check fct='.$fct,
            ($routeInfo->getFct() === $fct)
        );

        $this->check(
            $checkMsgPrefix.'check role='.$role,
            ($routeInfo->getRole() === $role)
        );
    }
}
