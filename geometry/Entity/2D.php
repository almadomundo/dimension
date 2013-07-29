<?php
/**
 * Base class for all 2D figures and entities
 */
abstract class Entity_2D extends Float_Operations
{
    /**
     * Produce set of intersections for two 2D entitied
     */
    abstract public function getIntersection($mEntity);
    /**
     * Rotate rectangle Decart coorditantes by 2D-angle
     */
    abstract public function rotateCoordinates(Angle_2D $rAngle);
    /**
     * Shift rectangle Decart coorditantes by 2D-point
     */
    abstract public function shiftCoordinates(Point_2D $rPoint);
}