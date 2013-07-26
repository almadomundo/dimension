<?php
class Line_2D extends Entity_2D
{
    const CANONICAL_A           = 'A';
    const CANONICAL_B           = 'B';
    const CANONICAL_C           = 'C';
    const POSITION_POINT_LEFT   = -1;
    const POSITION_POINT_BORDER = 0;
    const POSITION_POINT_RIGHT  = 1;
    
    protected $_rFrom   = null;
    protected $_rTill   = null;
    protected $_fLength = 0;
    
    public function __construct()
    {
        $rgArgs         = func_get_args();
        if(count($rgArgs)==4 && count(array_filter($rgArgs, 'is_numeric'))==4)
        {
            $this->_rFrom   = new Point_2D($rgArgs[0], $rgArgs[1]);
            $this->_rTill   = new Point_2D($rgArgs[2], $rgArgs[3]);
        }
        elseif(count($rgArgs)==2 && count(array_filter($rgArgs, function($mArg)
        {
            return $mArg instanceof Point_2D;
        }))==2)
        {
            $this->_rFrom   = $rgArgs[0];
            $this->_rTill   = $rgArgs[1];
        }
        elseif(count($rgArgs)==3 && count(array_filter($rgArgs, 'is_numeric'))==3)
        {
            $this->resolve_by_angle(new Point_2D($rgArgs[0], $rgArgs[1]), new Angle_2D($rgArgs[2]));
        }
        elseif(count($rgArgs)==2 && ($rgArgs[0] instanceof Point_2D) && is_numeric($rgArgs[1]))
        {
            $this->resolve_by_angle($rgArgs[0], new Angle_2D($rgArgs[1]));
        }
        elseif(count($rgArgs)==2 && ($rgArgs[0] instanceof Point_2D) && ($rgArgs[1] instanceof Angle_2D))
        {
            $this->resolve_by_angle($rgArgs[0], $rgArgs[1]);
        }
        elseif(count($rgArgs)==2 && ($rgArgs[0] instanceof self) && is_numeric($rgArgs[1]))
        {
            $this->resolve_by_line($rgArgs[0], new Angle_2D($rgArgs[1]));
        }
        elseif(count($rgArgs)==2 && ($rgArgs[0] instanceof self) && ($rgArgs[1] instanceof Angle_2D))
        {
            $this->resolve_by_line($rgArgs[0], $rgArgs[1]);
        }
        else
        {
            throw new LogicException('Could not create '.__CLASS__.' instance by passed data');
        }
        $this->_fLength = $this->_rFrom->getDistance($this->_rTill);
    }
    
    public function __toString()
    {
        return '{'.(string)$this->getFrom().'->'.(string)$this->getTill().'}';
    }
    
    public static function areEqual(self $rLine0, self $rLine1)
    {
        return $rLine0->isEqual($rLine1);
    }
    
    public static function compareLinesUniform(self $rLine0, self $rLine1, $iPointMode=Point_2D::COMPARE_X_FIRST)
    {
        $iCheckFrom = self::compareLines($rLine0, $rLine1, $iPointMode);
        $iCheckTill = self::compareLines($rLine0, new Line_2D($rLine1->getTill(), $rLine1->getFrom()), $iPointMode);
        if(!$iCheckFrom || !$iCheckTill)
        {
            return 0;
        }
        return $iCheckFrom*$iCheckTill;
    }

    public static function compareLines(self $rLine0, self $rLine1, $iPointMode=Point_2D::COMPARE_X_FIRST)
    {
        $iFirstCheck    = Point_2D::comparePoints($rLine0->getFrom(), $rLine1->getFrom(), $iPointMode);
        $iSecondCheck   = Point_2D::comparePoints($rLine0->getTill(), $rLine1->getTill(), $iPointMode);
        if(!$iFirstCheck && !$iSecondCheck)
        {
            return 0;
        }
        elseif(!$iFirstCheck)
        {
            return $iSecondCheck;
        }
        elseif(!$iSecondCheck)
        {
            return $iFirstCheck;
        }
        return $iFirstCheck*$iSecondCheck;
    }
    
    public function getFrom()
    {
        return $this->_rFrom;
    }
    
    public function getTill()
    {
        return $this->_rTill;
    }
    
    public function getLength()
    {
        return $this->_fLength;
    }
    
    public function rotateCoordinates(Angle_2D $rAngle) 
    {
        $this->_rFrom->rotateCoordinates($rAngle);
        $this->_rTill->rotateCoordinates($rAngle);
        return $this;
    }
    
    public function shiftCoordinates(Point_2D $rPoint) 
    {
        $this->_rFrom->shiftCoordinates($rPoint);
        $this->_rTill->shiftCoordinates($rPoint);
        return $this;
    }
    
    public function getCanonical()
    {
        $fX0 = $this->getFrom()->getX();
        $fY0 = $this->getFrom()->getY();
        $fX1 = $this->getTill()->getX();
        $fY1 = $this->getTill()->getY();
        if(self::compareAsFloats($fX0, $fX1) && self::compareAsFloats($fY0, $fY1))
        {
            return array(
                self::CANONICAL_A   => null,
                self::CANONICAL_B   => null,
                self::CANONICAL_C   => null
            );
        }
        if(self::compareAsFloats($fX0*$fY1, $fX1*$fY0))
        {
            return array(
                self::CANONICAL_A   => self::compareAsFloats($fX0, 0)?(self::compareAsFloats($fX1, 0)?0:$fY1/$fX1):$fY0/$fX0,
                self::CANONICAL_B   => -1,
                self::CANONICAL_C   => 0
            );
        }
        return array(
            self::CANONICAL_A   => $fY1-$fY0,
            self::CANONICAL_B   => $fX0-$fX1,
            self::CANONICAL_C   => $fX1*$fY0-$fX0*$fY1
        );
    }
    
    public function getXbyY($fY)
    {
        $rgCoef = $this->getCanonical();
        if($rgCoef[self::CANONICAL_A])
        {
            return ($rgCoef[self::CANONICAL_B]*$fY+$rgCoef[self::CANONICAL_C])/(-1*$rgCoef[self::CANONICAL_A]);
        }
        return null;
    }
    
    public function getYbyX($fX)
    {
        $rgCoef = $this->getCanonical();
        if($rgCoef[self::CANONICAL_B])
        {
            return ($rgCoef[self::CANONICAL_A]*$fX+$rgCoef[self::CANONICAL_C])/(-1*$rgCoef[self::CANONICAL_B]);
        }
        return null;
    }
    
    public function getPointPosition(Point_2D $rPoint)
    {
        if($this->isInLine($rPoint))
        {
            return self::POSITION_POINT_BORDER;
        }
        $rPoint = $rPoint->shiftCoordinates($this->getFrom())->rotateCoordinates($this->getLineAngle());
        return $rPoint->getX()>0?self::POSITION_POINT_RIGHT:self::POSITION_POINT_LEFT;
    }
    
    public function getLineAngle()
    {
        $rgCoef = $this->getCanonical();
        if(self::compareAsFloats($rgCoef[self::CANONICAL_A], 0))
        {
            return new Angle_2D($this->getFrom()->getX()<$this->getTill()->getX()?0:pi());
        }
        if(self::compareAsFloats($rgCoef[self::CANONICAL_B], 0))
        {
            return new Angle_2D($this->getFrom()->getY()<$this->getTill()->getY()?pi()/2:-1*pi()/2);
        }
        $rPoint = new Point_2D($this->getTill()->getX(), $this->getFrom()->getY());
        return new Angle_2D($rPoint, $this->getFrom(), $this->getTill());
    }
    
    public function isEqual(Line_2D $rLine, $bRaw=false)
    {
        if($bRaw)
        {
            $rgCoef = $rLine->getCanonical();
            foreach($this->getCanonical() as $sCoef=>$fCoef)
            {
                if(!self::compareAsFloats($fCoef, $rgCoef[$sCoef]))
                {
                    return false;
                }
            }
            return true;
        }
        return $this->getFrom()->isEqual($rLine->getFrom()) &&
               $this->getTill()->isEqual($rLine->getTill());
    }
    
    public function isInsideLine(Point_2D $rPoint)
    {
        $fXMin = min([$this->getFrom()->getX(), $this->getTill()->getX()]);
        $fYMin = min([$this->getFrom()->getY(), $this->getTill()->getY()]);
        $fXMax = max([$this->getFrom()->getX(), $this->getTill()->getX()]);
        $fYMax = max([$this->getFrom()->getY(), $this->getTill()->getY()]);
        return  (self::compareGE($rPoint->getX(), $fXMin) && self::compareLE($rPoint->getX(), $fXMax)) &&
                (self::compareGE($rPoint->getY(), $fYMin) && self::compareLE($rPoint->getY(), $fYMax)) &&
                $this->isInLine($rPoint);
    }
    
    public function isInLine(Point_2D $rPoint)
    {
        $rgCoef = $this->getCanonical();
        return self::compareAsFloats($rgCoef[self::CANONICAL_A]*$rPoint->getX()+$rgCoef[self::CANONICAL_B]*$rPoint->getY()+$rgCoef[self::CANONICAL_C], 0);
    }
    
    public function getIntersection($rLine, $bRaw=false)
    {
        $rgIntersection = $this->_get_raw_intersection($rLine);
        $rLine0         = self::_normalize_line($this);
        $rLine1         = self::_normalize_line($rLine);
        if(!$rgIntersection['count'] || $bRaw)
        {
            return $rgIntersection;
        }
        elseif($rgIntersection['count']==1)
        {
            if($rLine0->isInsideLine($rgIntersection['set'][0])&&$rLine1->isInsideLine($rgIntersection['set'][0]))
            {
                return $rgIntersection;
            }
            return array(
                        'count' => 0,
                        'set'   => array()
                    );
        }
        else
        {
            $rgCoef = $rLine0->getCanonical();
            if(!self::compareAsFloats($rgCoef[self::CANONICAL_A], 0))
            {
                $fMaxFrom=max([$rLine0->getFrom()->getY(), $rLine1->getFrom()->getY()]);
                $fMinTill=min([$rLine0->getTill()->getY(), $rLine1->getTill()->getY()]);
                if(self::compareAsFloats($fMinTill, $fMaxFrom))
                {
                    return array(
                        'count' => 1,
                        'set'   => array(new Point_2D($rLine0->getXbyY($fMaxFrom), $fMaxFrom))
                    );
                }
                elseif($fMinTill<$fMaxFrom)
                {
                    return array(
                        'count' => 0,
                        'set'   => array()
                    );
                }
                else
                {
                    return array(
                        'count' => INF,
                        'set'   => array(new Line_2D($rLine0->getXbyY($fMaxFrom), $fMaxFrom, $rLine0->getXbyY($fMinTill), $fMinTill))
                    );
                }
            }
            else
            {
                $fMaxFrom=max([$rLine0->getFrom()->getX(), $rLine1->getFrom()->getX()]);
                $fMinTill=min([$rLine0->getTill()->getX(), $rLine1->getTill()->getX()]);
                if(self::compareAsFloats($fMinTill, $fMaxFrom))
                {
                    return array(
                        'count' => 1,
                        'set'   => array(new Point_2D($fMaxFrom, $rLine0->getYbyX($fMaxFrom)))
                    );
                }
                elseif($fMinTill<$fMaxFrom)
                {
                    return array(
                        'count' => 0,
                        'set'   => array()
                    );
                }
                else
                {
                    return array(
                        'count' => INF,
                        'set'   => array(new Line_2D($fMaxFrom, $rLine0->getYbyX($fMaxFrom), $fMinTill, $rLine0->getYbyX($fMinTill)))
                    );
                }
            }
        }
    }
    
    protected static function _normalize_line(self $rLine)
    {
        $rgCoef = $rLine->getCanonical();
        $rPoint = $rLine->getFrom();
        $rFrom  = $rLine->getFrom();
        $rTill  = $rLine->getTill();
        if($rgCoef[self::CANONICAL_A])
        {
            if($rLine->getFrom()->getX()>$rLine->getTill()->getX())
            {
                $rFrom = $rTill;
                $rTill = $rPoint;
            }
            //some actions
        }
        else
        {
            if($rLine->getFrom()->getY()>$rLine->getTill()->getY())
            {
                $rFrom = $rTill;
                $rTill = $rPoint;
            }
            //some actions
        }
        return new Line_2D($rFrom, $rTill);
    }
    
    protected function _get_raw_intersection(self $rLine)
    {
        $rLine0     = self::_normalize_line($this);
        $rLine1     = self::_normalize_line($rLine);
        $rgCoef0    = $rLine0->getCanonical();
        $rgCoef1    = $rLine1->getCanonical();
        $fDx        = $rgCoef1[self::CANONICAL_B]*$rgCoef0[self::CANONICAL_C]-$rgCoef0[self::CANONICAL_B]*$rgCoef1[self::CANONICAL_C];
        $fDy        = $rgCoef0[self::CANONICAL_A]*$rgCoef1[self::CANONICAL_C]-$rgCoef1[self::CANONICAL_A]*$rgCoef0[self::CANONICAL_C];
        $fD         = $rgCoef1[self::CANONICAL_A]*$rgCoef0[self::CANONICAL_B]-$rgCoef0[self::CANONICAL_A]*$rgCoef1[self::CANONICAL_B];
        //different cases, when lines are parallels:
        if(self::compareAsFloats($fD, 0))
        {
            if(self::compareAsFloats($rgCoef0[self::CANONICAL_A], 0))
            {
                if(self::compareAsFloats($fDx, 0))
                {
                    return array(
                        'count' => INF,
                        'set'   => array($this)
                    );
                }
                else
                {
                    return array(
                        'count' => 0,
                        'set'   => array()
                    );
                }
            }
            else
            {
                if(self::compareAsFloats($fDy, 0))
                {
                    return array(
                        'count' => INF,
                        'set'   => array($this)
                    );
                }
                else
                {
                    return array(
                        'count' => 0,
                        'set'   => array()
                    );
                }
            }
        }
        //common case - intersection
        return array(
            'count' => 1,
            'set'   => array(new Point_2D($fDx/$fD, $fDy/$fD))
        );
    }
    
    protected function resolve_by_angle(Point2D $rPoint, Angle_2D $rAngle)
    {
        $this->_rFrom   = $rPoint;
        $rAngle->normalizeAngle(pi());
        if(self::compareAsFloats($rAngle->getAngle(), pi()/2))
        {
            $this->_rTill   = new Point_2D($rPoint->getX(), $rPoint->getY()+1);
        }
        elseif($rAngle->getAngle()<pi()/2)
        {
            $this->_rTill   = new Point_2D($rPoint->getX()+1, $rPoint->getY()+tan($rAngle->getAngle()));
        }
        else
        {
            $this->_rTill   = new Point_2D($rPoint->getX()-1, $rPoint->getY()-tan($rAngle->getAngle()));
        }
    }
    
    protected function resolve_by_line(self $rLine, Angle_2D $rAngle)
    {
        $rAngle->normalizeAngle(pi());
        if(self::compareAsFloats($rAngle->getAngle(), pi()/2))
        {
            $rTill  = new Point_2D(0, 1);
        }
        elseif($rAngle->getAngle()<pi()/2)
        {
            $rTill  = new Point_2D(1, tan($rAngle->getAngle()));
        }
        else
        {
            $rTill  = new Point_2D(-1, -1*tan($rAngle->getAngle()));
        }
        $rFrom = new Point_2D(0, 0);
        if(self::compareAsFloats($rLine->getFrom()->getX(), $rLine->getTill()->getX()))
        {
            $rAngleCoord    = new Angle_2D(-1*pi()/2);
        }
        elseif(self::compareAsFloats($rLine->getFrom()->getY(), $rLine->getTill()->getY()))
        {
            $rAngleCoord    = new Angle_2D($rLine->getFrom()->getX()>$rLine->getTill()->getX()?pi():0);
        }
        else
        {
            $rAngleCoord    = new Angle_2D(-1*atan(abs($rLine->getFrom()->getY()-$rLine->getTill()->getY())/abs($rLine->getFrom()->getX()-$rLine->getTill()->getX())));
        }
        $rPointCoord    = new Point_2D(-1*$rLine->getFrom()->getX(), -1*$rLine->getFrom()->getY());
        $this->_rFrom   = $rFrom->rotateCoordinates($rAngleCoord)->shiftCoordinates($rPointCoord);
        $this->_rTill   = $rTill->rotateCoordinates($rAngleCoord)->shiftCoordinates($rPointCoord);
    }
}