<?php

class substrate_ContextStoneReference {

    /**
     * Stone's name
     * @var string
     */
    private $name;
    
    /**
     * Constructor
     */
    public function __construct($name) {
        $this->name = $name;
    }
    
    /**
     * Stone's name
     * @return string
     */
    public function name() {
        return $this->name;
    }
    
    /**
     * Set the stone's name
     * @param $name
     */
    public function setName($name) {
        $this->name = $name;
    }
    
}

?>