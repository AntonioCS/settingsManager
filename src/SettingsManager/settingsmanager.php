<?php

namespace SettingsManager;

/**
 * To ease access of config data
 *  
 */
class settingsManager {
    
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
    }
    
    /**
    * Access the _config property and return specified section
    * 
    * @param string $section - Example: section/config
    * @return mixed
    * 
    * @throws OutOfBoundsException 
    */
    public function get($section) {
        if ($this->_useCache && isset($this->_cache[$section]))
            return $this->_cache[$section];
        
        return $this->_engine($section);    
    }
    
    /**
     * If allowed this will set the given section to the given value
     * 
     * @param string $section
     * @param mixed $value
     * @return mixed 
     * 
     * @throws OutOfBoundsException
     * @throws TryToChangeImmutableObjectException
     */
    public function set($section, $value) {
        return $this->_engine($section, $value);
    }
    
    /**
     * Check and see if section exists
     * 
     * @param string $section 
     * @return bool
     */
    public function exists($section) {       
        try {
            $this->get($section);
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
     * Get/Set a value from the settings property
     * 
     * @param string $section
     * @param mixed $value
     * @return mixed
     * 
     * @throws \OutOfBoundsException
     * @throws TryToChangeImmutableObjectException
     */
    private function _engine($section, $value = null) {
        
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