<?php

require_once('substrate_PathResourceLocator.php');

class substrate_SimplePathResourceLocator extends substrate_PathResourceLocator {
    
    /**
     * Constructor
     */
    public function __construct($paths) {
        // We don't set a path.
        parent::__construct(null, $paths);
    }

}

?>