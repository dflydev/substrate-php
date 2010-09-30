<?php
/**
 * Path Resource Loader class.
 * @package dd_configuration
 */

require_once('dd_configuration_IResourceLoader.php');

/**
 * Path Resource Loader class.
 * @package dd_configuration
 */
class dd_configuration_PathResourceLoader implements dd_configuration_IResourceLoader {

    /**
     * Calling file
     *
     * This path is used as a starting point for '.' path so that resource
     * files that live in the same directory as a library will be loaded
     * relatively.
     *
     * @var string
     */
    private $callingFile;

    /**
     * Paths
     * @var array
     */
    private $paths = array();

    /**
     * Appended paths
     * @var array
     */
    private $appendedPaths = array();

    /**
     * Prepended paths
     * @var array
     */
    private $prependedPaths = array();

    /**
     * Constructor
     * @param string $callingFile Dot path
     */
    public function __construct($callingFile = null, $paths = null, $prependedPaths = null, $appendedPaths = null) {
        if ( $callingFile !== null ) {
            $this->callingFile = $callingFile;
        }
        if ( $paths !== null ) {
            if ( ! is_array($paths) ) {
                $paths = array($paths);
            }
            foreach ( $paths as $path ) {
                $this->paths[] = $path;
            }
        }
        if ( $prependedPaths !== null ) {
            if ( ! is_array($prependedPaths) ) {
                $prependedPaths = array($prependedPaths);
            }
            foreach ( $prependedPaths as $path ) {
                $this->prependedPaths[] = $path;
            }
        }
        if ( $appendedPaths !== null ) {
            if ( ! is_array($appendedPaths) ) {
                $appendedPaths = array($appendedPaths);
            }
            foreach ( $appendedPaths as $path ) {
                $this->appendedPaths[] = $path;
            }
        }
    }

    /**
     * Find the path for a location
     * @param string $location Location
     * @return string
     */
    public function find($location) {

        foreach ( $this->allPaths() as $path ) {
            $testLocation = $path . '/' . $location;
            // TODO This could possibly be cached eventually.
            if ( file_exists($testLocation) ) return $testLocation;
        }

        // Could not be found.
        return null;

    }

    /**
     * Paths to search
     * @return array
     */
    public function allPaths() {

        $callingFiles = array();
        if ( $this->callingFile ) $callingFiles[] = dirname($this->callingFile);

        return array_merge(
            $callingFiles,
            $this->prependedPaths(),
            $this->paths(),
            $this->appendedPaths()
        );

    }

    /**
     * Paths
     * @return array
     */
    public function paths() {
        return $this->paths;
    }

    /**
     * Prepend a path to the classpath
     * @param string $path Path
     */
    public function prependPath($path) {
        array_unshift($this->prependedPaths, $path);
    }

    /**
     * Append a path to the classpath
     * @param string $path Path
     */
    public function appendPath($path) {
        push($this->appendedPaths, $path);
    }

    /**
     * Prepended paths
     * @return array
     */
    public function prependedPaths() {
        return $this->prependedPaths;
    }

    /**
     * Appended paths
     * @return array
     */
    public function appendedPaths() {
        return $this->appendedPaths;
    }


}

?>
