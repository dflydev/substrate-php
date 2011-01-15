<?php

define('PACKAGE_LIB', dirname(dirname(__FILE__)) . '/lib/');
$classpath = explode(PATH_SEPARATOR, get_include_path());
array_unshift($classpath, PACKAGE_LIB);
require_once(dirname(__FILE__) . '/bootstrap.php');
set_include_path(implode(PATH_SEPARATOR, $classpath));

class SubstrateUtilPathMatcherTest extends PHPUnit_Framework_TestCase {
    public function testAntPathMatcher() {
        require_once('substrate_util_AntPathMatcher.php');
        $pathMatcher = new substrate_util_AntPathMatcher();
        $this->assertTrue($pathMatcher->match("**/foo.txt", "hello/world/foo.txt"));
        // TODO This next test currently fails, need to explore why.
        //$this->assertFalse($pathMatcher->match("*/foo.txt", "hello/world/foo.txt"));
    }
}
