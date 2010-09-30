<?php
interface substrate_objects_IFactoryObject {

    /**
     * Get the object.
     * @return object
     */
    public function getObject();
    
    /**
     * Get the object type
     * @return string
     */
    public function getObjectType();
    
}

?>