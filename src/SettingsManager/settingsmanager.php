<?php

namespace SettingsManager;


class settingsManager {
    
    /**
     *
     * @var array
     */
    private $_settings = array();
    
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
     */
    public function __construct($settings, $allowChange = false) {
        $this->_settings = $settings;      
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
        if (isset($this->_cache[$section]))
            return $this->_cache[$section];        
        
        $sections = explode('/',$section);
        $tempSectionData = $this->_settings; 
        
        foreach ($sections as $currentSection) {                        
            if (!isset($tempSectionData[$currentSection])) {
                throw new \OutOfBoundsException($section . ' - ' . $currentSection);
            }            
            $tempSectionData = $tempSectionData[$currentSection];
        }
                
        return $tempSectionData;            
    }
    
    public function set($section, $value) {
        
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
        $tempSectionData = $this->_settings; 

        foreach ($sections as $currentSection) {        
            if (!isset($tempSectionData[$currentSection])) {
                throw new \OutOfBoundsException($section . ' - ' . $currentSection);
            }       

            $tempSectionData = &$tempSectionData[$currentSection];
        }

        if ($value) {
            $tempSectionData = $value;								
        }	

        return $tempSectionData;
    }            
}