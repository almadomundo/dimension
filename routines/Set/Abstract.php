<?php
/**
 * Represents set, containing abstract elements
 */
class Set_Abstract
{
    /**
     * Array to store set elements
     * @_rgItems array 
     */
    protected $_rgItems = [];
    /**
     * Construct a set from given elements
     * @return \self
     * @throws Exception If passed parameters contain resources
     */
    public function __construct()
    {
        $rgArgs = func_get_args();
        if(count($rgArgs)!=count(array_filter($rgArgs, function($mItem)
        {
            return !is_resource($mItem);
        })))
        {
            throw new Exception('Could not create a set containing non-serializable items');
        }
        $this->_rgItems = $rgArgs;
        return $this;
    }
    /**
     * Construct a set from array of elements
     * @return \self
     */
    public static function createFromArray()
    {
        return call_user_func_array([new ReflectionClass(__CLASS__), 'newInstanceArgs'], func_get_args());
    }
    /**
     * Get set data list
     * @return array
     */
    public function getData()
    {
        return $this->_rgItems;
    }
    /**
     * Check if passed set includes current set
     * @param self $rSet
     * @param callable | null $fnCallback Comparison function. If not set, items will be converted and compared as strings
     * @return boolean
     */
    public function isSubset(self $rSet, $fnCallback=null)
    {
        if(is_callable($fnCallback))
        {
            return count(array_uintersect($this->getData(), $rSet->getData(), $fnCallback))==count($this->getData());
        }
        $rgSelf = array_map('serialize', $this->getData());
        $rgData = array_map('serialize', $rSet->getData());
        return (bool)count(array_intersect($rgSelf, $rgData))==count($rgSelf);
    }
    /**
     * Check if current set includes passed set
     * @param self $rSet
     * @param callable | null $fnCallback Comparison function. If not set, items will be converted and compared as strings
     * @return boolean
     */
    public function isSupset(self $rSet, $fnCallback=null)
    {
        return $rSet->isSubset($this, $fnCallback);
    }
    /**
     * Check if passed set is equal to current set
     * @param self $rSet
     * @param callable | null $fnCallback Comparison function. If not set, items will be converted and compared as strings
     * @return boolean
     */
    public function isEqual(self $rSet, $fnCallback=null)
    {
        return $this->isSubset($rSet, $fnCallback) && $this->isSupset($rSet, $fnCallback);
    }
    /**
     * Check if current set is empty
     * @return boolean
     */
    public function isEmpty()
    {
        return !count($this->getData());
    }
    /**
     * Get intersection of passed and current sets
     * @param self $rSet
     * @param callable | null $fnCallback Comparison function. If not set, items will be converted and compared as strings
     * @return boolean
     */
    public function getIntersection($rSet, $fnCallback=null)
    {
        if(is_callable($fnCallback))
        {
            return self::createFromArray(array_uintersect($this->getData(), $rSet->getData(), $fnCallback));
        }
        $rgSelf = array_map('serialize', $this->getData());
        $rgData = array_map('serialize', $rSet->getData());
        return self::createFromArray(array_map('unserialize', array_intersect($rgSelf, $rgData)));
    }
    /**
     * Get subtract of passed and current sets
     * @param self $rSet
     * @param callable | null $fnCallback Comparison function. If not set, items will be converted and compared as strings
     * @return \self
     */
    public function getSubtract($rSet, $fnCallback=null)
    {
        if(is_callable($fnCallback))
        {
            return self::createFromArray(array_udiff($this->getData(), $rSet->getData(), $fnCallback));
        }
        $rgSelf = array_map('serialize', $this->getData());
        $rgData = array_map('serialize', $rSet->getData());
        return self::createFromArray(array_map('unserialize', array_diff($rgSelf, $rgData)));
    }
    /**
     * Get union of passed and current sets
     * @param self $rSet
     * @param callable | null $fnCallback Comparison function. If not set, items will be converted and compared as strings
     * @return \self
     */
    public function getUnion($rSet, $fnCallback=null)
    {
        if(is_callable($fnCallback))
        {
            return self::createFromArray(array_merge($this->getData(), $rSet->getSubtract($this, $fnCallback)));
        }
        $rgSelf = array_map('serialize', $this->getData());
        $rgData = array_map('serialize', $rSet->getData());
        return self::createFromArray(array_map('unserialize', array_merge($rgSelf, $rgData)));
    }
    /**
     * Get current set power
     * @return integer
     */
    public function getPower()
    {
        return count($this->getData());
    }
    /**
     * Get product of passed and current sets
     * @param self $rSet
     * @return \self
     */
    public function getProduct(self $rSet)
    {
        $rgResult = [];
        foreach($this->getData() as $mOne)
        {
            foreach($rSet->getData() as $mTwo)
            {
                $rgResult[] = [$mOne, $mTwo];
            }
        }
        return self::createFromArray($rgResult);
    }
}