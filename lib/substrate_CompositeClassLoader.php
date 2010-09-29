<?php

require_once('substrate_IClassLoader.php');

class substrate_CompositeClassLoader implements substrate_IClassLoader {
    
    /**
     * Class loaders
     * @var array
     */
    protected $classLoaders;
    
    /**
     * Constructor
     * @param $templateCallbacks
     */
    public function __construct($classLoaders) {
        if ( ! is_array($classLoaders) ) {
            $classLoaders = array($classLoaders);
        }
        $this->classLoaders = $classLoaders;
    }
    
    /**
     * Load a class
     * @param $className
     * @param $includeFilename
     */
    public function load($className, $includeFilename = null) {
        if ( class_exists($className) ) return true;
        if ( isset($this->loadAttemptedLocally[$className]) ) return false;
        $this->loadAttemptedLocally[$className] = true;
        if ( $includeFilename !== null ) {
            // If an include filename was specified, we'll
            // just assume that is what we are looking for.
            require_once($includeFilename);
            return true;
        }
        foreach ( $this->classLoaders as $classLoader ) {
            if ( $classLoader->load($className) ) return true;
        }
        return false;
    }

}

?>