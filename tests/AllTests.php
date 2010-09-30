<?php

$classpath = explode(PATH_SEPARATOR, get_include_path());
$classpath[] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test-libs';
$classpath[] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test-contexts';
$classpath[] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test-configs';
require_once('bootstrap.php');
set_include_path(implode(PATH_SEPARATOR, $classpath));

require_once('PHPUnit/Framework.php');

class AllTests {
    public static $testClassNames = array(
        'SubstrateBasicTest'
    );
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Substrate');
        foreach ( self::$testClassNames as $testClassName ) {
            require_once($testClassName . '.php');
            $suite->addTestSuite($testClassName);
        }
        return $suite;
    }
}

?>
