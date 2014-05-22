<?php
require_once(__DIR__.'/../../bootstrap.php');

use Universal\Operable\Numeric\Real as TestedNumeric;

class RealNumberTest extends PHPUnit_Framework_TestCase
{
    protected $numberFactory = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->numberFactory = new TestedNumeric\Factory;
    }
    
    public function test_divideWithXFiniteNonzeroYFiniteNonzero()
    { 
        $x = $this->numberFactory->create(10);
        $y = $this->numberFactory->create(-10);
        $z = $this->numberFactory->create(-1);
        $x->divideWith($y);
        
        $this->assertTrue($x->equalTo($z));
        $this->assertTrue($x->isFinite());
        $this->assertTrue($x->isNegative());
        $this->assertFalse($x->isPositive());
        $this->assertFalse($x->isZero());
        $this->assertFalse($x->isNAN());
    }
    
    public function test_divideWithXFiniteNonzeroYFiniteZero()
    {    
        $x = $this->numberFactory->create(10);
        $y = $this->numberFactory->create(0);
        $x->divideWith($y);
        
        $this->assertFalse($x->isFinite());
        $this->assertFalse($x->isNegative());
        $this->assertTrue($x->isPositive());
        $this->assertFalse($x->isZero());
        $this->assertFalse($x->isNAN());
    }
    
    public function test_divideWithXFiniteZeroYFiniteNonzero()
    {
        $x = $this->numberFactory->create(0);
        $y = $this->numberFactory->create(10);
        $x->divideWith($y);
        
        $this->assertTrue($x->isFinite());
        $this->assertFalse($x->isNegative());
        $this->assertFalse($x->isPositive());
        $this->assertTrue($x->isZero());
        $this->assertFalse($x->isNAN());
    }
    
    public function test_divideWithXFiniteZeroYFiniteZero()
    {    
        $x = $this->numberFactory->create(0);
        $y = $this->numberFactory->create(0);
        $x->divideWith($y);
        
        $this->assertFalse($x->isFinite());
        $this->assertFalse($x->isNegative());
        $this->assertFalse($x->isPositive());
        $this->assertFalse($x->isZero());
        $this->assertTrue($x->isNAN());
    }
    
    public function test_divideWithXInfinityPlusYFiniteNonzero()
    { 
        $x = $this->numberFactory->create("+inf");
        $y = $this->numberFactory->create(-1);
        $x->divideWith($y);
        
        $this->assertFalse($x->isFinite());
        $this->assertTrue($x->isNegative());
        $this->assertFalse($x->isPositive());
        $this->assertFalse($x->isZero());
        $this->assertFalse($x->isNAN());
    }
    
    public function test_divideWithXInfinityPlusYFiniteZero()
    { 
        $x = $this->numberFactory->create("+inf");
        $y = $this->numberFactory->create(0);
        $x->divideWith($y);
        
        $this->assertFalse($x->isFinite());
        $this->assertFalse($x->isNegative());
        $this->assertTrue($x->isPositive());
        $this->assertFalse($x->isZero());
        $this->assertFalse($x->isNAN());
    }
}