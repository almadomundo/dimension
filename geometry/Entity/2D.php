<?php
abstract class Entity_2D extends Float_Operations
{
    abstract public function getIntersection($mEntity);
    abstract public function rotateCoordinates(Angle_2D $rAngle);
    abstract public function shiftCoordinates(Point_2D $rPoint);
}