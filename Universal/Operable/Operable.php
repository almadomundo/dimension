<?php
namespace Universal\Operable;
use Universal\Operators as Operators;

interface Operable
{
    //impossible till PHP 5.6:
    //public function invoke();
    public function get();
    public function set($x);
}