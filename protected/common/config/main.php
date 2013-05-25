<?php

$common = dirname(dirname(__FILE__));
$application = dirname($common);
// 包含所有代码的根目录
$site = dirname($application);
// 后台
$backend = $application.'/backend';
// 前台
$frontend = $application.'/frontend';
// 命令行
$console = $application.'/console';

Yii::setPathOfAlias('site', $site);
Yii::setPathOfAlias('application', $application);
Yii::setPathOfAlias('common',$common);
Yii::setPathOfAlias('backend',$backend);
Yii::setPathOfAlias('frontend',$frontend);
Yii::setPathOfAlias('console',$console);

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => $application,
	'name'=>'静态电影',
	'language' => 'zh_cn',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'common.models.*',
		'common.components.*',
		'common.modules.*'
	),

	'modules'=>array(),

	// application components
	'components'=>array(
		'clientScript' => array(
			'class' => 'common.extensions.minify.EClientScript',
			'combineScriptFiles' => false,
			'combineCssFiles' => false,
			'optimizeCssFiles' => false,
			'optimizeScriptFiles' => false,
		),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				/*
				'movie'=>'site/index',
				'article'=>'site/post',
				'movie/<id:\d+>'=>'site/viewmovie',
				'article/<id:\d+>'=>'site/view',*/
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=s_movie',
			'emulatePrepare' => false,
			'username' => 'root',
			'password' => 'qeephp',
			'charset' => 'utf8',
			'tablePrefix' => 's_'
		),
		'cache' => array(
			'class' => 'system.caching.CFileCache',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		// 网站首页设置
		'websiteIndexTitle' => '静态电影首页',
		'websiteIndexDescription' => '',
		'websiteIndexKeywords' => '',
		// 教程
		'articleTitle' => '教程',
		'articleDescription' => '',
		'articleKeywords' => '',
		// 摄影师
		'photoTitle' => '摄影师',
		'photoDescription' => '',
		'photoKeywords' => '',
		// 模特
		'modelTitle' => '模特',
		'modelDescription' => '',
		'modelKeywords' => '',
	),
);