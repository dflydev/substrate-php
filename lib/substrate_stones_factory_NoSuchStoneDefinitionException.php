<?php

require_once('substrate_stones_StonesException.php');

/**
 * Exception thrown when a StoneFactory is asked for a stone instance name for which it cannot find a definition
 */
class substrate_stones_factory_NoSuchStoneDefinitionException extends substrate_stones_StonesException {
    
    /**
     * Name of the missing stone
     * @var string
     */
    private $stoneName;
    
    /**
     * String representing the type of the missing stone
     * @var string
     */
    private $type;

    /**
     * Constructor
     * @param $stoneName
     * @param $type
     */
    public function __construct($stoneName = null, $type = null) {
        $message = 'Stone was not defined.';
        if ( $stoneName and $type ) {
            $message = 'Stone named "' . $stoneNamem . '" of type "' . $type . '" was not defined.';
        } elseif ( $stoneName ) {
            $message = 'Stone named "' . $stoneNamem . '" was not defined.';
        } else {
            $message = 'Unique stone of type "' . $type . '" was not defined.';
        }
        parent::__construct($message);
        $this->stoneName = $stoneName;
        $this->type = $type;
    }
    
    /**
     * Name of the missing stone
     * @return string
     */
    public function stoneName() { return $this->stoneName; }
    
    /**
     * String representing the type of the missing stone
     * @return string
     */
    public function type() { return $this->type; }

}