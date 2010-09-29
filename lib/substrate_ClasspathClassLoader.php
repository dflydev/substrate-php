<?php

require_once('substrate_ResourceLocatorClassLoader.php');
require_once('substrate_ClasspathResourceLocator.php');

class substrate_ClasspathClassLoader extends substrate_ResourceLocatorClassLoader {
    
    /**
     * Constructor
     * @param $templateCallbacks
     */
    public function __construct($templateCallbacks = null) {
        parent::__construct(new substrate_ClasspathResourceLocator(), $templateCallbacks);
    }

}

?>