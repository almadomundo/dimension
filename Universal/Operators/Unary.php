<?php
namespace Universal\Operators;

interface Unary extends Operator
{    
    public function apply($x);
}