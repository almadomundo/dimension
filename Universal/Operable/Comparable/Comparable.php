<?php
namespace Universal\Operable\Comparable;
use Universal\Operable as Operable;

interface Comparable extends Operable\Operable
{
    public function equalTo(Operable\Operable $x);
    public function notEqualTo(Operable\Operable $x);
    
    public function greaterThan(Operable\Operable $x);
    public function lessThan(Operable\Operable $x);
    
    public function greaterOrEqualTo(Operable\Operable $x);
    public function lessOrEqualTo(Operable\Operable $x);
}