<?php

require_once __DIR__.'/../vendor/autoload.php';

use AlexeyKupershtokh\OpcodeHitMiss\OpcodeHitMiss;

OpcodeHitMiss::register(
    function (OpcodeHitMiss $o) {
//        print_r($o->getIncludedFilesStats());
        var_dump($o->getIncludedFiles());
    }
);
