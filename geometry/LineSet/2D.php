<?php
/**
 * This class was designed as a solution of "filling" problem, but now has
 * useful functionality of creating polygons by set of lines
 */
class LineSet_2D extends Float_Operations
{
    /**
     * Original set of 2d-lines
     * @_rgLines array
     */
    protected $_rgLines         = [];
    /**
     * Graph of partition using lines intersetion points
     * @_rgPartition array | null 
     */
    protected $_rgPartition     = null;
    /**
     * Result set of polygons
     * @rgPolygons array | null
     */
    protected $_rgPolygons      = null;
    
    /**
     * Create set of lines by gives array of lines
     * @return null
     * @throws LogicException If argument is not instance of 2d-line
     */
    public function __construct()
    {
        $rgArgs = func_get_args();        
        $rgLines= array_filter($rgArgs, function($mItem)
        {
            return $mItem instanceof Line_2D;
        });
        if(!count($rgArgs) || count($rgArgs)!=count($rgLines))
        {
            throw new LogicException('Could not create '.__CLASS__.' instance by passed data');
        }
        $this->_rgLines = $rgLines;
    }
    
    /**
     * Construct a set from array of elements
     * @return \self
     */
    public static function createFromArray()
    {
        return call_user_func_array([new ReflectionClass(__CLASS__), 'newInstanceArgs'], func_get_args());
    }
    
    /**
     * Solution of "filling" problem. Returns true if plane filling from
     * rPoint0 will reach rPoint1
     * @param Point_2D $rPoint0 Start point of plane filling
     * @param Point_2D $rPoint1 Test point
     * @return boolean
     */
    public function hasLink(Point_2D $rPoint0, Point_2D $rPoint1)
    {
        $rgPolygons = $this->getPolygons();
        foreach($rgPolygons as $rPolygon)
        {
            if(!$rPolygon->getPointPosition($rPoint0) || !$rPolygon->getPointPosition($rPoint1))
            {
                //treating on-set position as true
                return true;
            }
            if($rPolygon->getPointPosition($rPoint0)!==$rPolygon->getPointPosition($rPoint1))
            {
                return false;
            }
        }
        return true;
    }
    /**
     * Create polygons from original set of 2d-lines
     * @return array
     */
    public function getPolygons()
    {
        if(isset($this->_rgPolygons))
        {
            return $this->_rgPolygons;
        }
        $this->getPartition();
        $this->_get_polygons($this->_rgPartition[0]);
        $this->_rgPolygons = Array_Operations::array_uunique($this->_rgPolygons, ['Polygon_2D', 'comparePolygons']);
        return $this->_rgPolygons;
    }
    /**
     * Get original set of 2d-lines
     * @return array
     */
    public function getLines()
    {
        return $this->_rgLines;
    }
    /**
     * Create partition graph from original set of 2d-lines
     * @return array
     */
    public function getPartition()
    {
        if(isset($this->_rgPartition))
        {
            return $this->_rgPartition;
        }
        return $this->_get_partition();
    }
    
    protected function _get_polygons(Line_2D $rCurrentVector, $rgPath=array())
    {
        $rgVectors  = $this->_get_transitions($rCurrentVector);
        if(!count($rgVectors))
        {
            return null;
        }
        foreach($rgVectors as $rWayLine)
        {
            if($rgLoopback = $this->_get_way_loopback($rWayLine, $rgPath))
            {
                $this->_rgPolygons[]=new Polygon_2D($rgLoopback);
                return null;
            }
            else
            {
                $this->_get_polygons($rWayLine, array_merge($rgPath, [$rWayLine]));
            }
        }
        return null;
    }
    
    protected function _get_partition()
    {
        $rgLines            = $this->getLines();
        $rgIntersections    = array();
        $rgRepeats          = Array_Operations::array_repeat_pair($rgLines, true);
        $this->_rgPartition = array();
        foreach($rgLines as $iPrimaryKey=>$rLine)
        {
            $rgIntersections[$iPrimaryKey] = [$rLine->getFrom(), $rLine->getTill()];
        }
        foreach($rgRepeats as $rgPair)
        {
            $rgIntersection = array_values($rgPair)[0]->getIntersection(array_values($rgPair)[1]);
            if($rgIntersection['count']==1)
            {
                if(!count(array_uintersect($rgIntersections[array_keys($rgPair)[0]], $rgIntersection['set'], ['Point_2D', 'comparePoints'])))
                {
                    $rgIntersections[array_keys($rgPair)[0]][]=$rgIntersection['set'][0];
                }
                if(!count(array_uintersect($rgIntersections[array_keys($rgPair)[1]], $rgIntersection['set'], ['Point_2D', 'comparePoints'])))
                {
                    $rgIntersections[array_keys($rgPair)[1]][]=$rgIntersection['set'][0];
                }
            }
        }
        $rgIntersections = array_map(function($rgPoints)
        {
            usort($rgPoints, ['Point_2D', 'comparePoints']);
            return $rgPoints;
        }, $rgIntersections);
        foreach($rgIntersections as $iPrimaryKey=>$rgPoints)
        {
            $this->_rgPartition = array_merge($this->_rgPartition, $this->_build_vectors($rgPoints));
        }
        return $this->_rgPartition;
    }
    
    protected function _build_vectors($rgPoints)
    {
        $rgResult   = array();
        $rgMirror   = [$rgPoints, array_reverse($rgPoints)];
        foreach($rgMirror as $rgDirection)
        {
            for($i=1;$i<count($rgDirection); $i++)
            {
                $rgResult[] = new Line_2D($rgDirection[$i-1], $rgDirection[$i]);
            }
        }
        return $rgResult;
    }
    
    protected function _get_transitions(Line_2D $rVector)
    {
        return array_filter($this->getPartition(), function($rLine) use ($rVector)
        {
            return $rLine->getFrom()->isEqual($rVector->getTill()) && (!$rLine->getTill()->isEqual($rVector->getFrom()));
        });
    }
    
    protected function _get_way_loopback(Line_2D $rVector, $rgPath)
    {
        if(count(array_uintersect([$rVector->getTill()], array_map(function($rLine)
        {
            return $rLine->getFrom();
        }, $rgPath), ['Point_2D', 'comparePoints'])))
        {
            $iStart     = 0;
            while(!$rVector->getTill()->isEqual($rgPath[$iStart]->getFrom()))
            {
                $iStart++;
            }
            return array_map(function($mItem)
            {
                return $mItem->getFrom();
            }, array_merge(array_slice($rgPath, $iStart), [$rVector]));
            
        }
        return false;
    }
}