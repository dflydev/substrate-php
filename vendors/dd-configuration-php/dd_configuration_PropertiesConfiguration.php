<?php
/**
 * Properties file based Configuration.
 * @package dd_configuration
 */

require_once('dd_configuration_MapConfiguration.php');
require_once('dd_configuration_ClasspathResourceLoader.php');

/**
 * Properties file based Configuration.
 * @package dd_configuration
 */
class dd_configuration_PropertiesConfiguration extends dd_configuration_MapConfiguration {

    /**
     * Properties file locations
     * @var array
     */
    protected $locations = array();

    /**
     * Resource loader
     * @var dd_configuration_IResourceLoader
     */
    protected $resourceLoader;

    /**
     * Constructor
     * @param mixed $locations Locations
     */
    public function __construct($locations = null, $resourceLoader = null) {

        if ( $resourceLoader === null ) {
            $trace = debug_backtrace();
            $this->resourceLoader = new dd_configuration_ClasspathResourceLoader(
                isset($trace[0]['file']) ? $trace[0]['file'] : null
            );
        }
        else $this->resourceLoader = $resourceLoader;

        if ( $locations !== null ) $this->addLocations($locations);

    }

    /**
     * Add properties file locations
     *
     * May accept a string specifying location or an array containing
     * multiple location strings. This is the most flexible way to
     * add locations.
     *
     * @param mixed $locations Locations
     */
    public function addLocations($locations = null) {

        if ( $locations !== null ) {
            if ( ! is_array($locations) ) $locations = array($locations);
            foreach ( $locations as $location ) {
                $this->addLocation($location);
            }
        }

    }

    /**
     * Add a properties file location
     *
     * Requires passed value to be a location string.
     *
     * @param string $location Location
     */
    public function addLocation($location) {

        if ( is_array($location) ) throw new Exception('Cannot specify location as an array');

        $path = $this->resourceLoader->find($location);

        if ( $path !== null ) {

            $this->locations[] = $path;

            $content = file_get_contents($path);

            foreach ( explode("\n", $content) as $line ) {

                preg_match('/^\s*([^=]+?)\s*=\s*(.+?)\s*$/', $line, $matches);

                if ( count($matches) > 1 && strlen($matches[1]) > 0 && strpos($matches[1], '#') !== 0) {
                    $this->map[$matches[1]] = $matches[2];
                }

            }

        }

    }

}

?>
