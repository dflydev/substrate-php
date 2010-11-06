<?php

/**
 * Central interface to provide configuration for an application.
 * 
 * This is read-only while the application is running, but may be reloaded
 * if the implementation supports this.
 * @todo Extend: ApplicationEventPublisher, StoneFactory, HierarchicalStoneFactory, ListableStoneFactory, MessageSource, ResourceLoader, ResourcePatternResolver
 */
interface substrate_context_IApplicationContext extends substrate_stones_factory_IStoneFactory {
    
    /**
     * Return a friendly name for this context.
     * @return string
     */
    public function getDisplayName();
    
    /**
     * Return the unique id of this application context.
     * @return int
     */
    public function getId();
    
    /**
     * Return the parent context, or null if there is no parent and this is the root of the context hierarchy.
     * @return substrate_context_IApplicationContext
     */
    public function getParent();
    
    /**
     * Return the timestamp when this context was first loaded
     * @return long
     */
    public function getStartupDate();

}