<?php

$mainPath = dirname(dirname(dirname(__FILE__)));
$mainConfig = require($mainPath.'/common/config/main.php');

$frontendConfig = array(
	'defaultController' => 'movie',
	'controllerPath' => $frontend.'/controllers',
	'viewPath' => $frontend.'/views',
	'runtimePath' => $frontend.'/runtime',
	'modulePath' => $frontend.'/modules',
	'import' => array(
		'frontend.models.*',
		'frontend.components.*',
		'frontend.modules.*'
	),
);

return CMap::mergeArray($mainConfig, $frontendConfig);