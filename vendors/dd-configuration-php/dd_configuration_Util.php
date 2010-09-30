<?php
/**
 * Configuration Utility library.
 * @package dd_configuration
 */

require_once('dd_configuration_IConfiguration.php');

/**
 * Configuration Utility library.
 * @package dd_configuration
 */
class dd_configuration_Util {

    /**
     * Import one configuration into another
     * @param dd_configuration_IConfiguration $importer Importer
     * @param dd_configuration_IConfiguration $importee Importee
     */
    static public function IMPORT(dd_configuration_IConfiguration $importer, dd_configuration_IConfiguration $importee) {
        foreach ( $importee->keys() as $key ) {
            $importer->set($key, $importee->getRaw($key));
        }
        return true;
    }

    /**
     * Resolve a key
     * @param dd_configuration_IConfiguration $configuration Configuration
     * @param string $key Key
     * @return string Resolved key
     */
    static public function RESOLVE_KEY(dd_configuration_IConfiguration $configuration, $key) {
        return $configuration->resolver()->resolveKey($configuration, $key);
    }

    /**
     * Resolve a value
     * @param dd_configuration_IConfiguration $configuration Configuration
     * @param string $value value
     * @return string Resolved key
     */
    static public function RESOLVE_VALUE(dd_configuration_IConfiguration $configuration, $value) {
        return $configuration->resolver()->resolveValue($configuration, $value);
    }

}

?>
