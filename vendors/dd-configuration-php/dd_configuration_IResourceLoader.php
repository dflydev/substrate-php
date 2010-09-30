<?php
/**
 * Resource Loader interface.
 * @package dd_configuration
 */

/**
 * Resource Loader interface.
 * @package dd_configuration
 */
interface dd_configuration_IResourceLoader {

    /**
     * Find the path for a location
     * @param string $location Location
     * @return string
     */
    public function find($location);

}

?>
