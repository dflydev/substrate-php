<?php
require_once('substrate_context_IApplicationContext.php');
interface substrate_context_IApplicationContextAware {
    /**
     * Set the ApplicationContext that this object runs in.
     * @param $applicationContext
     */
    public function setApplicationContext(substrate_context_IApplicationContext $applicationContext);
}