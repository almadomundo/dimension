<?php
class Polygon_2D extends Entity_2D
{
    const POSITION_POINT_INSIDE     = 1;
    const POSITION_POINT_BORDER     = 0;
    const POSITION_POINT_OUTSIDE    = -1;
    const POSITION_LINE_LEFT        = -1;
    const POSITION_LINE_INTERSECT   = 0;
    const POSITION_LINE_RIGHT       = 1;
    
    protected $_rgBorders   = array();
    protected $_rgPoints    = array();
    protected $_fPerimeter  = 0;
    
    public function __construct() 
    {
        $rgArgs     = func_get_Args();
        if(count($rgArgs)==1 && is_array($rgArgs[0]))
        {
            $rgArgs = $rgArgs[0];
        }
        $rgPoints   = array_filter($rgArgs, function($mItem)
        {
            return $mItem instanceof Point_2D;
        });
        if(count($rgArgs)<3 || count($rgArgs)!=count($rgPoints))
        {
            throw new LogicException('Could not create '.__CLASS__.' instance by passed data');
        }
        $rgPoints   = Array_Operations::array_uunique($rgArgs, ['Point_2D', 'comparePoints']);
        if(count($rgPoints)!=count($rgArgs))
        {
            throw new Exception('Given point set contains repeated points');
        }
        $this->_resolve_by_points($rgPoints);
    }
    
    public static function comparePolygons(self $rPolygon0, self $rPolygon1)
    {
        $rgBorders0  = $rPolygon0->getBorders();
        $rgBorders1  = $rPolygon1->getBorders();
        if(count($rgBorders0)!=count($rgBorders1))
        {
            return count($rgBorders0)>count($rgBorders1)?1:-1;
        }
        $iCount = 0;
        foreach($rgBorders0 as $rBorder)
        {
            $iCount+=(int)Array_Operations::array_consists($rBorder, $rgBorders1, ['Line_2D', 'compareLinesUniform']);
        }
        if($iCount==count($rgBorders1))
        {
            return 0;
        }
        if($rPolygon0->getPerimeter()>$rPolygon1->getPerimeter())
        {
            return 1;
        }
        if($rPolygon0->getPerimeter()<$rPolygon1->getPerimeter())
        {
            return -1;
        }
        return 1; //no solid condition to compare: assuming user-defined order
    }
    
    public function getPerimeter()
    {
        return $this->_fPerimeter;
    }
    
    public function getPoints()
    {
        return $this->_rgPoints;
    }
    
    public function getBorders()
    {
        return $this->_rgBorders;
    }
    
    public function getPointPosition($rPoint)
    {
        if($this->isInPolygon($rPoint))
        {
            return self::POSITION_POINT_BORDER;
        }
        $fY     = max(array_map(function($rItem)
        {
            return $rItem->getY();
        }, $this->getPoints()))+1;
        $fX     = max(array_map(function($rItem) use ($rPoint, $fY)
        {
            $rLine = new Line_2D($rPoint, $rItem);
            return $rLine->getXbyY($fY);
        }, $this->getPoints()))+1;
        $rgIntersection         = $this->getIntersection(new Line_2D($rPoint, new Point_2D($fX, $fY)));
        $rgIntersection['set']  = array_filter($rgIntersection['set'], function($mItem)
        {
            return $mItem instanceof Point_2D;
        });
        return count($rgIntersection['set'])%2?self::POSITION_POINT_INSIDE:self::POSITION_POINT_OUTSIDE;
    }
    
    public function isInPolygon(Point_2D $rPoint)
    {
        foreach($this->getBorders() as $rBorderLine)
        {
            if($rBorderLine->isInsideLine($rPoint))
            {
                return true;
            }
        }
        return false;
    }
    
    public function getPolygonPosition(Line_2D $rLine)
    {
        $rgPoints   = $this->getPoints();
        $i          = 0;
        while(!($iPosition  = $rLine->getPointPosition($rgPoints[$i])))
        {
            $i++;
        }
        foreach($rgPoints as $rCurrentPoint)
        {
            if($rLine->getPointPosition($rCurrentPoint)*$iPosition<0)
            {
                return self::POSITION_LINE_INTERSECT;
            }
        }
        return $iPosition==Line_2D::POSITION_POINT_LEFT?self::POSITION_LINE_LEFT:self::POSITION_LINE_RIGHT;
    }
    
    public function getIntersection($mEntity, $bRaw=false) 
    {
        if($mEntity instanceof Line_2D)
        {
            return $this->_get_intersection_line($mEntity, $bRaw);
        }
        if($mEntity instanceof Circle_2D)
        {
            return $this->_get_intersection_circle($mEntity);
        }
    }
    
    public function rotateCoordinates(Angle_2D $rAngle) 
    {
        foreach($this->getBorders() as &$rLine)
        {
            $rLine->rotateCoordinates($rAngle);
        }
    }
    
    public function shiftCoordinates(Point_2D $rPoint) 
    {
        foreach($this->getBorders() as &$rLine)
        {
            $rLine->shiftCoordinates($rPoint);
        }
    }
    
    protected function _get_intersection_circle(Circle_2D $rCircle)
    {
        $rgResultPoints = array();
        foreach($this->getBorders() as $rBorderLine)
        {
            $rgIntersection = $rBorderLine->getIntersection($rCircle);
            if($rgIntersection['count'])
            {
                $rgResultPoints = array_merge($rgResultPoints, $rgIntersection['set']);
            }
        }
        $rgResultPoints = Array_Operations::array_uunique($rgResultPoints, ['Point_2D', 'comparePoints']);
        return array(
            'count' => count($rgResultPoints),
            'set'   => $rgResultPoints
        );
    }
    
    protected function _get_intersection_line(Line_2D $rLine, $bRaw=false)
    {
        $rgResultPoints = array();
        $rgResultLines  = array();
        foreach($this->getBorders() as $rBorderLine)
        {
            $rgIntersection = $rBorderLine->getIntersection($rLine);
            if($rgIntersection['count']==1)
            {
                $rgResultPoints[]   = $rgIntersection['set'][0];
            }
            elseif($rgIntersection['count'])
            {
                $rgResultLines[]    = $rgIntersection['set'][0];
            }
        }
        $rgResultPoints = Array_Operations::array_uunique($rgResultPoints, ['Point_2D', 'comparePoints']);
        $rgResultLines  = Array_Operations::array_uunique($rgResultLines, ['Line_2D', 'compareLines']);
        
        foreach($rgResultLines as $rCurrentLine)
        {
            $rgResultPoints = array_filter($rgResultPoints, function($rPoint) use ($rCurrentLine)
            {
                return !($rCurrentLine->isInsideLine($rPoint));
            });
        }
        return array(
            'count' => count($rgResultLines)?INF:count($rgResultPoints),
            'set'   => array_merge($rgResultPoints, $rgResultLines)
        );
    }
    
    protected function _resolve_by_points($rgPoints)
    {
        $rPoint = array_shift($rgPoints);
        $rFirst = $rPoint;
        foreach($rgPoints as $rCurrentPoint)
        {
            /* //commented due to __construct check
            if($rPoint->isEqual($rCurrentPoint))
            {
                throw new Exception('Zero-sized border: given side-points are equal');
            }
             */
            $this->_rgBorders[] = new Line_2D($rPoint, $rCurrentPoint);
            $this->_rgPoints[]  = $rPoint;
            $rPoint = $rCurrentPoint;
        }
        if($rFirst->isEqual($rPoint))
        {
            throw new Exception('Zero-sized border: given side-points are equal');
        }
        $this->_rgBorders[] = new Line_2D($rPoint, $rFirst);
        $this->_rgPoints[]  = $rCurrentPoint;
        $this->_fPerimeter  = array_sum(array_map(function($rLine)
        {
            return $rLine->getLength();
        }, $this->_rgBorders));
    }
}