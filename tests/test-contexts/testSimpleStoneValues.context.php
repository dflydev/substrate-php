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

?>