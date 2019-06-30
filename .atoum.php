<?php

require __DIR__ . '/vendor/autoload.php';

use mageekguy\atoum;

$report = $script->addDefaultReport();

$runner->addTestsFromDirectory(__DIR__.'/test/Unit/src');

//clover file for scrutinizer
$cloverWriter = new atoum\writers\file('./clover.xml');
$cloverReport = new atoum\reports\asynchronous\clover;
$cloverReport->addWriter($cloverWriter);
$runner->addReport($cloverReport);

//html coverage for local
$coverageField = new atoum\report\fields\runner\coverage\html(
    'BultonFr\Annotation',
    __DIR__.'/test/Unit/report/coverage'
);
$report->addField($coverageField);
