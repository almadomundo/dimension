<?php
/**
 * Routines for array operations
 */
class Array_Operations extends Float_Operations
{
    /**
     * Get sum of array's elements using user-defined callback function
     * @param array $rgData
     * @param callable $fnCallback
     * @return null | array
     */
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
    /**
     * Map a function for all array's elements, using both key and value for each element. Either function or array could be passed in any order.
     * @param mixed $mData
     * @param mixed $mCallback
     * @param array $mParams
     * @return null | array
     */
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
    /**
     * Split array for groups of elements using user-defined callback function
     * @param array $rgData Array, elements of which will be splitted into groups
     * @param callable $fnCallback Function for grouping elements of array
     * @param boolean $bStoreGroup Flag to store value of callback in result array's keys
     * @return null | array
     */
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
    /**
     * Search key in an array, using user-defined function of comparing
     * @param mixed $mItem Item, key of which should be found
     * @param array $rgData Array to search in
     * @param callable $fnCallback User-defined comparison function
     * @return boolean | mixed
     */
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
    /**
     * Determine if element belongs to an array, using user-defined comparison function 
     * @param mixed $mItem Item, which is checking
     * @param array $rgData Array to search in
     * @param callable $fnCallback User-defined comparison function
     * @return boolean
     */
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
    /**
     * Get unique array items, using user-defined comparison function
     * @param array $rgData Array, consisting original data
     * @param callable $fnCompare User-defined callback to compare elements
     * @return null | array
     */
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
    /**
     * Returns decart product for array, excluding repeating
     * @param array $rgData Array, consisting original data
     * @param boolean $bPreserveOrder If true, result pair's key will be set to function, that was using for pair definition
     * @return array
     */
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
    /**
     * One of possible functions, that generates unique pair key
     * @return int
     */
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