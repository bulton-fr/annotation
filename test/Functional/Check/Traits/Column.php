<?php

namespace BultonFr\Annotation\Test\Functional\Check\Traits;

trait Column
{
    protected function checkColumn($propertyAnnotList, $propertyName, $columnAttr)
    {
        $checkMsgPrefix = 'Property '.$propertyName.' : ';

        $defaultAttr = [
            'name'     => $propertyName,
            'type'     => null,
            'primary'  => false,
            'nullable' => false,
            'entity'   => null,
            'values'   => null
        ];

        $expectedAttr = array_merge($defaultAttr, $columnAttr);

        $this->check($checkMsgPrefix.'Column exist', isset($propertyAnnotList['Column']));
        $columnInfoList = $propertyAnnotList['Column'];

        $this->check($checkMsgPrefix.'One column only', (count($columnInfoList) === 1));
        $columnInfo = $columnInfoList[0];

        foreach ($expectedAttr as $attrName => $exceptedValue) {
            $method    = 'get'.ucfirst($attrName);
            $realValue = $columnInfo->{$method}();

            //var_dump($attrName, $exceptedValue, $realValue, ($exceptedValue === $realValue));

            $checkMsg =
                $checkMsgPrefix.'Column'
                .' - attr '.$attrName.' : Check value'
                //."\n".'Expected: '.print_r($exceptedValue, true)
                //."\n".'Real    : '.print_r($realValue, true)
            ;

            $this->check(
                $checkMsg,
                ($exceptedValue === $realValue)
            );
        }
    }
}
