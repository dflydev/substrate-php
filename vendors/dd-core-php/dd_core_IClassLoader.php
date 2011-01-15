<?php

interface dd_core_IClassLoader {

    /**
     * Load a class
     * @param $className
     * @param $includeFilename
     */
    public function load($className, $includeFilename = null);
    
}