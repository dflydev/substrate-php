<?php

interface substrate_stones_factory_IStoneClassLoaderAware {
    
    /**
     * Callback that allows a stone to be aware of the stone class loader
     * @todo Implement
     * @param $classLoader
     */
    public function setStoneClassLoader($classLoader);

}