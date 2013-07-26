<?php
/**
 * Provides functionality to describe algebraic coefficient. Common structure:
 * (Real Coefficient #0)*A0^(Real Power #0:0)*A1^(Real Power #0:1)*... +
 * (Real Coefficient #1)*B0^(Real Power #1:0)*B1^(Real Power #1:1)*... +
 * ...
 */
class Entity_Coefficient extends Float_Operations
{
    /**
     * Container for coefficient's structure
     * @_rgData array
     */
    protected $_rgData = [];
    /**
     * 
     * @param mixed $mData Structure with coefficient data. Can be either array or string
     * @param boolean $bResolveAsString Flag to resolve input structure as string instead of converting to array
     */
    public function __construct($mData, $bResolveAsString=false)
    {
        if($bResolveAsString)
        {
            $this->_resolve_by_string($mData);
        }
        else
        {
            $this->_resolve_by_array($mData);
        }
        $this->groupMembers();
    }
    /**
     * Compare two coefficients rX, rY and return -1 if rX<rY; 0 if rX=rY; 1 if rX>rY
     * @param mixed $rX First coefficient in comparison pair
     * @param mixed $rY Second coefficient in comparison pair
     * @return integer
     * @throws Exception if operands could not be traited as instance of coefficient
     */
    public static function compareCoefs($rX, $rY)
    {
        if(is_numeric($rX))
        {
            $rX = self::convertNumber($rX);
        }
        if(is_numeric($rY))
        {
            $rY = self::convertNumber($rY);
        }
        if($rX instanceof self && $rY instanceof self)
        {
            $rgX    = $rX->getData();
            $rgY    = $rY->getData();
            usort($rgX, function($rgL, $rgR)
            {                
                return strcasecmp(serialize($rgL), serialize($rgR));
            });
            usort($rgY, function($rgL, $rgR)
            {                
                return strcasecmp(serialize($rgL), serialize($rgR));
            });
            return strcasecmp(serialize($rgX), serialize($rgY));
        }
        throw new Exception('Unsupported operand types');
    }
    /**
     * Convert data to instance of coefficient
     * @param mixed $mCoef Either number or coefficient to convert to instance of self
     * @return \self
     * @throws Exception if data could not be converted to instance of coefficient
     */
    public static function convertToCoef($mCoef)
    {
        if(is_numeric($mCoef))
        {
            return self::convertNumber($mCoef);
        }
        elseif(!($mCoef instanceof self))
        {
            throw new Exception('Member should be numeric or instance of Coefficient');
        }
        return $mCoef;
    }
    /**
     * Convert coefficient to real number. If contains undefined values, null will be returned
     * @param self $rCoef Coefficient, from which number will be extracted
     * @return null|double
     */
    public static function convertCoef(self $rCoef)
    {
        $rgCoef = $rCoef->getData();
        if(count($rgCoef)==1 && !count($rgCoef[0]['data']))
        {
            return $rgCoef[0]['coef'];
        }
        return null;
    }
    /**
     * Convert number to an instance of coefficient
     * @param double $fNumber A number to convert to instance of coefficient
     * @return null|\self
     */
    public static function convertNumber($fNumber)
    {
        if(!is_numeric($fNumber))
        {
            return null;
        }
        if(self::compareAsFloats(0, $fNumber))
        {
            return new self([]);
        }
        return new self(['coef'=>(double)$fNumber, 'data'=>[]]);
    }
    
    public function __sleep()
    {
        return ['_rgData'];
    }
    /**
     * Get full coefficient's data structure
     * @return array
     */
    public function getData()
    {
        return $this->_rgData;
    }
    /**
     * Dump coefficient as a string, formatting members with given rules
     * @param boolean $bDumpFull Flag of full dumping. If true, all delimiters will bw shown even if algebraic rules allows to skip them
     * @param null|array $mFormatter If set, this should be an array with delimiting rules. See Formatter_Polynom for examples
     * @return string
     */
    public function formatAsString($bDumpFull=false, $mFormatter=null)
    {
        if(!isset($mFormatter))
        {
            $mFormatter = Formatter_Polynom::getPolynomFormat();
        }
        if(!count($this->getData()))
        {
            return '0';
        }
        if($bDumpFull)
        {
            return join($mFormatter[Formatter_Polynom::DELIMITER_MEMBER], Array_Operations::array_map_assoc($this->getData(), [$this,'formatExpression'], [$bDumpFull, $mFormatter]));
        }
        return preg_replace('/^\s*\+\s*/', '', join('', Array_Operations::array_map_assoc($this->getData(), [$this,'formatExpression'], [$bDumpFull, $mFormatter])));
    }
    /**
     * Dump a member as a string
     * @param array $rgData Array containing member data
     * @param mixed $iNumber Number of member in coefficient's structure
     * @param boolean $bDumpFull Flag of full dumping. If true, all delimiters will bw shown even if algebraic rules allows to skip them
     * @param null|array $mFormatter If set, this should be an array with delimiting rules. See Formatter_Polynom for examples
     * @return string
     */
    public function formatExpression($rgData, $iNumber=0, $bDumpFull=false, $mFormatter=null)
    {
        if(!isset($mFormatter))
        {
            $mFormatter = Formatter_Polynom::getPolynomFormat();
        }
        if($bDumpFull)
        {
            return $mFormatter[Formatter_Polynom::ENCLOSER_COEFFICIENT_LEFT].
                   str_replace('{coef}',$rgData['coef'], $mFormatter[Formatter_Polynom::CONTAINER_COEFFICIENT]).
                   $mFormatter[Formatter_Polynom::ENCLOSER_COEFFICIENT_RIGHT].
                   $mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT].
                   $mFormatter[Formatter_Polynom::ENCLOSER_MEMBER_LEFT].
                   join($mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT], Array_Operations::array_map_assoc($rgData['data'], [$this, 'formatPower'], [$bDumpFull, $mFormatter])).
                   $mFormatter[Formatter_Polynom::ENCLOSER_MEMBER_RIGHT];
        }
        //members
        $sMembers   = join($mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT], Array_Operations::array_map_assoc($rgData['data'], [$this, 'formatPower'], [$bDumpFull, $mFormatter]));
        //coefficient
        if(self::compareAsFloats(1, $rgData['coef']))
        {
            $sCoef  = self::getAlgebraicSign($rgData['coef']);
        }
        else
        {
            $sCoef  = str_replace('{coef}', self::getAlgebraicSign($rgData['coef']).abs($rgData['coef']), $mFormatter[Formatter_Polynom::CONTAINER_COEFFICIENT]);
            if(count($rgData['data']))
            {
                $sCoef  = $sCoef.$mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT];
            }
        }
        return $sCoef.$sMembers;
    }
    /**
     * Dump a member power as a string
     * @param double $fPower Power number
     * @param string $mMember String of base representation
     * @param boolean $bDumpFull Flag of full dumping. If true, all delimiters will bw shown even if algebraic rules allows to skip them
     * @param null|array $mFormatter If set, this should be an array with delimiting rules. See Formatter_Polynom for examples
     * @return string
     */
    public function formatPower($fPower, $mMember, $bDumpFull=false, $mFormatter=null)
    {
        if(!isset($mFormatter))
        {
            $mFormatter = Formatter_Polynom::getPolynomFormat();
        }
        if($bDumpFull)
        {
            return $mFormatter[Formatter_Polynom::ENCLOSER_MEMBER_LEFT].
                   str_replace('{member}',$mMember, $mFormatter[Formatter_Polynom::CONTAINER_MEMBER]).
                   $mFormatter[Formatter_Polynom::ENCLOSER_MEMBER_RIGHT].
                   $mFormatter[Formatter_Polynom::DELIMITER_POWER].
                   $mFormatter[Formatter_Polynom::ENCLOSER_POWER_LEFT].
                   str_replace('{power}', $fPower, $mFormatter[Formatter_Polynom::CONTAINER_POWER]).
                   $mFormatter[Formatter_Polynom::ENCLOSER_POWER_RIGHT];
        }
        //power:
        if(self::compareAsFloats(1, $fPower))
        {
            $sPower = '';
        }
        else
        {
            $sPower = str_replace('{power}', $fPower, $mFormatter[Formatter_Polynom::CONTAINER_POWER]);
            if(self::compareLT($fPower, 0))
            {
                $sPower = $mFormatter[Formatter_Polynom::ENCLOSER_POWER_LEFT].$fPower.$mFormatter[Formatter_Polynom::ENCLOSER_POWER_RIGHT];
            }
            $sPower = $mFormatter[Formatter_Polynom::DELIMITER_POWER].$sPower;
        }
        //member:
        $sMember    = str_replace('{member}',$mMember, $mFormatter[Formatter_Polynom::CONTAINER_MEMBER]);
        return $sMember.$sPower;
    }
    /**
     * Get sum of two coefficients
     * @param self $rCoef Coefficient to sum with
     * @return \self
     */
    public function getSum(self $rCoef)
    {
        return new self(array_merge($this->getData(), $rCoef->getData()));
    }
    /**
     * Get subtract of two coefficients
     * @param self $rCoef Coefficient to subtract with
     * @return \self
     */
    public function getSubtract($mCoef)
    {
        $mCoef   = self::convertToCoef($mCoef);
        return $this->getSum($mCoef->getProduct(-1));
    }

    /**
     * Direct add member to coefficient and group members. Deprecated since getSum() method provides same feature
     * @param type $mMember
     * @return type
     */
    public function addMember($mMember)
    {
        if(!is_array($mMember))
        {
            $mMember = ['coef'=>(double)1, 'data'=>[$mMember=>(double)1]];
        }
        $this->_rgData[]=$mMember;
        return $this->groupMembers();
    }
    /**
     * Group members of coefficient according to algebraic rules
     * @return \self
     */
    public function groupMembers()
    {
        $rgGroup    = Array_Operations::array_split($this->getData(), function($rgItem)
        {
            ksort($rgItem['data']);
            return serialize($rgItem['data']);
        });
        $rgGroup    = array_map(function($rgItems)
        {
            return ['coef'=>(double)array_sum(array_column($rgItems, 'coef')),
                    'data'=>$rgItems[0]['data']
            ];
        }, $rgGroup);
        $this->_rgData  = array_filter($rgGroup, function($rgItem)
        {
            return !self::compareAsFloats(0, $rgItem['coef']);
        });
        
        $this->_rgData  = array_map(function($rgItem)
        {
            return [
                'coef'  => $rgItem['coef'],
                'data'  => array_filter($rgItem['data'], function($fPower)
                {
                    return !self::compareAsFloats(0, $fPower);
                })
            ];
        }, $this->_rgData);
        return $this;
    }
    /**
     * Get power of coefficient according to algebraic rules
     * @param integer $iPower Positive integer number for power
     * @return \self
     * @throws LogicException If number is not integer or not positive
     */
    public function getPower($iPower)
    {
        if(!is_int($iPower) || $iPower<0)
        {
            throw new LogicException('Power should be positive integer');
        }
        if(!$iPower)
        {
            return self::convertNumber(1);
        }
        $rCoef = clone $this;
        for($i=1;$i<$iPower;$i++)
        {
            $rCoef = $rCoef->getProduct($this);
        }
        return $rCoef;
    }
    /**
     * Get product of two coefficients according to algebraic rules
     * @param mixed $mCoef A coefficient to multiple with. If it is not an instance of self, conversion will be maded
     * @return \self
     */
    public function getProduct($mCoef)
    {
        if(is_numeric($mCoef))
        {
            $mCoef = self::convertNumber($mCoef);
        }
        $rgLeft = $this->getData();
        $rgRight= $mCoef->getData();
        $rgCoef = [];
        foreach($rgLeft as $rgCoefLeft)
        {
            foreach($rgRight as $rgCoefRight)
            {
                $rgCoef[] = ['coef'=>$rgCoefLeft['coef']*$rgCoefRight['coef'],
                             'data'=>$this->_get_product($rgCoefLeft['data'], $rgCoefRight['data'])
                ];
            }
        }
        return new self($rgCoef);
    }
    
    protected function _get_product($rgL, $rgR)
    {
        $rgR = array_merge($rgR, array_map(function($x)
        {
            return 0;
        }, array_diff_key($rgL, $rgR)));
        $rgL = array_merge($rgL, array_map(function($x)
        {
            return 0;
        }, array_diff_key($rgR, $rgL)));
        foreach($rgL as $sMember=>$fPower)
        {
            $rgL[$sMember]+=(double)$rgR[$sMember];
        }
        return array_filter($rgL, function($fValue)
        {
            return !self::compareAsFloats(0, $fValue);
        });
    }
    
    protected function _resolve_by_array($mData)
    {
        if(!is_array($mData))
        {
            $mData = [['coef'=>(double)1, 'data'=>[$mData=>(double)1]]];
        }
        $rgKeys = array_keys($mData);
        if(count($rgKeys)==2 && $rgKeys[0]=='coef' && $rgKeys[1]=='data')
        {
            $mData=[$mData];
        }
        elseif(count($rgKeys)==1 && is_numeric($mData[$rgKeys[0]]))
        {
            $mData=[['coef'=>1, 'data'=>[$rgKeys[0]=>(double)$mData[$rgKeys[0]]]]];
        }
        /*
        if($bMergeMembers)
        {
            foreach($mData as $mMember)
            {
                if(count($mMember['data']))
                {
                    foreach($mMember['data'] as $mVar=>$mPower)
                    {
                        $this->_rgData[]=['coef'=>$mMember['coef'], 'data'=>[$mVar=>$mPower]];
                    }
                }
                else
                {
                    $this->_rgData[]=$mMember;
                }
            }
        }
        else
        {
         */
            $this->_rgData  = $mData;
        //}
    }
    
    protected function _resolve_by_string()
    {
        
    }
}