<?php
require_once(__DIR__.'/bootstrap.php');

use Universal\Operable\Numeric as Numeric;

$numberFactory = new Numeric\Real\Factory;

$x = $numberFactory->create(10);
$z = $numberFactory->create(0);

$p = $numberFactory->create(20);
$q = $numberFactory->create(-5);
$q->divideWith($z);

var_dump(
        $x->divideWith($z)->get(),
        $x->divideWith($p)->get(),
        $x->divideWith($z)->get(),
        $q->get(),
        $q->divideWith($x)->get(),
        $p->get(),
        $p->divideWith($x)->get(),
        $p->isFinite()
);