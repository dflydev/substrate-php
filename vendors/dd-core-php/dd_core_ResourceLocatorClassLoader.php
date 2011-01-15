<?php

require_once('dd_core_AbstractClassLoader.php');
require_once('dd_core_IResourceLocator.php');

class dd_core_ResourceLocatorClassLoader extends dd_core_AbstractClassLoader {
    
    /**
     * Constructor
     * @param $resourceLocator
     */
    public function __construct(dd_core_IResourceLocator $resourceLocator, $templateCallbacks = null) {
        parent::__construct($templateCallbacks);
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * (non-PHPdoc)
     * @see dd_core_AbstractClassLoader::find()
     */
    public function find($classFileName) {
        return $this->resourceLocator->find($classFileName, true);
    }
    
}