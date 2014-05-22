<?php
namespace Universal\Operable\Comparable\Mixin;
use Universal\Operable as Operable;

trait Linear
{
    use Equality, Inequality;
    
    public function lessThan(Operable\Operable $x)
    {
        return !$this->equalTo($x) && !$this->greaterThan($x);
    }
    
    public function greaterOrEqualTo(Operable\Operable $x)
    {
        return $this->greaterThan($x) || $this->equalTo($x);
    }
    
    public function lessOrEqualTo(Operable\Operable $x)
    {
        return !$this->greaterThan($x);
    }
}