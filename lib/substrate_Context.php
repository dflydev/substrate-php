<?php
/**
 * Context
 * @package substrate
 */

require_once('substrate_IResourceLocator.php');
require_once('substrate_IClassLoader.php');
require_once('substrate_ContextStoneReference.php');
require_once('substrate_stones_IContextAware.php');
require_once('substrate_stones_IContextStartupAware.php');
require_once('substrate_stones_IFactoryStone.php');


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
     * Cache of instantiated stones
     * @var array
     */
    protected $stoneInstances = array();
    
    /**
     * Cache of initialized stone names
     * @var array
     */
    protected $initializedStones = array();
    
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
     * Execute the Substrate context
     */
    public function execute() {
        foreach ( $this->stoneDefinitions as $name => $setup ) {
            $stoneDefinition = $this->prepareStone($name);
            $this->stoneDefinitions[$name] = $stoneDefinition;
            if ( ! $stoneDefinition['lazyLoad'] ) {
                $this->instantiate($name);
            }
        }
    }
    
    /**
     * Check if stone has been defined
     * @param $name
     */
    public function exists($name = null) {
        return array_key_exists($name, $this->stoneDefinitions);
    }
    
    /**
     * Is a stone instantiated?
     * @param $name
     */
    protected function instantiated($name = null) {
        return array_key_exists($name, $this->stoneInstances);
    }
    
    /**
     * Is a stone initialized?
     * Enter description here ...
     * @param unknown_type $name
     */
    protected function initialized($name = null) {
        return array_key_exists($name, $this->initializedStones);
    }
    
    /**
     * Instantiate a stone
     * @param $name
     * @throws Exception
     */
    protected function instantiate($name = null) {

        if ( $name === null ) throw new Exception('Object name must be specified.');
        if ( $this->instantiated($name)) { return $this->stoneInstances[$name]; }
        
        $setup = $this->stoneDefinition($name);
        $className = $setup['className'];
        $this->logDebug('Initializing stone named "' . $name . '" (' . $className . ')');
        $this->loadDependantClasses($setup);

        $reflectionClass = new ReflectionClass($className);

        $constructor = $reflectionClass->getConstructor();
        $constructorArgs = array();

        $originalConstructorArgs = isset($setup['constructorArgs']) ?
            $setup['constructorArgs'] : null;

        if ( $constructor ) {

            foreach ( $constructor->getParameters() as $reflectionParamter ) {

                $constructorArgumentName = $reflectionParamter->getName();
                if ( isset($setup['constructorArgs'][$constructorArgumentName]) ) {
                    $constructorArgs[] = $setup['constructorArgs'][$constructorArgumentName];
                    // We no longer want to remember this constructor argument.
                    unset($originalConstructorArgs[$constructorArgumentName]);
                } else {
                    $throwException = true;
                    $foundArgument = false;
                    $paramClass = $reflectionParamter->getClass();
                    if ( $paramClass ) {
                        $paramClassName = $paramClass->getName();
                        foreach ( $this->stoneInstances as $testStone ) {
                            if ( $testStone instanceof $paramClassName ) {
                                $throwException = false;
                                $foundArgument = true;
                                $constructorArgs[] = $testStone;
                                break;
                            }
                        }
                    }
                    if ( ! $foundArgument and $reflectionParamter->allowsNull() ) {
                        $throwException = false;
                    }
                    if ( $throwException ) {
                        throw new Exception('Could not find constructor argument named "' . $constructorArgumentName . '" for stone named "' . $name . '"');
                    }
                }
            }

            if ( count($originalConstructorArgs) ) {
                $constructorArgs = array_merge($constructorArgs, array_values($originalConstructorArgs));
            }

        }
        
        if ( sizeof($constructorArgs) ) {
            for ( $i = 0; $i < count($constructorArgs); $i++ ) {
                $constructorArgs[$i] =
                    $this->resolvedConstructorArg($constructorArgs[$i]);
            }
            $newInstance = $reflectionClass->newInstanceArgs($constructorArgs);
        } else {
            $newInstance = $reflectionClass->newInstance();
        }

        $references = array();
        foreach ( $setup['properties'] as $key => $value ) {
            $methodName = 'set' . ucfirst($key);
            if ( method_exists($newInstance, $methodName) ) {
                if ( $value instanceof substrate_ContextStoneReference ) {
                    $references[] = array(
                        'methodName' => $methodName,
                        'contextStoneReference' => $value,
                    );
                } else {
                    $newInstance->$methodName($value);
                }
            }
        }

        foreach ( $setup['dependencies'] as $value ) {
            $references[] = array(
                'methodName' => null,
                'contextStoneReference' => $value,
            );
        }

        if ( $newInstance instanceof substrate_stones_IFactoryStone ) {
            $newInstance = $newInstance->getStone();
        }

        $this->stoneInstances[$name] = $newInstance;

        if ( count($references) ) {
            // If there are references, we can try to load them now.
            // We do this AFTER we have stored our stone reference
            // so that we can avoid infinite loops for stones that
            // have a dependency on each other.
            $this->loadReferences($name, $references);
        }

        $this->addInterfacetoMap(get_class($newInstance), $name);

        foreach ( class_implements($newInstance) as $implementedInterface ) {
            $this->addInterfaceToMap($implementedInterface, $name);
        }

        foreach ( class_parents($newInstance) as $parentClass ) {
            $this->addInterfaceToMap($parentClass, $name);
        }
        
        return $newInstance;
            
            
    }
    
    /**
     * Get the object for the specified stone
     * @param  $name
     * @throws Exception
     */
    public function get($name= null) {
        
        if ( $name === null ) throw new Exception('Object name must be specified.');
        if ( $this->initialized($name) ) return $this->stoneInstances[$name];
        
        $this->initializedStones[$name] = true;

        $object = $this->instantiate($name);
        
        if ( $object instanceof substrate_IContextAware ) {
            $object->informAboutContext($this);
        }
        
        if ( $object instanceof substrate_IContextStartupAware ) {
            $object->informAboutContextStartup($this);
        }
        
        return $object;
    }
    
    /**
     * Get the definition for a stone by name
     * @param $name
     */
    public function stoneDefinition($name) {
        if ( ! $this->exists($name) ) {
            throw new Exception('Could not locate stone definition for name "' . $name . '"');
        }
        return $this->stoneDefinitions[$name];
    }
    
    /**
     * Replaced by substrate_Context::stoneDefinition()
     * @param $name
     * @deprecated
     */
    public function getStoneDefinition($name) {
        return $this->deprecated()->stoneDefinition($name);
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
        return '___anonymousStone_context' . $this->id . '_stone' . $this->anonymousStoneNameCounter++;
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
     * Prepare a stone by name
     * 
     * Preparing a stone essentially traverses the parents to ensure that
     * the stone's settings are correct.
     * 
     * @param $name
     * @return array
     */
    protected function prepareStone($name) {
        
        $returnSetup = $thisStoneSetup = $this->stoneDefinitions[$name];
        
        if ( $thisStoneSetup['parent'] ) {
            $returnSetup = $this->prepareStone($thisStoneSetup['parent']);
        }
        
        foreach ( $thisStoneSetup as $param => $value ) {
            if ( $param == 'properties' ) {
                $returnSetup[$param] = array_merge($returnSetup[$param], $value);
            } elseif ( $param == 'dependencies' ) {
                $returnSetup[$param] = array_merge($returnSetup[$param], $value);
            } elseif ( $param == 'constructorArgs' ) {
                if ( $returnSetup['inheritConstructorArgs'] and $thisStoneSetup['parent']) {
                    foreach ( $thisStoneSetup['constructorArgs'] as $constructorArg => $constructorValue ) {
                        $returnSetup[$param][$constructorArg] = $constructorValue;
                    }
                } else {
                    $returnSetup[$param] = $value;
                }
            } elseif ( $param == 'className' and $value === null) {
                $returnSetup[$param] = $returnSetup[$param];
            } else {
                $returnSetup[$param] = $value;
            }
        }
        
        foreach ( array('constructorArgs', 'properties', 'dependencies') as $key ) {
            foreach ( $returnSetup[$key] as $i => $value ) {
                $returnSetup[$key][$i] = $this->replacePlaceholder($value);
            }
        }
                
        return $returnSetup;
        
    }
    
    /**
     * Load dependant classes based on a specified setup
     * @param $setup
     */
    protected function loadDependantClasses($setup) {
        if ( $setup['parent'] ) {
            $this->loadDependantClasses($this->stoneDefinitions[$setup['parent']]);
        }
        if ( array_key_exists('className', $setup) ) {
            $this->loadClass($setup['className'], $setup['includeFilename']);
        }
    }
    
    /**
     * Load a class
     * @param $className
     * @param $includeFilename
     */
    protected function loadClass($className, $includeFilename = null) {
        if ( $className === null ) throw new Exception('Class name must be specified.');
        if ( class_exists($className) ) return;
        $this->classLoader->load($className, $includeFilename);
    }

    /**
     * Add interface to interface cachemap
     * @param $interfaceOrClass
     * @param $name
     */
    protected function addInterfaceToMap($interfaceOrClass, $name) {
        if ( ! isset($this->mapByInterface[$interfaceOrClass]) ) {
            $this->mapByInterface[$interfaceOrClass] = array();
        }
        $this->mapByInterface[$interfaceOrClass][] = $name;
    }
    
    /**
     * Resolve a constructor arg
     * @param $value
     */
    protected function resolvedConstructorArg($value = null) {
        if ( is_object($value) and $value instanceof substrate_ContextStoneReference  ) {
            return $this->get($value->name());
        } elseif ( is_array($value) ) {
            $newArray = array();
            foreach ( $value as $i => $v ) {
                $newArray[$i] = $this->resolvedConstructorArg($v);
            }
            return $newArray;
        }
        return $value;
    }
    
    /**
     * @see substrate_Context::resolvedConstructorArg()
     * @deprecated
     */
    protected function getResolvedConstructorArg($value = null) {
        return $this->deprecated()->resolvedConstructorArg($value);
    }
    
    /**
     * Replace a placeholder
     * 
     * This is called when the placeholder is potentially an object, a reference
     * or a string.
     * 
     * @param $value
     */
    protected function replacePlaceholder($value) {
        if ( is_object($value) and $value instanceof substrate_ContextStoneReference ) {
            $value->setName($this->replacePlaceholderValue($value->name()));
        } else if ( is_array($value) ) {
            foreach ( $value as $i => $v ) {
                $value[$i] = $this->replacePlaceholder($v);
            }
        } else if ( is_string($value) ) {
            $value = $this->replacePlaceholderValue($value);
        }
        return $value;
    }

    /**
     * Replace a placeholder value
     * 
     * This is called when the value is known to be a string.
     * 
     * @param string $value
     */
    protected function replacePlaceholderValue($value) {
        if ( $this->placeholderConfigurer() === null ) {
            // Just in case...
            return $value;
        } else {
            return $this->placeholderConfigurer()->replacePlaceholders($value);
        }
    }
    
    /**
     * The placeholder configurer (if defined)
     * @return mixed
     */
    protected function placeholderConfigurer() {

        if ( $this->exists('placeholderConfigurer') ) {
            return $this->get('placeholderConfigurer');
        }

        return null;

    }
    
    /**
     * @see substrate_Context::placeholderConfigurer()
     * @deprecated
     */
    protected function getPlaceholderConfigurer() {
        return $this->deprecated()->placeholderConfigurer();
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