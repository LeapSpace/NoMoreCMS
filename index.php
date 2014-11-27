<?php

$framework = dirname(__FILE__).DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'nomorecms.php';
$config = dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php';

define('ModulePath', dirname(__FILE__).DIRECTORY_SEPARATOR.'modules');
require_once($framework);

NM::runApp($config);