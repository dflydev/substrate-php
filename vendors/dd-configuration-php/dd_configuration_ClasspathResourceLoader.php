<?php
/**
 * Classpath Resource Loader class.
 * @package dd_configuration
 */

require_once('dd_configuration_PathResourceLoader.php');

/**
 * Classpath Resource Loader class.
 * @package dd_configuration
 */
class dd_configuration_ClasspathResourceLoader extends dd_configuration_PathResourceLoader {

    /**
     * Constructor
     * @param string $dotPath Dot path
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
