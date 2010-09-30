<?php
/**
 * Composite Configuration.
 * @package dd_configuration
 */

require_once('dd_configuration_MapConfiguration.php');

/**
 * Composite Configuration.
 *
 * An dd_configuration_IConfiguration implementation that is
 * populated from multiple dd_configuration_IConfiguration
 * objects.
 *
 * @package dd_configuration
 */
class dd_configuration_CompositeConfiguration extends dd_configuration_MapConfiguration {

    /**
     * Constructor.
     * @param Array $configurations Configuration sources.
     */
    public function __construct($configurations = null) {
        $this->import($configurations);
    }

}

?>
