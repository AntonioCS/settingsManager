<?php

namespace SettingsManager;


class settingsManager {
    
    /**
     *
     * @var data
     */
    private $_data = array();
    
    /**
     * To speed up access
     * @var type 
     */
    private $_cache = array();
    
    /**
     * To use or not to use the cache (that is the question!!)
     * 
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
     * @param array $settings 
     * @param bool $allowChange
     */
    public function __construct($data, $allowChange = false) {
        $this->_data = $data;      
        $this->_allowChange = $allowChange;        
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
    
    public function set($section, $value) {
        return $this->_engine($section, $value);
    }
    
    /**
     * Check and see if section exists
     * 
     * @param string $section 
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
     * Set a section in the cache
     * 
     * @param string $section
     * @return null 
     */
    public function cacheGet($section) {
        if (isset($this->_cache[$section]))
            return $this->_cache[$section];    
        
        return null;
    }
    
    /**
     * Set the cache value for given section
     * 
     * @param string $section
     * @param mixed $value 
     */
    public function cacheSet($section, $value) {
        $this->_cache[$section] = $value;        
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
     * @throws \OutOfBoundsException 
     */
    private function _engine($section, $value = null) {
        
        $sections = explode('/',$section);
        $tempSectionData = $this->_data; 

        foreach ($sections as $currentSection) {        
            if (!isset($tempSectionData[$currentSection])) {
                throw new \OutOfBoundsException($section . ' - ' . $currentSection);
            }       

            $tempSectionData = &$tempSectionData[$currentSection];
        }

        if ($value) {
            if (!$this->_allowChange)
                throw new TryToChangeImmutableObjectException();
            
            $tempSectionData = $value;            
            if ($this->_useCache)
                unset($this->_cache[$section]);
        }	

        return $tempSectionData;
    }            
}

class TryToChangeImmutableObjectException extends \Exception {}