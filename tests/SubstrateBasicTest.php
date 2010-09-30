<?php
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
        
    }

}
?>
