<?php
namespace Universal\Operators\Comparison\Mixin;
use Universal\Operators\Comparison as Comparison;

trait Equality
{
    private $equalityOperator = null;
    
    public function setEqualityOperator(Comparison\Equality $operator)
    {
        $this->equalityOperator = $operator;
    }
    
    public function equal($x, $y)
    {
        return $this->equalityOperator->equal($x, $y);
    }
    
    public function notEqual($x, $y)
    {
        return $this->equalityOperator->notEqual($x, $y);
    }
    
    protected function equalImmutable($x, $y)
    {
        return $this->equalityOperator->equal($x, $y);
    }
    
    protected function notEqualImmutable($x, $y)
    {
        return $this->equalityOperator->notEqual($x, $y);
    }
}
