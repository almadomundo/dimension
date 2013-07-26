<?php
class Float_Operations
{
    const MEMBER_SIGN_POSITIVE  = 1;
    const MEMBER_SIGN_NEGATIVE  = -1;
    const PRECISION_DELTA       = 1E-13;
    
    public static function compareAsFloats($fX, $fY)
    {
            return abs($fX-$fY)<self::PRECISION_DELTA;
    }
    
    public static function compareGT($fX, $fY)
    {
        return $fX>$fY;
    }
    
    public static function compareGE($fX, $fY)
    {
        return self::compareAsFloats($fX, $fY) || self::compareGT($fX, $fY);
    }
    
    public static function compareLT($fX, $fY)
    {
        return self::compareGT($fY, $fX);
    }
    
    public static function compareLE($fX, $fY)
    {
        return self::compareGE($fY, $fX);
    }

    public static function getAlgebraicSignSingle($fNumeric)
    {
        return $fNumeric<0?'-':'';
    }
    
    public static function getAlgebraicSign($fNumeric)
    {
        return $fNumeric<0?'-':'+';
    }

    public static function getSign($fArg)
    {
        if($fArg<0)
        {
            return -1;
        }
        return 1;
    }

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