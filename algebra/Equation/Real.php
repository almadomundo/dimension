<?php
class Equation_Real extends Float_Operations
{
    const ROOT_EXACT = 'point';
    const ROOT_RANGE = 'range';
    const ROOT_RANGE_FROM = 'range_from';
    const ROOT_RANGE_TILL = 'range_till';
    const SOLVE_ALGEBRAIC = 1;
    const SOLVE_EXACT     = 0;

    protected $_iPower = 0;
    protected $_rgCoef = array();
	
    public function __construct()
    {
        $rgArgs = func_get_args();
        if(!count($rgArgs))
        {
            return null;
        }
        $rgCoef = is_array($rgArgs[0])?$rgArgs[0]:$rgArgs;
        $this->_iPower = count($rgCoef)-1;
        for($i=0; $i<=$this->_iPower; $i++)
        {
            $this->_rgCoef[$this->_iPower-$i] = $rgCoef[$i];
        }
        $i=$this->_iPower;
        //strip zero-multiplied high powers, if any, with reducing whole power:
        while(!$this->_rgCoef[$i] && $i>0)
        {
            unset($this->_rgCoef[$i]);
            $i--;
            $this->_iPower--;
        }
    }

    public function solveEquation($mFlag=self::SOLVE_EXACT)
    {
        $sMethod = '_solve_power_'.$this->_iPower;
        if(method_exists($this, $sMethod))
        {
            return $this->$sMethod($mFlag);
        }
        return null;
    }
    
    public function getAlgebraic($sVariable='z', $sPowerDelimiter='^')
    {
        $rgPrints = array();
        $rgCoef   = $this->_rgCoef;
        krsort($rgCoef);
        array_walk($rgCoef, function($fValue, $iKey) use (&$rgPrints, $sVariable, $sPowerDelimiter)
        {
            if($fValue)
            {
                $rgPrints[] = $iKey?($fValue!=1?$fValue:'').$sVariable.($iKey!=1?$sPowerDelimiter.$iKey:''):$fValue;
            }
        });
        return str_replace('+ -','- ',join(' + ', $rgPrints));
    }

    protected function _solve_power_0($mFlag=self::SOLVE_EXACT, $rgCoef=null)
    {
        $rgCoef=isset($rgCoef)&&is_array($rgCoef)?$rgCoef:$this->_rgCoef;
        if(!$rgCoef[0])
        {
            return array(
                    self::ROOT_RANGE=>array(self::ROOT_RANGE_FROM=>-INF, self::ROOT_RANGE_TILL=>INF),
                    );
        }
        else
        {
            return array();
        }
    }

    protected function _solve_power_1($mFlag=self::SOLVE_EXACT, $rgCoef=null)
    {
        $rgCoef=isset($rgCoef)&&is_array($rgCoef)?$rgCoef:$this->_rgCoef;
        if($mFlag==self::SOLVE_ALGEBRAIC)
        {
            $sCoef   = -1*($rgCoef[0]/$rgCoef[1])<0?'-':'';
            $mResult = $rgCoef[0]?$sCoef.abs($rgCoef[0]).'/'.abs($rgCoef[1]):'0';
        }
        else
        {
            $mResult = -1*$rgCoef[0]/$rgCoef[1];
        }
        return array(self::ROOT_EXACT=>array($mResult));
    }

    protected function _solve_power_2($mFlag=self::SOLVE_EXACT, $rgCoef=null)
    {
        $rgCoef=isset($rgCoef)&&is_array($rgCoef)?$rgCoef:$this->_rgCoef;
        $fDiscriminant = pow($rgCoef[1],2)-4*$rgCoef[0]*$rgCoef[2];
        if(self::compareAsFloats($fDiscriminant, 0))
        {
            $sCoef	 = -1*($rgCoef[1]/$rgCoef[2])<0?'-':'';
            $mResult = $mFlag==self::SOLVE_EXACT?-1*$rgCoef[1]/(2*$rgCoef[2]):$sCoef.abs($rgCoef[1]).'/(2*'.abs($rgCoef[2]).')';
            return array(
                    self::ROOT_EXACT=>array($mResult)
            );
        }
        elseif($fDiscriminant<0)
        {
            return array();
        }

        else
        {
            if($mFlag==self::SOLVE_EXACT)
            {
                $rgRoots = array(
                        (-1*$rgCoef[1]+sqrt($fDiscriminant))/(2*$rgCoef[2]),
                        (-1*$rgCoef[1]-sqrt($fDiscriminant))/(2*$rgCoef[2]));
            }
            else
            {
                $sCoefB = self::getAlgebraicSignSingle(-1*$rgCoef[1]);
                $sCoefA = self::getAlgebraicSignSingle(-1*$rgCoef[2]);
                $sCoefD = -1*$rgCoef[2]*$rgCoef[0]<0?'-':'+';
                $sSummD = $rgCoef[0]?$sCoefD.'4*'.abs($rgCoef[2]).'*'.abs($rgCoef[0]):'';
                $sSummB0= '';
                $sSummB1= '';
                if($rgCoef[1])
                {
                        $sSummB0 = $sCoefB.abs($rgCoef[1]);
                        $sSummB1 = 'pow('.$rgCoef[1].',2)';
                }
                $rgRoots = array(
                        $sCoefA.'('.$sSummB0.'+sqrt('.$sSummB1.$sSummD.'))/(2*'.$rgCoef[2].')',
                        $sCoefA.'('.$sSummB0.'-sqrt('.$sSummB1.$sSummD.'))/(2*'.$rgCoef[2].')'
                );
            }
            return array(self::ROOT_EXACT=>$rgRoots);
        }
    }
    //Viett method on resolving 3-power method
    protected function _solve_power_3($mFlag=self::SOLVE_EXACT, $rgCoef=null)
    {
        $rgCoef     = isset($rgCoef)&&is_array($rgCoef)?$rgCoef:$this->_rgCoef;
        $fMajor     = $rgCoef[3];
        $rgCoef     = array_map(function($fItem) use ($fMajor)
        {
            return $fItem/$fMajor;
        }, $rgCoef);
        $fQ         = (pow($rgCoef[2],2)-3*$rgCoef[1])/9;
        $fR         = (2*pow($rgCoef[2],3)-9*$rgCoef[2]*$rgCoef[1]+27*$rgCoef[0])/54;
        $fS         = pow($fQ,3)-pow($fR,2);
        if($fS>0)
        {
            $fPhi   = acos($fR/pow($fQ,1/3))/3;
            $rgRoots= array(
                -2*pow($fQ,1/2)*cos($fPhi)-$rgCoef[2]/3,
                -2*pow($fQ,1/2)*cos($fPhi+2*pi()/3)-$rgCoef[2]/3,
                -2*pow($fQ,1/2)*cos($fPhi-2*pi()/3)-$rgCoef[2]/3,
            );
        }
        elseif($fS<0)
        {
            if($fQ>0)
            {
                $fPhi   = acosh(abs($fR)/self::getAlgebraicRoot(pow(abs($fQ),3),2))/3;
                $rgRoots= array(-2*self::getSign($fR)*self::getAlgebraicRoot(abs($fQ),2)*cosh($fPhi)-$rgCoef[2]/3);
            }
            else
            {
                $fPhi   = asinh(abs($fR)/self::getAlgebraicRoot(pow(abs($fQ),3),2))/3;
                $rgRoots= array(-2*self::getSign($fR)*self::getAlgebraicRoot(abs($fQ),2)*sinh($fPhi)-$rgCoef[2]/3);
            }
        }
        else
        {
            if($fR)
            {
                $rgRoots=array(
                    -2*self::getAlgebraicRoot($fR,3)-$rgCoef[2]/3,
                    self::getAlgebraicRoot($fR,3)-$rgCoef[2]/3
                );
            }
            else
            {
                $rgRoots=array(-1*$rgCoef[2]/3);
            }
        }
        $rgRoots    = array_map(function($fItem)
        {
            return self::compareAsFloats(round($fItem), $fItem)?round($fItem):$fItem;
        }, $rgRoots);
        return array(self::ROOT_EXACT=>$rgRoots);
    }
    //Ferrari method of resolving 4-power equation
    protected function _solve_power_4($mFlag=self::SOLVE_EXACT, $rgCoef=null)
    {
        $rgCoef     = isset($rgCoef)&&is_array($rgCoef)?$rgCoef:$this->_rgCoef;
        $fAlpha     = (-3*pow($rgCoef[3],2))/(8*pow($rgCoef[4],2))+$rgCoef[2]/$rgCoef[4];
        $fBeta      = pow($rgCoef[3],3)/(8*pow($rgCoef[4],3))-($rgCoef[3]*$rgCoef[2])/(2*pow($rgCoef[4],2))+$rgCoef[1]/$rgCoef[4];
        $fGamma     = (-3*pow($rgCoef[3],4))/(256*pow($rgCoef[4],4))+pow($rgCoef[3],2)*$rgCoef[2]/(16*pow($rgCoef[4],3))-$rgCoef[3]*$rgCoef[1]/(4*pow($rgCoef[4],2))+$rgCoef[0]/$rgCoef[4];
        $rgRoots    = array();
        if(self::compareAsFloats(0, $fBeta))
        {
            if(($fResolventaD = pow($fAlpha,2)-4*$fGamma)<0)
            {
                return array();
            }
            if(($fSqrtNeg = -1*$fAlpha-self::getAlgebraicRoot($fResolventaD, 2))>=0)
            {
                $rgRoots = array_merge($rgRoots, array(
                    -1*$rgCoef[3]/(4*$rgCoef[4])+self::getAlgebraicRoot($fSqrtNeg/2, 2),
                    -1*$rgCoef[3]/(4*$rgCoef[4])-self::getAlgebraicRoot($fSqrtNeg/2, 2)
                ));
            }
            if(($fSqrtPos = -1*$fAlpha+self::getAlgebraicRoot($fResolventaD, 2))>=0)
            {
                $rgRoots = array_merge($rgRoots, array(
                    -1*$rgCoef[3]/(4*$rgCoef[4])+self::getAlgebraicRoot($fSqrtPos/2, 2),
                    -1*$rgCoef[3]/(4*$rgCoef[4])-self::getAlgebraicRoot($fSqrtPos/2, 2)
                ));
            }
        }
        else
        {
            $fP = -1*pow($fAlpha,2)/12-$fGamma;
            $fQ = -1*pow($fAlpha,3)/108+$fAlpha*$fGamma/3-pow($fBeta,2)/8;
            if(($fResolventaD = pow($fQ,2)/4+pow($fP,3)/27)<0)
            {
                return array();
            }
            $fR = -1*$fQ/2+self::getAlgebraicRoot($fResolventaD, 2);
            $fU = self::getAlgebraicRoot($fR, 3);
            $fY = -5*$fAlpha/6+$fU+($fU?((-1*$fP)/(3*$fU)):(-1*self::getAlgebraicRoot($fQ, 3)));
            if(($fW = $fAlpha+2*$fY)<0)
            {
                return array();
            }
            $fW = self::getAlgebraicRoot($fW, 2);
            if(($fSqrtNeg = -1*(3*$fAlpha+2*$fY-2*$fBeta/$fW))>=0)
            {
                $rgRoots = array_merge($rgRoots, array(
                    -1*$rgCoef[3]/(4*$rgCoef[4])+(-1*$fW+self::getAlgebraicRoot($fSqrtNeg, 2))/2,
                    -1*$rgCoef[3]/(4*$rgCoef[4])+(-1*$fW-self::getAlgebraicRoot($fSqrtNeg, 2))/2
                ));
            }
            if(($fSqrtPos = -1*(3*$fAlpha+2*$fY+2*$fBeta/$fW))>=0)
            {
                $rgRoots = array_merge($rgRoots, array(
                    -1*$rgCoef[3]/(4*$rgCoef[4])+($fW+self::getAlgebraicRoot($fSqrtPos, 2))/2,
                    -1*$rgCoef[3]/(4*$rgCoef[4])+($fW-self::getAlgebraicRoot($fSqrtPos, 2))/2
                ));
            }
        }
        //since Ferrari method have no guarantee from duplicate roots, do following:
        $rgRoots    = array_unique(array_map(function($fItem)
        {
            return self::compareAsFloats(round($fItem), $fItem)?round($fItem):$fItem;
        }, $rgRoots));
        return array(self::ROOT_EXACT=>$rgRoots);
    }
}