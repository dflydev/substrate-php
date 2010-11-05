<?php

require_once('substrate_stones_StonesException.php');
require_once('substrate_stones_factory_NoSuchStoneDefinitionException.php');
require_once('substrate_stones_factory_StoneNotOfRequiredTypeException.php');

interface substrate_stones_factory_IStoneFactory {

    /**
     * Does the stone factory contain a stone with the given name?
     * @return bool
     */
    public function containsStone($stoneName);
    
    /**
     * Return the alises for the given stone name, if any.
     * @param $stoneName
     * @return array
     */
    public function getAliases($stoneName);
    
    /**
     * Return an instance of the specified stone.
     * @param $stoneName
     * @return object
     * @throws substrate_stones_factory_NoSuchStoneDefinitionException - if there is no stone definition with the specified name
     * @throws substrate_stones_StonesException - if the stone could not be obtained
     */
    public function get($stoneName);
    
    /**
     * Return the stone instance that uniquely matches the given object type, if any.
     * @param $type
     */
    public function getByType($type);
    
    /**
     * Return an instance of the specified stone.
     * 
     * Similar to get($stoneName), but provides a measure of saftey by throwing a
     * StoneNotOfRequiredTypeException if the stone is not of the required type.
     * 
     * @param $name
     * @param $type
     * @return object
     * @throws substrate_stones_factory_StoneNotOfRequiredTypeException - if the stone is not of the required type
     * @throws substrate_stones_factory_NoSuchStoneDefinitionException - if there is no stone definition with the specified name
     * @throws substrate_stones_StonesException - if the stone could not be obtained
     */
    public function getByNameAndType($name, $type);
    
    /**
     * Determine the type of the stone with the given name.
     * @param $name
     * @return string
     * @throws substrate_stones_factory_NoSuchStoneDefinitionException - if there is no stone definition with the specified name
     */
    public function getType($name);

}