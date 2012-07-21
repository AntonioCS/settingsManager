<?php

namespace SettingsManager;

/**
 * To ease access of config data
 *  
 */
class settingsManager extends \ArrayObject {
    
    /**
     * Where that data will be held
     * @var array
     */
    private $_data = array();
    
    /**
     * To speed up access
     * @var array
     */
    private $_cache = array();
    
    /**
     * To use or not to use the cache (that is the question!!)     
     * @var bool
     */
    private $_useCache = true;
    
    /**
     * Allow the settings to be changed 
     * @var bool
     */
    private $_allowChange = false;    
    
    /**
     * Initialize the class and set the settings data
     * 
     * @param array $data
     * @param bool $allowChange - Default false
     * @param bool $useCache - Default true
     */
    public function __construct($data, $allowChange = false, $useCache = true) {
        $this->_data = $data;      
        $this->_allowChange = $allowChange; 
        $this->_useCache = $useCache;
        
        parent::__construct($data);
    }
    
    /**
    * Access the _data property and return specified section
    * 
    * @param string $section - Example: section/config
    * @return mixed
    * 
    * @throws OutOfBoundsException 
    */
    public function fetch($section) {
        if ($this->_useCache && isset($this->_cache[$section])) {
            return $this->_cache[$section];
        }
        
        return $this->_parse($section);
    }
    
    /**
     * If allowed this will set the given section to the given value
     * 
     * @param string $section
     * @param mixed $value
     * @return self 
     * 
     * @throws OutOfBoundsException
     * @throws TryToChangeImmutableObjectException
     */
    public function setValue($section, $value) {
        $this->_parse($section, $value);
        return $this;
    }
    
    /**
     * Check and see if section path exists
     * 
     * @param string $section 
     * @return bool
     */
    public function pathExists($section) {       
        try {
            $this->fetch($section);
        }
        catch (\OutOfBoundsException $e) {
            return false;
        }
        
        return true;
    }
       
    /**
     * Clear cache 
     */
    public function clearCache() {
        $this->_cache = array();
    }
    
    /**
     * To check if it is possible to change the values
     * 
     * @return bool
     */
    public function isWrittable() {
        return $this->_allowChange;
    }
    
    /**
     * Get/Set a value from the settings property
     * 
     * @param string $section
     * @param mixed $value
     * @return mixed
     * 
     * @throws \OutOfBoundsException
     * @throws TryToChangeImmutableObjectException
     */
    private function _parse($section, $value = null) {
        
        if ($value && !$this->_allowChange)
            throw new TryToChangeImmutableObjectException();
        
        $sections = explode('/',$section);
        $tempSectionData = &$this->_data; 

        foreach ($sections as $currentSection) {        
            if (!isset($tempSectionData[$currentSection])) {
                throw new \OutOfBoundsException($section . ' - ' . $currentSection);
            }       

            $tempSectionData = &$tempSectionData[$currentSection];
        }

        if ($value) {
            $tempSectionData = $value;            
            if ($this->_useCache && isset($this->_cache[$section]))
                unset($this->_cache[$section]);
        }	

        return $tempSectionData;
    }            
}

class TryToChangeImmutableObjectException extends \Exception {}