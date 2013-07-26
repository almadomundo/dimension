<?php
class Circle_2D extends Entity_2D
{
    const INTERSECTION_OUTER    = 0;
    const INTERSECTION_INNER    = 1;
    const INTERSECTION_MATCH    = 2;
    const INTERSECTION_LIMIT    = 3;
    const POSITION_POINT_INSIDE = 1;
    const POSITION_POINT_BORDER = 0;
    const POSITION_POINT_OUTSIDE= -1;

    protected $_rCenter = null;
    protected $_fRadius = null;

    public function __construct($fX, $fY, $fRadius)
    {
        if((double)$fRadius<=0)
        {
            throw new LogicException('Given circle radius is non-positive');
        }
        $this->_rCenter = new Point_2D($fX, $fY);
        $this->_fRadius = (double)$fRadius;
    }

    public function getCenter()
    {
        return $this->_rCenter;
    }

    public function getRadius()
    {
        return $this->_fRadius;
    }
    
    public function getPointPosition(Point_2D $rPoint)
    {
        $rDistance = $this->getCenter()->getDistance($rPoint);
        if(self::compareAsFloats($rDistance, $this->getRadius()))
        {
            return self::POSITION_POINT_BORDER;
        }
        return $rDistance<$this->getRadius()?self::POSITION_POINT_INSIDE:self::POSITION_POINT_OUTSIDE;
    }
    
    public function rotateCoordinates(Angle_2D $rAngle) 
    {
        $this->_rCenter->rotateCoordinates($rAngle);
        return $this;
    }
    
    public function shiftCoordinates(Point_2D $rPoint)
    {
        $this->_rCenter->shiftCoordinates($rPoint);
        return $this;
    }
    
    public function getIntersection($rEntity, $bRaw=false)
    {
        if($rEntity instanceof self)
        {
            return $this->_get_intersection_circle($rEntity);
        }
        if($rEntity instanceof Line_2D)
        {
            return $this->_get_intersection_line($rEntity, $bRaw);
        }
    }
    
    protected function _get_intersection_line(Line_2D $rLine, $bRaw=false)
    {
        $fX0    = $this->getCenter()->getX();
        $fY0    = $this->getCenter()->getY();
        $fR     = $this->getRadius();
        $rgCoef = $rLine->getCanonical();
        $fD     = $rgCoef[Line_2D::CANONICAL_A]*$fX0+$rgCoef[Line_2D::CANONICAL_B]*$fY0+$rgCoef[Line_2D::CANONICAL_C];
        if(self::compareAsFloats($rgCoef[Line_2D::CANONICAL_A], 0))
        {
            $rEquation  = new Equation_Real(
                    pow($rgCoef[Line_2D::CANONICAL_B], 2),
                    0,
                    pow($rgCoef[Line_2D::CANONICAL_B]*$fY0, 2)+pow($rgCoef[Line_2D::CANONICAL_C], 2)-pow($rgCoef[Line_2D::CANONICAL_B]*$fR, 2)
            );
            $rgPoints   = array_map(function($fRoot) use ($fX0, $fY0, $rgCoef, $fD)
            {
                return new Point_2D(
                        $fX0 + $fRoot,
                        -1*$rgCoef[Line_2D::CANONICAL_C]/$rgCoef[Line_2D::CANONICAL_B]
                );
            }, $rEquation->solveEquation(Equation_Real::SOLVE_EXACT)[Equation_Real::ROOT_EXACT]);
        }
        else
        {
            $rEquation  = new Equation_Real(
                    pow($rgCoef[Line_2D::CANONICAL_A],2)+pow($rgCoef[Line_2D::CANONICAL_B],2),
                    2*$fD*$rgCoef[Line_2D::CANONICAL_B],
                    pow($fD,2)-pow($rgCoef[Line_2D::CANONICAL_A]*$fR,2)
            );
            $rgPoints   = array_map(function($fRoot) use ($fX0, $fY0, $rgCoef, $fD)
            {
                return new Point_2D(
                        $fX0 - ($rgCoef[Line_2D::CANONICAL_B]*$fRoot+$fD)/$rgCoef[Line_2D::CANONICAL_A],
                        $fY0 + $fRoot
                );
            }, $rEquation->solveEquation(Equation_Real::SOLVE_EXACT)[Equation_Real::ROOT_EXACT]);
        }
        if(!$bRaw)
        {
            $rgPoints=array_filter($rgPoints, array($rLine, 'isInsideLine'));
        }
        return array(
            'count' => count($rgPoints),
            'set'   => $rgPoints
        );
    }

    protected function _get_intersection_circle(self $rCircle)
    {
        $fP = max([$this->getRadius(), $rCircle->getRadius()]);
        $fQ = min([$this->getRadius(), $rCircle->getRadius()]);
        $fS = $this->getCenter()->getDistance($rCircle->getCenter());
        if(self::compareAsFloats($fP, $fQ) && self::compareAsFloats($fS, 0))
        {
            return array(
                    'type'  => self::INTERSECTION_MATCH,
                    'count' => INF,
                    'set'   => [$this]
            );
        }
        if(self::compareAsFloats($fP, $fS))
        {
            return array(
                    'type'  => self::INTERSECTION_LIMIT,
                    'count' => 2,
                    'set'   => $this->_resolve_intersection_dual($rCircle)
            );
        }
        if($fP+$fQ<$fS)
        {
            return array(
                    'type'  => self::INTERSECTION_OUTER,
                    'count' => 0,
                    'set'   => array()
            );
        }
        if(self::compareAsFloats($fP+$fQ, $fS))
        {
            return array(
                    'type'  => self::INTERSECTION_OUTER,
                    'count' => 1,
                    'set'   => $this->_resolve_intersection_mono($rCircle)
            );
        }
        if($fP+$fQ>$fS && $fS>$fP)
        {
            return array(
                    'type'  => self::INTERSECTION_OUTER,
                    'count' => 2,
                    'set'   => $this->_resolve_intersection_dual($rCircle)
            );
        }
        if($fP>$fS)
        {
            if(self::compareAsFloats($fS+$fQ, $fP))
            {
                    return array(
                            'type'  => self::INTERSECTION_INNER,
                            'count' => 1,
                            'set'   => $this->_resolve_intersection_mono($rCircle)
                    );
            }
            if($fS+$fQ<$fP)
            {
                    return array(
                            'type'  => self::INTERSECTION_INNER,
                            'count' => 0,
                            'set'   => array()
                    );
            }
            if($fS+$fQ>$fP)
            {
                    return array(
                            'type'  => self::INTERSECTION_INNER,
                            'count' => 2,
                            'set'   => $this->_resolve_intersection_dual($rCircle)
                    );
            }
        }
    }
    
    protected function _resolve_intersection_dual(self $rCircle)
    {
        $fR0    = $this->getRadius();
        $fR1    = $rCircle->getRadius();
        $fS     = $this->getCenter()->getDistance($rCircle->getCenter());
        $rAlfa  = new Angle_2D(acos((pow($fR0,2)+pow($fS,2)-pow($fR1,2))/(2*$fR0*$fS)));
        $rBeta  = new Angle_2D(acos((pow($fR1,2)+pow($fS,2)-pow($fR0,2))/(2*$fR1*$fS)));
        $rLineCenters0  = new Line_2D($this->getCenter(), $rCircle->getCenter());
        $rLineCenters1  = new Line_2D($rCircle->getCenter(), $this->getCenter());
        $rRadiusUp0     = new Line_2D($rLineCenters0, $rAlfa);
        $rRadiusUp1     = new Line_2D($rLineCenters1, new Angle_2D(2*pi()-$rBeta->getAngle()));
        $rRadiusDown0   = new Line_2D($rLineCenters0, new Angle_2D(2*pi()-$rAlfa->getAngle()));
        $rRadiusDown1   = new Line_2D($rLineCenters1, $rBeta);
        return array_merge($rRadiusUp0->getIntersection($rRadiusUp1, true)['set'], $rRadiusDown0->getIntersection($rRadiusDown1, true)['set']);
        
    }
    
    protected function _resolve_intersection_mono(self $rCircle)
    {
        $rLine      = new Line_2D($this->getCenter(), $rCircle->getCenter());
        $rgPoints0  = $this->_get_intersection_line($rLine, true)['set'];
        $rgPoints1  = $rCircle->_get_intersection_line($rLine, true)['set'];
        return array_uintersect($rgPoints0, $rgPoints1, array('Point_2D', 'comparePoints'));
    }
}