<?php
/**
 * Configuration interface.
 * @package dd_configuration
 */

/**
 * Configuration interface.
 * @package dd_configuration
 */
interface dd_configuration_IConfiguration {

    /**
     * All keys
     * @return array
     */
    public function keys();

    /**
     * Get value for key
     *
     * Value will contain resolved results.
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Get raw value for key
     *
     * Value will be returned raw and will not contain any resolved
     * results.
     *
     * @param string $key Key
     * @return mixed
     */
    public function getRaw($key);

    /**
     * Set value for a key
     * @input string $key Key
     * @input mixed $value Value
     */
    public function set($key, $value = null);

    /**
     * Does a key exist?
     * @input string $key Key
     * @return bool
     */
    public function exists($key);

    /**
     * Get the resolver
     * @return dd_configuration_Resolver
     */
    public function resolver();

    /**
     * Import another configuration
     * @param mixed $input dd_configuration_IConfiguration or array
     */
    public function import($configuration);

}

?>
