<?php
/**
 * demo for set operations
 */
error_reporting(2047);
$rOne   = new Set_Abstract('foo', 5, false, -62.2);
$rTwo   = Set_Abstract::createFromArray(['', 84, 'bar']);
$rThree = Set_Abstract::createFromArray([false, 84, 'bar']);
$rSet   = $rOne->getProduct($rTwo);
var_dump($rSet->isSupset(new Set_Abstract(['foo', 'bar'], [5, 84])));
var_dump($rSet->isSupset(new Set_Abstract(['foo', 'bar'], [84, 5])));
var_dump($rOne->getIntersection($rTwo)->isEmpty());
var_dump($rOne->getIntersection($rThree)->getData());
var_dump($rOne->getUnion($rThree)->getData());