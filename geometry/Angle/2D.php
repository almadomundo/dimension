<?php
class Angle_2D extends Entity_2D
{
    protected $_fAngle = null;
    public function __construct()
    {
        $rgArgs = func_get_args();
        if(count($rgArgs)==3 && count(array_filter($rgArgs, function($mArg)
        {
            return $mArg instanceof Point_2D;
        }))==3)
        {
            //by 3 Points:
            $this->resolve_by_points($rgArgs[0], $rgArgs[1], $rgArgs[2]);
            
        }
        elseif(count($rgArgs)==2 && count(array_filter($rgArgs, function($mArg)
        {
            return $mArg instanceof Line_2D;
        }))==2)
        {
            //by 2 Lines:
            $rgIntersection = $rgArgs[0]->getIntersection($rgArgs[1], true);
            if($rgIntersection['count']!==1)
            {
                throw new Exception('Given angle lines are not crossing');
            }
            $rPoint0    = $rgArgs[0]->getFrom();
            $rPoint1    = $rgIntersection['set'][0];
            $rPoint2    = $rgArgs[0]->isInLine($rgArgs[1]->getFrom())?$rgArgs[1]->getTill():$rgArgs[1]->getFrom();
            $this->resolve_by_points($rPoint0, $rPoint1, $rPoint2);
        }
        elseif(count($rgArgs)==1 && is_numeric($rgArgs[0]))
        {
            //plain:
            $this->_fAngle=$rgArgs[0];
            $this->normalizeAngle();
        }
        else
        {
            throw new LogicException('Could not create '.__CLASS__.' instance by passed data');
        }
    }
    
    public function getIntersection($mEntity) 
    {
       //puppet
    }
    
    public function rotateCoordinates(Angle_2D $rAngle) 
    {
        //puppet
    }
    
    public function shiftCoordinates(Point_2D $rPoint) 
    {
        //puppet
    }
    
    public function getAngle()
    {
        return $this->_fAngle;
    }
    
    public function getAngleDegrees()
    {
        return $this->_fAngle*180/pi();
    }
    
    public function normalizeAngle($fStep=null)
    {
        if(!isset($fStep))
        {
            $fStep = 2*pi();
        }
        if(self::compareAsFloats($fStep, 0) || $fStep<0)
        {
            throw new LogicException('Angle interval must be positive');
        }
        if($this->_fAngle<0)
        {
            while(($this->_fAngle+=$fStep)<0);
        }
        if($this->_fAngle>$fStep || self::compareAsFloats($this->_fAngle, $fStep))
        {
            while(($this->_fAngle-=$fStep)>$fStep);
        }
    }
    
    protected function resolve_by_points(Point_2D $rPoint0, Point_2D $rPoint1, Point_2D $rPoint2)
    {
        $rLineA = new Line_2D($rPoint0, $rPoint2);
        if($rLineA->isInLine($rPoint1))
        {
            throw new Exception('Given angle points belongs to same line');
        }
        $rLineB = new Line_2D($rPoint0, $rPoint1);
        $rLineC = new Line_2D($rPoint1, $rPoint2);
        $this->_fAngle = acos((pow($rLineC->getLength(),2)+pow($rLineB->getLength(),2)-pow($rLineA->getLength(),2))/(2*$rLineB->getLength()*$rLineC->getLength()));
    }
}