<?php

require_once('substrate_AbstractClassLoader.php');
require_once('substrate_IResourceLocator.php');

class substrate_ResourceLocatorClassLoader extends substrate_AbstractClassLoader {
    
    /**
     * Constructor
     * @param $resourceLocator
     */
    public function __construct(substrate_IResourceLocator $resourceLocator, $templateCallbacks = null) {
        parent::__construct($templateCallbacks);
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * (non-PHPdoc)
     * @see lib/substrate_AbstractClassLoader::find()
     */
    public function find($classFileName) {
        return $this->resourceLocator->find($classFileName, true);
    }
    
}

?>