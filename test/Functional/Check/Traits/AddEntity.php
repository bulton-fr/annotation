<?php

namespace BultonFr\Annotation\Test\Functional\Check\Traits;

trait AddEntity
{
    protected function checkAddEntity($key, $annotList, $ns, $alias)
    {
        $this->check(
            'AddEntity #'.$key.' : check key exist',
            (isset($annotList[$key]))
        );

        $annotObj = $annotList[$key];

        $this->check(
            'AddEntity #'.$key.' : valid ns',
            ($annotObj->getNS() === $ns)
        );

        $this->check(
            'AddEntity #'.$key.' : valid alias',
            ($annotObj->getAlias() === $alias)
        );
    }
}
