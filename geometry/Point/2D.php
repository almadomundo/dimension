<?php
class Point_2D extends Entity_2D
{
    const COMPARE_X_FIRST   = 0;
    const COMPARE_Y_FIRST   = 1;
    
    protected $_rgCoordinates = array();

    public function __construct($fX, $fY)
    {
            $this->_rgCoordinates['x']=(double)$fX;
            $this->_rgCoordinates['y']=(double)$fY;
    }
    
    public function __toString()
    {
        return '('.$this->getX().','.$this->getY().')';
    }

    public static function areEqual(self $rPoint0, self $rPoint1)
    {
        return $rPoint0->isEqual($rPoint1);
    }

    public static function comparePoints(self $rPoint0, self $rPoint1, $iMode=self::COMPARE_X_FIRST)
    {
        $sFirst = 'getX';
        $sSecond= 'getY';
        if($iMode==self::COMPARE_Y_FIRST)
        {
            $sFirst = 'getY';
            $sSecond= 'getX';
        }
        if(self::compareAsFloats($rPoint0->$sFirst(), $rPoint1->$sFirst()))
        {
            if(self::compareAsFloats($rPoint0->$sSecond(), $rPoint1->$sSecond()))
            {
                return 0;
            }
            return $rPoint0->$sSecond()>$rPoint1->$sSecond()?1:-1;
        }
        return $rPoint0->$sFirst()>$rPoint1->$sFirst()?1:-1;
    }

    public function getX()
    {
            return $this->_rgCoordinates['x'];
    }

    public function getY()
    {
            return $this->_rgCoordinates['y'];
    }

    public function isEqual(Point_2D $rPoint)
    {
        return self::compareAsFloats($this->getX(), $rPoint->getX()) &&
               self::compareAsFloats($this->getY(), $rPoint->getY());
    }

    public function getDistance($rPoint)
    {
            if($rPoint instanceof self)
            {
                    return sqrt(pow($this->getX()-$rPoint->getX(), 2) + pow($this->getY()-$rPoint->getY(), 2));
            }
            return null;
    }

    public function rotateCoordinates(Angle_2D $rAngle)
    {
        $rAngle->normalizeAngle(); //safe
        $this->_rgCoordinates['x']=$this->_rgCoordinates['x']*cos($rAngle->getAngle())-$this->_rgCoordinates['y']*sin($rAngle->getAngle());
        $this->_rgCoordinates['y']=$this->_rgCoordinates['x']*sin($rAngle->getAngle())+$this->_rgCoordinates['y']*cos($rAngle->getAngle());
        return $this;
    }

    public function shiftCoordinates(Point_2D $rPoint)
    {
        if(!($rPoint instanceof self))
        {
            return null;
        }
        $this->_rgCoordinates['x']-=$rPoint->getX();
        $this->_rgCoordinates['y']-=$rPoint->getY();
        return $this;
    }

    public function getIntersection($mEntity) 
    {
        //puppet
    }
}