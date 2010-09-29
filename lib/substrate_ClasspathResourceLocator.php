<?php

require_once('substrate_PathResourceLocator.php');

class substrate_ClasspathResourceLocator extends substrate_PathResourceLocator {
    
    /**
     * Constructor
     */
    public function __construct($dotPath = null, $prependedPaths = null, $appendedPaths = null) {
        // We don't set a path.
        parent::__construct($dotPath, null, $prependedPaths, $appendedPaths);
    }
    
    /**
     * Paths that make up the classpath
     * @return array
     */
    public function paths() {

        // TODO Should this could be cached somehow?
        return explode(PATH_SEPARATOR , get_include_path());

    }

}

?>