<?php
namespace Universal\Operable\Measurable\Mixin;
use Universal\Operators\Arithmetic as ArithmeticOperators;
use Universal\Operable as Operable;

trait Division
{
    private $divisionOperator;
    
    public function setDivisionOperator(ArithmeticOperators\Division $operator)
    {
        $this->divisionOperator = $operator;
    }
    
    public function divideWith(Operable\Operable $x)
    {
        if($this->isNAN() || $x->isNAN())
        {
            return $this;
        }
        return $this->divideRegularWith($x);
    }
    
    protected function divideRegularWith(Operable\Operable $x)
    {
        if($this->isFinite() && $x->isFinite())
        {
            return $this->divideRegularFiniteWith($x);
        }
        return $this->divideRegularInfiniteWith($x);
    }
    
    protected function divideRegularInfiniteWith(Operable\Operable $x)
    {
        $this->set($this->arithmeticZero->get());
        $this->setNAN(!$this->isFinite() && !$x->isFinite());
        if(!$this->isNAN())
        {
            $infinityPlus   = ($this->isInfinityPlus() && !$x->isNegative()) 
                            ||($this->isInfinityMinus() && !$x->isPositive());
            $infinityMinus  = ($this->isInfinityPlus() && !$x->isPositive())
                            ||($this->isInfinityMinus() && !$x->isNegative());
            $this->setInfinityPlus($infinityPlus);
            $this->setInfinityMinus($infinityMinus);
        }
        return $this;
    }
    
    protected function divideRegularFiniteWith(Operable\Operable $x)
    {
        try
        {
            $result = $this->divisionOperator->divide($this, $x, $this->arithmeticZero);
            $this->set($result);
        }
        catch(ArithmeticOperators\Exception\Division $e)
        {
            return $this->divideWithZero();
        }
        return $this;
    }
    
    protected function divideWithZero()
    {
        $this->setNAN($this->isZero());
        if(!$this->isNAN())
        {
            $this->setInfinityPlus($this->isPositive());
            $this->setInfinityMinus($this->isNegative());
        }
        return $this;
    }
}
