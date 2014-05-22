<?php
namespace Universal\Operators\Comparison;
use Universal\Operators\Comparison\Mixin as Mixin;
use Universal\Operable as Operable;

abstract class Linear 
{
    use Mixin\Equality, Mixin\Inequality;
    
    public function less($x, $y)
    {
        return !$this->equal($x, $y) && !$this->greater($x, $y);
    }
    
    public function greaterOrEqual($x, $y)
    {
        return $this->greater($x, $y) || $this->equal($x, $y);
    }
    
    public function lessOrEqual($x, $y)
    {
        return !$this->greater($x, $y);
    }
}