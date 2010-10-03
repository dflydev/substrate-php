<?php
$context->add('jon', array(
    'className' => 'tests_Person',
    'constructorArgs' => array(
        'name' => 'Jon',
    ),
));

$context->add('jane', array(
    'className' => 'tests_Person',
    'properties' => array(
        'name' => 'Jane',
    ),
));

$context->add('jonAndJaneConstructor', array(
    'className' => 'tests_Pair',
    'constructorArgs' => array(
        'leader' => $context->ref('jon'),
        'follower' => $context->ref('jane'),
    ),
));

$context->add('jonAndJaneProperties', array(
    'className' => 'tests_Pair',
    'properties' => array(
        'leader' => $context->ref('jon'),
        'follower' => $context->ref('jane'),
    ),
));

$context->add('jonAndJaneMixed', array(
    'className' => 'tests_Pair',
    'constructorArgs' => array(
        'leader' => $context->ref('jon'),
    ),
    'properties' => array(
        'follower' => $context->ref('jane'),
    ),
));

$context->add('bobAndBill', array(
    'className' => 'tests_Pair',
    'constructorArgs' => array(
        'follower' => $context->add(array(
            'className' => 'tests_Person',
            'properties' => array('name' => '${person.bill.name}'),
        )),
    ),
    'properties' => array(
        'leader' => $context->add(array(
            'className' => 'tests_Person',
            'properties' => array('name' => '${person.bob.name}'),
        )),
    ),
));

// Application Configuration.
$context->add('configuration', array(
    'className' => 'dd_configuration_PropertiesConfiguration',
    'constructorArgs' => array(
        'locations' => array(
            'testSimpleStoneValues.properties',
        ),
    ),
));

// Placeholder Configurer is used to replace ${property.key.names} with
// the values found inside of a configuration object.
$context->add('placeholderConfigurer', array(
    'className' => 'substrate_DdConfigurationPlaceholderConfigurer',
    'constructorArgs' => array(
        'configuration' => $context->ref('configuration'),
    ),
));

?>
