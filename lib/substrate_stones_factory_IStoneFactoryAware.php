<?php

require_once('substrate_stones_factory_IStoneFactory.php');

interface substrate_stones_factory_IStoneFactoryAware {

    /**
     * Callback that supplies the owning factory to a stone instance.
     * @param $stoneFactory
     */
    public function setStoneFactory(substrate_stones_factory_IStoneFactory $stoneFactory);

}