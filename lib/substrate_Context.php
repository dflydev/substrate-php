<?php
/**
 * Context
 * @package substrate
 */

require_once('substrate_IResourceLocator.php');
require_once('substrate_IClassLoader.php');
require_once('substrate_ContextStoneReference.php');


/**
 * Context
 * @package substrate
 */
class substrate_Context {
    
    /**
     * Global ID counter
     * @var int
     */
    protected static $ID_COUNTER = 0;
    
    /**
     * Shared classpath resource locator instance
     * @var substrate_IResourceLocator
     */
    private static $CLASSPATH_RESOURCE_LOCATOR_INSTANCE = null;
    
    /**
     * Access to share classpath resource locator instance
     * @return substrate_IResourceLocator
     */
    public static function CLASSPATH_RESOURCE_LOCATOR() {
        if ( self::$CLASSPATH_RESOURCE_LOCATOR_INSTANCE === null ) {
            require_once('substrate_ClasspathResourceLocator.php');
            self::$CLASSPATH_RESOURCE_LOCATOR_INSTANCE = new substrate_ClasspathResourceLocator();
        }
        return self::$CLASSPATH_RESOURCE_LOCATOR_INSTANCE;
    }
    
    /**
     * Shared classpath class loader instance
     * @var substrate_IClassLoader
     */
    private static $CLASSPATH_CLASS_LOADER_INSTANCE = null;
    
    /**
     * Access to share classpath resource locator instance
     * @return substrate_IClassLoader
     */
    public static function CLASSPATH_CLASS_LOADER() {
        if ( self::$CLASSPATH_CLASS_LOADER_INSTANCE === null ) {
            require_once('substrate_ResourceLocatorClassLoader.php');
            self::$CLASSPATH_CLASS_LOADER_INSTANCE = new substrate_ResourceLocatorClassLoader(self::CLASSPATH_RESOURCE_LOCATOR());
        }
        return self::$CLASSPATH_CLASS_LOADER_INSTANCE;
    }
    
    /**
     * Start time for benchmark
     * @var float
     */
    private $benchmarkStart = null;
    
    /**
     * Depth of importing
     * @var int
     */
    protected $importingDepth = 0;
    
    /**
     * Cache of stone definitions by name
     * @var array
     */
    protected $stoneDefinitions = array();
    
    /**
     * Cache of stone definitions by interface
     * @var array
     */
    protected $stoneDefinitionsByInterface = array();
    
    /**
     * Context ID
     * @var int
     */
    protected $id;
    
    /**
     * Resource locator for locating context files
     * @var substrate_IResourceLocator
     */
    protected $contextResourceLocator;
    
    /**
     * Class loader
     * @var substrate_IClassLoader
     */
    protected $classLoader;

    /**
     * Constructor
     */
    public function __construct($contextConfigNames, substrate_IResourceLocator $contextResourceLocator = null, substrate_IClassLoader $classLoader = null) {
        $this->benchmarkStart = microtime(true);
        $this->id = self::$ID_COUNTER++;
        if ( $contextResourceLocator === null ) {
            $this->contextResourceLocator = self::CLASSPATH_RESOURCE_LOCATOR();
        } else {
            $this->contextResourceLocator = $contextResourceLocator;
        }
        if ( $classLoader === null ) {
            $this->classLoader = self::CLASSPATH_CLASS_LOADER();
        } else {
            $this->classLoader = $classLoader;
        }
        $this->import($contextConfigNames);
        $this->classLoader->load('Foo');
        $this->classLoader->load('Bar');
        $this->classLoader->load('FooBarBaz');
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->logDebug('Context lived for ' . ( microtime(true) - $this->benchmarkStart ) . ' seconds');
    }
    
    /**
     * Import context configurations
     * @param mixed $contextConfigNames
     * @param bool $autoExecute
     */
    public function import($contextConfigNames, $autoExecute = false) {
        
        if ( $this->importingDepth == 0 ) {
            $originalStoneNames = $this->registeredStoneNames();
        }
        
        if ( ! is_array($contextConfigNames) ) {
            $contextConfigNames = array($contextConfigNames);
        }
        
        $context = $this;
        
        $this->importingDepth++;
        foreach ( $contextConfigNames as $contextConfigName ) {
            $contextFilePath = $this->contextResourceLocator->find($contextConfigName);
            if ( file_exists($contextFilePath) ) {
                $this->logInfo('Importing object definitions for "' . $contextConfigName . '" from "' . $contextFilePath . '"');
                include($contextFilePath);
            } else {
                $this->logError('Could not find context configuration for "' . $contextConfigName . '"');
            }
        }
        $this->importingDepth--;
        
        if ( $this->importingDepth == 0 ) {
            $currentStoneNames = $this->registeredStoneNames();
            $this->recentlyDefinedStones = array_diff($currentStoneNames, $originalStoneNames);
            if ( count($this->recentlyDefinedStones) ) {
                $this->logInfo('Added stone definitions for: ' . implode(', ', $this->recentlyDefinedStones));
            } else {
                $this->logInfo('No stones found in context configuration(s): ' . implode(', ', $contextConfigNames));;
            }
        }
        
        if ( $autoExecute ) {
            $this->execute();
        }
        
    }

    /**
     * @see substrate_IContext::registeredStoneNames()
     * @override
     */
    public function registeredStoneNames() {
        return array_keys($this->stoneDefinitions);
    }
    
    /**
     * Replaced by substrate_Context::registeredStoneNames()
     * @deprecated
     */
    public function getRegisteredStoneNames() {
        return $this->deprecated()->registeredStoneNames();
    }
    
    /**
     * Replaced by substrate_Context::registeredStoneNames()
     * @deprecated
     */
    public function getRegisteredObjectNames() {
        return $this->deprecated()->registeredStoneNames();
    }
    
    /**
     * Reference a stone
     * @param $name
     */
    public function ref($name = null) {
        if ( $name === null ) throw new Exception('Stone name must be specified.');
        return new substrate_ContextStoneReference($name);
    }

    /**
     * Generate an anonymous stone name
     * @return string
     */
    protected final function generateAnonymousStoneName() {
        if ( ! isset($this->anonymousStoneNameCounter) ) {
            $this->anonymousStoneNameCounter = 0;
        }
        return '___anonymouseStone_context' . $this->id . '_stone' . $this->anonymousStoneNameCounter++;
    }
    
    /**
     * Generate an anonymous stone name
     * @return string
     * @deprecated
     */
    protected final function generateAnonymousObjectName() {
        return $this->deprecated()->generateAnonymousStoneName();
    }
    
    /**
     * Add a new stone to the context
     * 
     * $context->add('foo', 'my_Foo');
     * $context->add('foo', array('className' => 'my_Foo'));
     * $context->add(array('name' => 'foo', 'className' => 'my_Foo'));
     * 
     * @throws Exception
     */
    public function add() {
        $name = null;
        $setup = null;
        $args = func_get_args();
        if ( count($args) == 2 ) {
            list($name, $setup) = $args;
        } else {
            $name = $this->generateAnonymousStoneName();
            list($setup) = $args;
        }
        if ( $setup === null ) throw new Exception('Setup cannot be null.');
        if ( ! is_array($setup) ) {
            $setup = array ('className' => $setup);
        }
        $setup['name'] = $name;
        if ( ! isset($setup['includeFilename']) ) $setup['includeFilename'] = null;
        if ( ! isset($setup['abstract']) ) $setup['abstract'] = false;
        if ( ! isset($setup['parent']) ) $setup['parent'] = null;
        if ( ! isset($setup['properties']) ) $setup['properties'] = array();
        if ( ! isset($setup['constructorArgs']) ) $setup['constructorArgs'] = array();
        if ( ! isset($setup['dependencies']) ) $setup['dependencies'] = array();
        if ( ! isset($setup['inheritConstructorArgs']) ) $setup['inheritConstructorArgs'] = true;
        if ( ! isset($setup['lazyLoad']) ) $setup['lazyLoad'] = true;
        if ( ! isset($setup['className']) ) $setup['className'] = null;
        $this->stoneDefinitions[$name] = $setup;
        print_r($setup);
        return new substrate_ContextStoneReference($name);
    }

    /**
     * Setup a stone in the context
     * @see substrate_Context::add()
     * @deprecated
     */
    public function set() {
        $this->deprecated();
        $args = func_get_args();
        return call_user_func_array(array($this, 'add'), $args);
    }
    
    /**
     * Log
     * @param string $type
     * @param string $message
     */
    private function log($type, $message = null) {
        printf("%s: %s\n", strtoupper($type), $message === null ? '--NULL--' : $message);
    }

    /**
     * Log trace message
     * @param string $message
     */
    protected function logTrace($message = null) {
        $this->log('trace', $message);
    }
    
    /**
     * Log debug message
     * @param string $message
     */
    protected function logDebug($message = null) {
        $this->log('debug', $message);
    }
    
    /**
     * Log info message
     * @param string $message
     */
    protected function logInfo($message = null) {
        $this->log('info', $message);
    }
    
    /**
     * Log warn message
     * @param string $message
     */
    protected function logWarn($message = null) {
        $this->log('warn', $message);
    }
    
    /**
     * Log error message
     * @param string $message
     */
    protected function logError($message = null) {
        $this->log('error', $message);
    }
    
    /**
     * Log fatal message
     * @param string $message
     */
    protected function logFatal($message = null) {
        $this->log('fatal', $message);
    }
    
    /**
     * Used to notify about a deprecated call
     */
    protected function deprecated() {
        $back = debug_backtrace();
        $this->logWarn('Deprecated call to ' . $back[1]['class'] . '::' . $back[1]['function'] . ', ' . $back[1]['file'] . ':' . $back[1]['line']);
        return $this;
    }
    
}

?>