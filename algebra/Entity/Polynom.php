<?php
class Entity_Polynom extends Float_Operations
{
    protected $_rgData = [];
    
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
    
    public function getData()
    {
        return $this->_rgData;
    }
    
    public function getSum($mPolynom)
    {
        $mPolynom   = self::convertToPolynom($mPolynom);
        return new self(array_merge($this->getData(), $mPolynom->getData()));
    }
    
    public function getSubtract($mPolynom)
    {
        $mPolynom   = self::convertToPolynom($mPolynom);
        return $this->getSum($mPolynom->getProduct(-1));
    }
    
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
        $rPolynom = clone $this;
        for($i=1;$i<$iPower;$i++)
        {
            $rPolynom = $rPolynom->getProduct($this);
        }
        return $rPolynom;
    }
    
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
        return new self(['coef'=>Entity_Coefficient::convertNumber($fNumber), 'data'=>[]]);
    }
    
    public static function convertToPolynom($mPolynom)
    {
        if($mPolynom instanceof Entity_Coefficient)
        {
            $mPolynom   = new Entity_Polynom(['coef'=>$mPolynom, 'data'=>[]]);
        }
        elseif(is_numeric($mPolynom))
        {
            $mPolynom   = self::convertNumber($mPolynom);
        }
        elseif(!($mPolynom instanceof self))
        {
            throw new Exception('Member should be numeric or instance of either Polynom or Coefficient');
        }
        return $mPolynom;
    }
    
    public function formatAsString($bDumpFull=false, $mFormatter=null)
    {
        if(!count($this->getData()))
        {
            return 0;
        }
        if(!isset($mFormatter))
        {
            $mFormatter = Formatter_Polynom::getPolynomFormat();
        }
        //same in both cases?
        //if($bDumpFull)
        //{
            return join($mFormatter[Formatter_Polynom::DELIMITER_MEMBER], Array_Operations::array_map_assoc($this->getData(), [$this,'formatExpression'], [$bDumpFull, $mFormatter]));
        //}
    }
    
    public function formatExpression($rgData, $iNumber=0, $bDumpFull=false, $mFormatter=null)
    {
        if(!isset($mFormatter))
        {
            $mFormatter = Formatter_Polynom::getPolynomFormat();
        }
        if($bDumpFull)
        {
            return $mFormatter[Formatter_Polynom::ENCLOSER_COEFFICIENT_LEFT].
                   str_replace('{coef}',$rgData['coef']->formatAsString($bDumpFull, $mFormatter), $mFormatter[Formatter_Polynom::CONTAINER_COEFFICIENT]).
                   $mFormatter[Formatter_Polynom::ENCLOSER_COEFFICIENT_RIGHT].
                   $mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT].
                   join($mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT], Array_Operations::array_map_assoc($rgData['data'], [$this, 'formatPower'], [$bDumpFull, $mFormatter]));
        }
        //coefficient
        if(!Entity_Coefficient::compareCoefs($rgData['coef'], 1))
        {
            $sCoef = '';
        }
        else
        {
            $sCoef  = str_replace('{coef}',$rgData['coef']->formatAsString($bDumpFull, $mFormatter), $mFormatter[Formatter_Polynom::CONTAINER_COEFFICIENT]);
            $mCoef  = Entity_Coefficient::convertCoef($rgData['coef']);
            if(is_null($mCoef))
            {
                $sCoef = $mFormatter[Formatter_Polynom::ENCLOSER_COEFFICIENT_LEFT].$sCoef.$mFormatter[Formatter_Polynom::ENCLOSER_COEFFICIENT_RIGHT];
            }
            if(count($rgData['data']))
            {
                $sCoef  = $sCoef.$mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT];
            }
        }
        //members
        $sMembers   = join($mFormatter[Formatter_Polynom::DELIMITER_COEFFICIENT], Array_Operations::array_map_assoc($rgData['data'], [$this, 'formatPower'], [$bDumpFull, $mFormatter]));
        return $sCoef.$sMembers;
    }
    
    public function formatPower($mPower, $mMember, $bDumpFull=false, $mFormatter=null)
    {
        if(!isset($mFormatter))
        {
            $mFormatter = Formatter_Polynom::getPolynomFormat();
        }
        if($bDumpFull)
        {
            return $mFormatter[Formatter_Polynom::ENCLOSER_MEMBER_LEFT].
                   str_replace('{member}', $mMember, $mFormatter[Formatter_Polynom::CONTAINER_MEMBER]).
                   $mFormatter[Formatter_Polynom::ENCLOSER_MEMBER_RIGHT].
                   $mFormatter[Formatter_Polynom::DELIMITER_POWER].
                   $mFormatter[Formatter_Polynom::ENCLOSER_POWER_LEFT].
                   str_replace('{power}', $mPower->formatAsString($bDumpFull, $mFormatter), $mFormatter[Formatter_Polynom::CONTAINER_POWER]).
                   $mFormatter[Formatter_Polynom::ENCLOSER_POWER_RIGHT];
        }
        //power
        if(!Entity_Coefficient::compareCoefs($mPower, 1))
        {
            $sPower = '';
        }
        else
        {
            $sPower = str_replace('{power}', $mPower->formatAsString($bDumpFull, $mFormatter), $mFormatter[Formatter_Polynom::CONTAINER_POWER]);
            $mCoef  = Entity_Coefficient::convertCoef($mPower);
            if(is_null($mCoef) || self::compareLT($mCoef, 0))
            {
                $sPower = $mFormatter[Formatter_Polynom::ENCLOSER_POWER_LEFT].$sPower.$mFormatter[Formatter_Polynom::ENCLOSER_POWER_RIGHT];
            }
            $sPower = $mFormatter[Formatter_Polynom::DELIMITER_POWER].$sPower;
        }
        //member
        $sMember = str_replace('{member}', $mMember, $mFormatter[Formatter_Polynom::CONTAINER_MEMBER]);
        return $sMember.$sPower;
    }
    
    public function groupMembers()
    {
        $rgGroup    = Array_Operations::array_split($this->getData(), function($rgItem)
        {
            ksort($rgItem['data']);
            return serialize($rgItem['data']);
        });
        $rgGroup    = array_map(function($rgItems)
        {
            return ['coef'=>Array_Operations::array_usum(array_column($rgItems, 'coef'), function($rX, $rY)
                    {
                        return $rX->getSum($rY);
                    }),
                    'data'=>$rgItems[0]['data']
            ];
        }, $rgGroup);
        $this->_rgData  = array_filter($rgGroup, function($rgItem)
        {
            return count($rgItem['coef']->getData());
        });
        $this->_rgData  = array_map(function($rgItem)
        {
            return [
                'coef'  => $rgItem['coef'],
                'data'  => array_filter($rgItem['data'], function($mPower)
                {
                    return count($mPower->getData());
                })
            ];
        }, $this->_rgData);
        return $this;
    }
    
    public function getProduct($mPolynom)
    {
        $mPolynom   = self::convertToPolynom($mPolynom);
        $rgLeft     = $this->getData();
        $rgRight    = $mPolynom->getData();
        $rgCoef     = [];
        foreach($rgLeft as $rgCoefLeft)
        {
            foreach($rgRight as $rgCoefRight)
            {
                $rgCoef[] = ['coef'=>$rgCoefLeft['coef']->getProduct($rgCoefRight['coef']),
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
            return new Entity_Coefficient([]);
        }, array_diff_key($rgL, $rgR)));
        $rgL = array_merge($rgL, array_map(function($x)
        {
            return new Entity_Coefficient([]);
        }, array_diff_key($rgR, $rgL)));
        foreach($rgL as $sMember=>$mPower)
        {
            $rgL[$sMember]=$rgL[$sMember]->getSum($rgR[$sMember]);
        }
        return array_filter($rgL, function($mValue)
        {
            return count($mValue->getData());
        });
    }
    
    protected function _resolve_by_array($mData)
    {
        if(!is_array($mData))
        {
            $mData = [['coef'=>Entity_Coefficient::convertNumber(1), 'data'=>[$mData=>Entity_Coefficient::convertNumber(1)]]];
        }
        $rgKeys = array_keys($mData);
        if(count($rgKeys)==2 && $rgKeys[0]=='coef' && $rgKeys[1]=='data')
        {
            $mData=[$mData];
        }
        elseif(count($rgKeys)==1 && is_numeric($mData[$rgKeys[0]]))
        {
            $mData=[['coef'=>Entity_Coefficient::convertNumber(1), 'data'=>[$rgKeys[0]=>Entity_Coefficient::convertNumber($mData[$rgKeys[0]])]]];
        }
        foreach($mData as &$mMember)
        {
            foreach($mMember['data'] as $mKey=>&$mPower)
            {
                $mPower = Entity_Coefficient::convertToCoef($mPower);
            }
        }
        $this->_rgData  = $mData;
    }

    protected function _resolve_by_string($sData)
    {
        
    }
}
