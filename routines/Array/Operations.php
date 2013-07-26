<?php
class Array_Operations extends Float_Operations
{
    public static function array_usum($rgData, $fnCallback=null)    
    {
        if(!isset($fnCallback))
        {
            return array_sum($rgData);
        }
        if(!is_callable($fnCallback) || !count($rgData))
        {
            return null;
        }
        if(count($rgData)==1)
        {
            return $rgData[0];
        }
        $mResult = call_user_func_array($fnCallback, [$rgData[0], $rgData[1]]);
        for($i=2;$i<count($rgData);$i++)
        {
            $mResult = call_user_func_array($fnCallback, [$mResult, $rgData[$i]]);
        }
        return $mResult;
    }
    //always hated difference in order of callback-data in array_map/array_filter:
    public static function array_map_assoc($mData, $mCallback, $mParams=[])
    {
        $rgData     = [];
        $fnCallback = null;
        if(is_callable($mData))
        {
            $rgData     = $mCallback;
            $fnCallback = $mData;
        }
        elseif(is_callable($mCallback))
        {
            $rgData     = $mData;
            $fnCallback = $mCallback;
        }
        //var_dump($mData, $mCallback, $rgData, $fnCallback);exit;
        if(!is_array($rgData) || !is_callable($fnCallback))
        {
            return null;
        }
        foreach($rgData as $mKey=>$mValue)
        {
            $rgData[$mKey] = call_user_func_array($fnCallback, array_merge([$mValue, $mKey], $mParams));
        }
        return $rgData;
    }
    public static function array_split($rgData, $fnCallback, $bStoreGroup=false)
    {
       if(!is_callable($fnCallback))
       {
          return null;
       }
       $rgResult = array();
       foreach($rgData as $mValue)
       {
          $mFunctional = call_user_func($fnCallback, $mValue);
          $sKey = $bStoreGroup?$mFunctional:serialize($mFunctional);
          if(is_object($sKey))
          {
             try
             {
                $sKey=(string)$sKey;
             }
             catch(Exception $rException)
             {
                return null;
             }
          }
          elseif(!is_scalar($sKey))
          {
             return null;
          }
          if(array_key_exists($sKey, $rgResult))
          {
             $rgResult[$sKey][]=$mValue;
          }
          else
          {
             $rgResult[$sKey] = array($mValue);
          }
       }
       return $bStoreGroup?$rgResult:array_values($rgResult);
    }
    
    public static function array_usearch($mItem, $rgData, $fnCallback=null)
    {
        if(!isset($fnCallback))
        {
            return array_search($mItem, $rgData);
        }
        foreach($rgData as $mKey=>$mElement)
        {
            //! ?
            if(call_user_func_array($fnCallback, [$mItem, $mElement]))
            {
                return $mKey;
            }
        }
        return false;//as in array_search, but bad practice to mix out-type
    }
    
    public static function array_consists($mItem, $rgData, $fnCallback=null)
    {
        if(!isset($fnCallback))
        {
            return in_array($mItem, $rgData);
        }
        foreach($rgData as $mElement)
        {
            if(!call_user_func_array($fnCallback, [$mItem, $mElement]))
            {
                return true;
            }
        }
        return false;
    }
    
    public static function array_uunique($rgData, $fnCompare=null)
    {
        if(!isset($fnCompare))
        {
            return array_unique($rgData);
        }
        if(!is_callable($fnCompare))
        {
            return null;
        }
        if(!count($rgData))
        {
            return array();
        }
        $rgResult = array();
        foreach($rgData as $mItem)
        {
            foreach($rgResult as $mTest)
            {
               if(!call_user_func_array($fnCompare, [$mItem, $mTest]))
               {
                  continue 2;
               }
            }
            $rgResult[]=$mItem;
        }
        return $rgResult;
    }
    
    public static function array_repeat_pair($rgData, $bPreserveOrder=false)
    {
        $rgRepeats  = array();
        for($i=0;$i<count($rgData);$i++)
        {
            for($j=0;$j<count($rgData);$j++)
            {
                if($i!=$j && !array_key_exists(self::get_pair_key($i, $j), $rgRepeats))
                {
                    $rgRepeats[self::get_pair_key($i, $j)] = $bPreserveOrder?[$i=>$rgData[$i], $j=>$rgData[$j]]:[$rgData[$i], $rgData[$j]];
                }
            }
        }
        return $rgRepeats;
    }
    
    public static function get_pair_key()
    {
        $rgArgs = func_get_args();
        if(count($rgArgs)==2)
        {
            $iIndex0    = (int)$rgArgs[0];
            $iIndex1    = (int)$rgArgs[1];
        }
        elseif(count($rgArgs)==1 && is_array($rgArgs[0]))
        {
            $iIndex0    = (int)$rgArgs[0][0];
            $iIndex1    = (int)$rgArgs[0][1];
        }
        else
        {
            return 0;
        }
        return $iIndex0*$iIndex0 + $iIndex1*$iIndex1;
    }
}