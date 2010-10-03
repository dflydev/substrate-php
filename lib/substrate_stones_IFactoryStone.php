<?php
interface substrate_stones_IFactoryStone {

    /**
     * Get the object.
     * @return object
     */
    public function getObject();
    
    /**
     * Get the object type
     * @return class
     */
    public function getObjectType();
    
}

?>