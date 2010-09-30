<?php
/**
 * Configuration Resolver.
 * @package dd_configuration
 */

require_once('dd_configuration_IConfiguration.php');

/**
 * Configuration Resolver.
 * @package dd_configuration
 */
class dd_configuration_Resolver {

    /**
     * Resolved cache
     * @var array
     */
    protected $resolved = array();

    /**
     * Resolve a key
     * @param dd_configuration_IConfiguration $configuration Configuration
     * @param string $key Key
     * @return string
     */
    public function resolveKey(dd_configuration_IConfiguration $configuration, $key) {
        if ( ! array_key_exists($key, $this->resolved) ) {
            $this->resolved[$key] = $this->resolveValue($configuration, $configuration->getRaw($key));
        }
        return $this->resolved[$key];
    }

    /**
     * Resolve a value
     * @param dd_configuration_IConfiguration $configuration Configuration
     * @param string $key Key
     * @return string
     */
    public function resolveValue(dd_configuration_IConfiguration $configuration, $value) {

        $counter = 0;
        $resolverCallback = new dd_configuration_ResolverCallback(
            $configuration
        );
        while ( true ) {

            $newValue = preg_replace_callback(
                '/\${([a-zA-Z0-9\.\(\)_\:]+?)}/',
                array($resolverCallback, 'resolveCallback'),
                $value
            );

            if ( $newValue === $value ) {
                break;
            }

            $value = $newValue;

            // Break recursion if depth goes beyond 10!
            // TODO Make this configurable?
            if ( $counter++ > 10 ) break;

        }

        return $value;

    }

}

/**
 * Configuration Resolver Callback.
 * @package dd_configuration
 */
class dd_configuration_ResolverCallback {

    /**
     * Configuration
     * @var dd_configuration_IConfiguration
     */
    protected $configuration;

    /**
     * Constructor
     * @param dd_configuration_IConfiguration $configuration Configuration
     */
    public function __construct(dd_configuration_IConfiguration $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * preg_replace_callback Callback.
     * @param array $matches Matches
     */
    public function resolveCallback($matches) {
        $key = $matches[1];
        if ( preg_match('/^(ENV|SERVER|CONSTANT):(\w+)$/', $key, $special) ) {
            list($whole, $which, $key) = $special;
            if ( $which == 'ENV' ) {
                if ( array_key_exists($key, $_ENV) ) {
                    return $_ENV[$key];
                }
            } elseif ( $which == 'SERVER' ) {
                if ( array_key_exists($key, $_SERVER) ) {
                    return $_SERVER[$key];
                }
            } elseif ( $which == 'CONSTANT' ) {
                if ( defined($key) ) {
                    return constant($key);
                }
            }
        }
        if ( $this->configuration->exists($key) ) {
            return $this->configuration->get($key);
        }
        return $matches[0];
    }

}

?>
