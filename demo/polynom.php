<?php
/**
 * Demo for polynom operations
 */
error_reporting(2047);
//1-st equation
$rA = new Entity_Coefficient('A');
$rB = new Entity_Coefficient('B');
$rC = new Entity_Coefficient('C');
$rD = new Entity_Coefficient('D');
$rE = new Entity_Coefficient('E');
$rF = new Entity_Coefficient('F');
//2-nd equation
$rP = new Entity_Coefficient('P');
$rQ = new Entity_Coefficient('Q');
$rR = new Entity_Coefficient('R');
$rS = new Entity_Coefficient('S');
$rT = new Entity_Coefficient('T');
$rU = new Entity_Coefficient('U');
//vars
$rX = new Entity_Polynom(['x'=>1]);
$rY = new Entity_Polynom(['y'=>1]);

$rAlpha = new Entity_Polynom([
    ['coef' => $rC, 'data'=>['y'=>1]],
    ['coef' => $rD, 'data'=>[]]
]);
$rBeta  = new Entity_Polynom([
    ['coef' => $rB, 'data'=>['y'=>2]],
    ['coef' => $rE, 'data'=>['y'=>1]],
    ['coef' => $rF, 'data'=>[]]
]);
$rDelta = $rAlpha->getPower(2)->getSubtract($rBeta->getProduct($rA)->getProduct(4));

$rgDzeta= [
    $rAlpha->getPower(2)->getProduct($rP),
    $rDelta->getProduct($rP),
    $rY->getPower(2)->getProduct($rQ)->getProduct(4),
    $rY->getProduct($rAlpha)->getProduct($rR)->getProduct(-2),
    $rAlpha->getProduct($rS)->getProduct(-2),
    $rY->getProduct($rT)->getProduct(4),
    $rU->getProduct(4)
];
$rDzeta = Array_Operations::array_usum($rgDzeta, function($rL, $rR)
{
    return $rL->getSum($rL);
})->getPower(2);
$rgTeta = [
    $rY->getProduct($rR)->getProduct(2),
    $rS->getProduct(2),
    $rAlpha->getProduct($rP)->getProduct(-2)
];
$rTeta  = Array_Operations::array_usum($rgTeta, function($rL, $rR)
{
    return $rL->getSum($rL);
})->getPower(2)->getProduct($rDelta);

$rEquation  = $rDzeta->getSubtract($rTeta);

            
echo $rAlpha->formatAsString().PHP_EOL;
echo $rBeta->formatAsString().PHP_EOL;
echo $rDelta->formatAsString().PHP_EOL;
echo $rDzeta->formatAsString().PHP_EOL;
echo $rTeta->formatAsString().PHP_EOL;
echo $rEquation->formatAsString().PHP_EOL;