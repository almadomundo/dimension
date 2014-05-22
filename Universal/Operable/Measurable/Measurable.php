<?php
namespace Universal\Operable\Measurable;
use Universal\Operable as Operable;

interface Measurable extends Operable\Operable
{
    public function sumWith(Operable\Operable $x);
    public function subtractWith(Operable\Operable $x);
    public function productWith(Operable\Operable $x);
    public function divideWith(Operable\Operable $x);
    public function negate();
    public function reverse();
}