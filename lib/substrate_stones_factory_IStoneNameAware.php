<?php

interface substrate_stones_factory_IStoneNameAware {
    
    /**
     * Set the name of the stone in the stone factory that created this stone.
     * @param $name
     */
    public function setStoneName($name = null);

}