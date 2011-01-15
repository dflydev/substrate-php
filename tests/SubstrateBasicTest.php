<?php

define('PACKAGE_LIB', dirname(dirname(__FILE__)) . '/lib/');
$classpath = explode(PATH_SEPARATOR, get_include_path());
array_unshift($classpath, PACKAGE_LIB);
$classpath[] = dirname(__FILE__) . '/test-libs';
$classpath[] = dirname(__FILE__) . '/test-contexts';
$classpath[] = dirname(__FILE__) . '/test-configs';
require_once(dirname(__FILE__) . '/bootstrap.php');
set_include_path(implode(PATH_SEPARATOR, $classpath));

require_once('PHPUnit/Framework.php');
require_once('substrate_Context.php');

class SubstrateBasicTest extends PHPUnit_Framework_TestCase {

    /**
     * Simple test to see if a context can be created, period.
     */
    public function testStartup() {
        $context = new substrate_Context(array());
    }

    /**
     * Test simple stone values
     */
    public function testSimpleStoneValues() {
        
        $context = new substrate_Context('testSimpleStoneValues.context.php');
        
        $context->execute();
        
        $personJon = $context->get('jon');
        $personJane = $context->get('jane');

        $this->assertEquals('Jon', $personJon->name());
        $this->assertEquals('Jane', $personJane->name());
        
        $jonAndJaneConstructor = $context->get('jonAndJaneConstructor');
        
        $this->assertTrue($jonAndJaneConstructor->leader() == $personJon);
        $this->assertTrue($jonAndJaneConstructor->follower() == $personJane);

        $jonAndJaneProperties = $context->get('jonAndJaneProperties');
        
        $this->assertTrue($jonAndJaneProperties->leader() == $personJon);
        $this->assertTrue($jonAndJaneProperties->follower() == $personJane);

        $jonAndJaneMixed = $context->get('jonAndJaneMixed');
        
        $this->assertTrue($jonAndJaneMixed->leader() == $personJon);
        $this->assertTrue($jonAndJaneMixed->follower() == $personJane);
        
        $people = $context->findStonesByImplementation('tests_Person');
        $this->assertEquals(2, count($people));
        
        $bobAndBill = $context->get('bobAndBill');
        $this->assertEquals('Bob', $bobAndBill->leader()->name());
        $this->assertEquals('Bill', $bobAndBill->follower()->name());
        
        $people = $context->findStonesByImplementation('tests_Person');
        $this->assertEquals(4, count($people));
        
    }

}
?>
