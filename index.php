<?php

die('This file is deprecated.');

# The directory containing the php5-markdown wiki code
$appRoot = __DIR__ . '/';
$libDir  = $appRoot . 'lib/';

$config = array(
	'docDir'      => $appRoot . 'pages/',
	'defaultPage' => 'index',
    'appRoot'     => $appRoot,
);

# And off we go...
require_once $libDir . 'controller.php';

?>