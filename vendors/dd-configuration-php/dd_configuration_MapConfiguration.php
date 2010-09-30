<?php
/**
 * Map based Configuration.
 * @package dd_configuration
 */

require_once('dd_configuration_AbstractConfiguration.php');

/**
 * Map based Configuration.
 * @package dd_configuration
 */
class dd_configuration_MapConfiguration extends dd_configuration_AbstractConfiguration {

    protected $map = array();

    /**
     * All keys
     * @return array
     */
    public function keys() {
        return array_keys($this->map);
    }

    /**
     * Get raw value for key
     *
     * Value will be returned raw and will not contain any resolved
     * results.
     *
     * @param string $key Key
     * @return mixed
     */
    public function getRaw($key) {
        return array_key_exists($key, $this->map) ? $this->map[$key] : null;
    }

    /**
     * Set value for a key
     * @input string $key Key
     * @input mixed $value Value
     */
    public function set($key, $value = null) {
        $this->map[$key] = $value;
    }

    /**
     * Does a key exist?
     * @input string $key Key
     * @return bool
     */
    public function exists($key) {
        return array_key_exists($key, $this->map);
    }

}

?>
