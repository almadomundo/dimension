<?php
namespace Universal\Operators\Arithmetic\Mixin;
use Universal\Operators\Arithmetic as Arithmetic;
use Universal\Operable as Operable;

trait Division
{
    private $divisionOperator = null;
    
    public function setDivisionOperator(Arithmetic\Division $operator)
    {
        $this->divisionOperator = $operator;
    }
    
    public function divide($x, $y)
    {
        return $this->divisionOperator->divide($x, $y);
    }
    
    protected function divideImmutable($x, $y)
    {
        return $this->divisionOperator->divide($x, $y);
    }
}
