<?php
namespace Universal\Operable\Numeric;
use Universal\Operable\Comparable as Comparable;
use Universal\Operable\Measurable as Measurable;

abstract class Number implements Enumerable
{
    const FORMAT_INFINITY_PLUS  = "+inf";
    const FORMAT_INFINITY_MINUS = "-inf";
    const FORMAT_NOT_A_NUMBER   = "nan";
    
    use Comparable\Mixin\Linear;
    use Measurable\Mixin\Arithmetic;
    
    protected $numberHolder  = null;
    
    abstract public function isValid($x);
    
    abstract protected function setDomainType($x);
    
    public function get()
    {
        if($this->isNAN())
            return self::FORMAT_NOT_A_NUMBER;
        if($this->isInfinityPlus())
            return self::FORMAT_INFINITY_PLUS;
        if($this->isInfinityMinus())
            return self::FORMAT_INFINITY_MINUS;
        return $this->numberHolder;
    }
    
    public function set($x)
    {
        if($x===self::FORMAT_NOT_A_NUMBER)
            $this->setNAN();
        elseif($x===self::FORMAT_INFINITY_PLUS)
            $this->setInfinityPlus();
        elseif($x===self::FORMAT_INFINITY_MINUS)
            $this->setInfinityMinus();
        else
            $this->setRegular($x);
    }
    
    public function setRegular($x)
    {
        if(!$this->isValid($x))
        {
            //TODO: replace with child exception!
            throw new \InvalidArgumentException('Invalid operable entity domain. Dump: '.PHP_EOL.var_export($x));
        }
        $this->numberHolder = $this->setDomainType($x);
    }
}