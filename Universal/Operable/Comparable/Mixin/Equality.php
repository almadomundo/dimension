<?php
namespace Universal\Operable\Comparable\Mixin;
use Universal\Operators\Comparison as Comparison;
use Universal\Operable as Operable;

trait Equality
{
    private $equalityOperator;
    
    public function setEqualityOperator(Comparison\Equality $operator)
    {
        $this->equalityOperator = $operator;
    }
    
    public function equalTo(Operable\Operable $x)
    {
        if($this->isNAN() || $x->isNAN())
        {
            return false;//mock?
        }
        return $this->equalToDefined($x);
    }
    
    protected function equalToDefined(Operable\Operable $x)
    {   
        if(!$this->isFinite() || !$x->isFinite())
        {
            return false;
        }
        return $this->equalityOperator->equal($this, $x);
    }
    
    public function notEqualTo(Operable\Operable $x)
    {
        if($this->isNAN() || $x->isNAN())
        {
            return false;//mock?
        }
        return $this->notEqualToDefined($x);
    }
    
    protected function notEqualToDefined(Operable\Operable $x)
    {   
        if(!$this->isFinite() || !$x->isFinite())
        {
            return $this->notEqualToDefinedInfinite($x);
        }
        return $this->equalityOperator->notEqual($this, $x);
    }
    
    protected function notEqualToDefinedInfinite(Operable\Operable $x)
    {
        if($this->isInfinityPlus() && $x->isInfinityPlus())
            return false;
        if($this->isInfinityMinus() && $x->isInfinityMinus())
            return false;
        return true;
    }
}
