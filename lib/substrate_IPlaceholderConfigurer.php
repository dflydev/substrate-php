<?php
interface substrate_IPlaceholderConfigurer {
    
    /**
     * Replace a placeholder value
     * @param $value
     * @return string
     */
    public function replacePlaceholders($value);
    
}
?>