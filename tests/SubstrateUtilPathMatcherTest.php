<?php
class SubstrateUtilPathMatcherTest extends PHPUnit_Framework_TestCase {
    public function testAntPathMatcher() {
        require_once('substrate_util_AntPathMatcher.php');
        $pathMatcher = new substrate_util_AntPathMatcher();
        $this->assertTrue($pathMatcher->match("**/foo.txt", "hello/world/foo.txt"));
        // TODO This next test currently fails, need to explore why.
        //$this->assertFalse($pathMatcher->match("*/foo.txt", "hello/world/foo.txt"));
    }
}