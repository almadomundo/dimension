<?php
/**
 * Demo for polygons operations
 */
error_reporting(2047);

//define or polygon. Note that Polygon_2D can also be used
$rPolygon = new LineSet_2D(
    new Line_2D( 0, 3, 1, 1),
    new Line_2D( 1, 1, 3, 0),
    new Line_2D( 3, 0, 1,-1),
    new Line_2D( 1,-1, 0,-3),
    new Line_2D( 0,-3,-1,-1),
    new Line_2D(-1,-1,-3,0),
    new Line_2D(-3, 0,-1, 1),
    new Line_2D(-1, 1, 0, 3)
);
//define partition line set
$rPartition = new LineSet_2D(
    new Line_2D(-1, 1, 1,-1),
    new Line_2D(-1,-1, 1, 1)
);
//result line set:
$rResultSet    = LineSet_2D::createFromArray(array_merge(
    $rPolygon->getLines(), 
    $rPartition->getLines()
));
//for example, dump plain result:
var_dump($rResultSet->getPolygons());