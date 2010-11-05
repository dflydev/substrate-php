<?php

require_once('substrate_stones_StonesException.php');

/**
 * Thrown when a stone doesn't match the expected type.
 */
class substrate_stones_factory_StoneNotOfRequiredTypeException extends substrate_stones_StonesException {
    
    /**
     * Name of stone
     * @var string
     */
    private $stoneName;
    
    /**
     * String representing the required type of the found stone
     * @var string
     */
    private $requiredType;

    /**
     * String representing the actual type of the found stone
     * @var string
     */
    private $actualType;

    /**
     * Constructor
     * @param $stoneName
     * @param $requiredType
     * @param $actualType
     */
    public function __construct($stoneName, $requiredType, $actualType) {
        parent::__construct('Stone named "' . $stoneName . '" was not of the required type. (required ' . $requiredType . ' but got ' . $actualType);
        $this->stoneName = $stoneName;
        $this->requiredType = $requiredType;
        $this->actualType = $actualType;
    }
    
    /**
     * Name of stone
     * @return string
     */
    public function stoneName() { return $this->stoneName; }
    
    /**
     * String representing the required type of the found stone
     * @return string
     */
    public function requiredType() { return $this->requiredType; }

    /**
     * String representing the actual type of the found stone
     * @return string
     */
    public function actualType() { return $this->actualType; }

}