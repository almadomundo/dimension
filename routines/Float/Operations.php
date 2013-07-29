<?php
/**
 * Routines for floating-point operations
 */
class Float_Operations
{
    const MEMBER_SIGN_POSITIVE  = 1;
    const MEMBER_SIGN_NEGATIVE  = -1;
    /**
     * Basic precision delta. Changing this will affect globally the entire library
     */
    const PRECISION_DELTA       = 1E-13;
    /**
     * Compare two floats on equality
     * @param double $fX
     * @param double $fY
     * @return boolean
     */
    public static function compareAsFloats($fX, $fY)
    {
            return abs($fX-$fY)<self::PRECISION_DELTA;
    }
    /**
     * Compare two floats on 'great than'
     * @param double $fX
     * @param double $fY
     * @return boolean
     */
    public static function compareGT($fX, $fY)
    {
        return $fX>$fY;
    }
    /**
     * Compare two floats on 'great or equal than'
     * @param double $fX
     * @param double $fY
     * @return boolean
     */
    public static function compareGE($fX, $fY)
    {
        return self::compareAsFloats($fX, $fY) || self::compareGT($fX, $fY);
    }
    /**
     * Compare two floats on 'less than'
     * @param double $fX
     * @param double $fY
     * @return boolean
     */
    public static function compareLT($fX, $fY)
    {
        return self::compareGT($fY, $fX);
    }
    /**
     * Compare two floats on 'less or equal than'
     * @param double $fX
     * @param double $fY
     * @return boolean
     */
    public static function compareLE($fX, $fY)
    {
        return self::compareGE($fY, $fX);
    }
    /**
     * [Deprecated] Return string sign by algebraic rules
     * @param mixed $fNumeric
     * @return string
     */
    public static function getAlgebraicSignSingle($fNumeric)
    {
        return $fNumeric<0?'-':'';
    }
    /**
     * Return string sign of operand
     * @param mixed $fNumeric
     * @return string
     */
    public static function getAlgebraicSign($fNumeric)
    {
        return $fNumeric<0?'-':'+';
    }
    /**
     * Return integer coefficient for signed operand
     * @param mixed $fArg
     * @return int
     */
    public static function getSign($fArg)
    {
        if($fArg<0)
        {
            return -1;
        }
        return 1;
    }
    /**
     * Get radical of an operand
     * @param double $fArg An operand for radical base
     * @param integer $iRoot Integer value of radical power. If below zero, 1/radical will be assumed as a result
     * @return null| double
     */
    public static function getAlgebraicRoot($fArg, $iRoot)
    {
        if(!is_int($iRoot))
        {
            return null;
        }
        if(!$iRoot)
        {
            return $fArg?1:NaN;
        }
        if($fArg>=0)
        {
            return pow($fArg, 1/$iRoot);
        }
        else
        {
            if($iRoot%2)
            {
                return -1*pow(abs($fArg), 1/$iRoot);
            }
            else
            {
                return NaN;
            }
        }
    }
}