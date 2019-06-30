<?php

use \bultonFr\Utils\Cli\BasicMsg;

require_once(__DIR__.'/../../vendor/autoload.php');

$testClasses = [
    \BultonFr\Annotation\Test\Functional\Check\Account::class,
    \BultonFr\Annotation\Test\Functional\Check\Category::class,
    \BultonFr\Annotation\Test\Functional\Check\Writing::class
];

BasicMsg::displayMsgNL('Run tests', 'yellow');

foreach ($testClasses as $className) {
    BasicMsg::displayMsgNL('> '.$className.' ... ', 'yellow');

    $testClass = new $className;
    try {
        $testClass->runTests();
        BasicMsg::displayMsgNL('Tests for '.$className.' : Success', 'green', 'bold');
    } catch (\Exception $e) {
        BasicMsg::displayMsgNL('Tests for '.$className.' : Failed', 'red', 'bold');
        BasicMsg::displayMsgNL($e->getMessage(), 'red');
        break;
    }
}
