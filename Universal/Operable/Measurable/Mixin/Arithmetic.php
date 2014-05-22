<?php
namespace Universal\Operable\Measurable\Mixin;
use Universal\Operable\Numeric as Numeric;

trait Arithmetic
{
    use Summation;
    use Subtraction;
    use Multiplication;
    use Division;
    use Negation;
    use Reciprocal;
    
    private $arithmeticZero             = null;
    private $arithmeticNAN              = false;
    private $arithmeticInfinityPlus     = false;
    private $arithmeticInfinityMinus    = false;
    
    public function setArithmeticZero(Numeric\Enumerable $x)
    {
        $this->arithmeticZero = $x;
    }
    
    public function isNAN()
    {
        return $this->arithmeticNAN;
    }
    
    public function isFinite()
    {
        return !$this->isNAN() && !$this->isInfinityPlus() && !$this->isInfinityMinus();
    }
    
    public function isZero()
    {
        return !$this->isNAN() && $this->isFinite() && $this->equalTo($this->arithmeticZero);
    }
    
    public function isPositive()
    {
        return $this->isInfinityPlus() || $this->greaterThan($this->arithmeticZero);
    }
    
    public function isNegative()
    {
        return $this->isInfinityMinus() || $this->lessThan($this->arithmeticZero);
    }
    
    protected function isInfinityPlus()
    {
        return $this->arithmeticInfinityPlus ;
    }
    
    protected function isInfinityMinus()
    {
        return $this->arithmeticInfinityMinus;
    }
    
    protected function setSpecialFlag($type, $flag)
    {
        $this->$type = $flag;
        if($flag)
        {
            $this->set($this->arithmeticZero->get());
        }
    }
    
    protected function setNAN($flag=true)
    {
        $this->setSpecialFlag('arithmeticNAN', $flag);      
    }
    
    protected function setInfinityPlus($flag=true)
    {
        $this->setSpecialFlag('arithmeticInfinityPlus', $flag);
    }
    
    protected function setInfinityMinus($flag=true)
    {
        $this->setSpecialFlag('arithmeticInfinityMinus', $flag);
    }
}