<?php
require_once('substrate_Context.php');
interface substrate_stones_IContextStartupAware {
    /**
     * Stone is aware about the Substrate context starting up
     * @param $context
     */
    public function informAboutContextStartup(substrate_Context $context);
}

?>