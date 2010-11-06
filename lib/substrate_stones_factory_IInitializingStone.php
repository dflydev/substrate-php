<?php

interface substrate_stones_factory_IInitializingStone {

    /**
     * Invoked by a StoneFactory after it has set all stone properties supplied (and satisfied StoneFactoryAware and ApplicationContextAware).
     */
    public function afterPropertiesSet();
    
}