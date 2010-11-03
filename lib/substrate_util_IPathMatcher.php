<?php
interface substrate_util_IPathMatcher {
    
    /**
     * Determine if specified path matches a pattern
     * @param $pattern
     * @param $path
     * @param $isCaseSensitive
     */
    public function match($pattern, $path, $isCaseSensitive = false);

}
?>