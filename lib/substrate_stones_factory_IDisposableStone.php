<?php

/**
 * Interface to be implemented by stones that want to release resources on destruction.
 * 
 * A StoneFactory is supposed to invoke the destroy method if it disposes a cached singleton.
 * An application context is supposed to dispose all of its singletons on close.
 */
interface substrate_stones_factory_IDisposableStone {

    /**
     * Invoked by a StoneFactory on destruction of a singleton.
     */
    public function destroy();
    
}