<?php
/**
 * Abstract Configuration class.
 * @package dd_configuration
 */

require_once('dd_configuration_IConfiguration.php');
require_once('dd_configuration_Resolver.php');
require_once('dd_configuration_Util.php');

/**
 * Abstract Configuration class.
 * @package dd_configuration
 */
abstract class dd_configuration_AbstractConfiguration implements dd_configuration_IConfiguration {

    /**
     * Resolver instance
     * @var dd_configuration_Resolver
     */
    private $resolver;

    /**
     * Get value for key
     *
     * Value will contain resolved results.
     *
     * @return mixed
     */
    public function get($key) {
        return dd_configuration_Util::RESOLVE_KEY(
            $this,
            $key
        );
    }

    /**
     * Get the resolver
     * @return dd_configuration_Resolver
     */
    public function resolver() {
        if ( $this->resolver === null ) {
            $this->resolver = new dd_configuration_Resolver($this);
        }
        return $this->resolver;
    }

    /**
     * Import another configuration
     * @param mixed $input dd_configuration_IConfiguration or array
     */
    public function import($configuration) {
        if ( ! is_array($configuration) ) {
            $configuration = array($configuration);
        }
        foreach ( $configuration as $item ) {
            if ( ! dd_configuration_Util::IMPORT($this, $item) ) {
                return false;
            }
        }
        return true;
    }

}

?>
