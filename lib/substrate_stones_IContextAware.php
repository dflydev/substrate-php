<?php
require_once('substrate_Context.php');
interface substrate_IContextAware {
    /**
     * Stone is aware of the Substrate context
     * @param $context
     */
    public function informAboutContext(substrate_Context $context);
}
?>