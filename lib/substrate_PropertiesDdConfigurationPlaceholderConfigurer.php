<?php
require_once('substrate_DdConfigurationPlaceholderConfigurer.php');
require_once('dd_configuration_PropertiesConfiguration.php');
class substrate_PropertiesDdConfigurationPlaceholderConfigurer extends substrate_DdConfigurationPlaceholderConfigurer {
    public function __construct($locations) {
        parent::__construct(new dd_configuration_PropertiesConfiguration($locations));
    }
}
?>
