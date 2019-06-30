<?php

namespace BultonFr\Annotation\Test\Functional\Annotations;

use BultonFr\Annotation\Annotations\AddNS;

class AddEntity extends AddNS
{
    public function __toString()
    {
        return $this->alias;
    }
}
