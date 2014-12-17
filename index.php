<?php

$config = dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php';

define('FramemPath',dirname(__FILE__).DIRECTORY_SEPARATOR.'framework');
define('ModulePath', dirname(__FILE__).DIRECTORY_SEPARATOR.'modules');
define('StaticPath', dirname(__FILE__).DIRECTORY_SEPARATOR.'static');

$framework = FramemPath.DIRECTORY_SEPARATOR.'nomorecms.php';
require_once($framework);

define('NM',true);

NM::runApp($config);