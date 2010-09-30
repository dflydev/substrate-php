<?php

require_once('substrate_IPlaceholderConfigurer.php');
require_once('dd_configuration_IConfiguration.php');
require_once('dd_configuration_Util.php');

class substrate_DdConfigurationPlaceholderConfigurer implements substrate_IPlaceholderConfigurer {

    protected $configuration;

    public function __construct(dd_configuration_IConfiguration $configuration) {
        $this->configuration = $configuration;
    }

    public function replacePlaceholders($value) {
        return dd_configuration_Util::RESOLVE_VALUE(
            $this->configuration,
            $value
        );
    }

}

?>