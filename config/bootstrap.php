<?php

	if (!defined('ECLO')) die("Hacking attempt");

	use ECLO\App;

	$config = require_once __DIR__ . '/config.php';
	require_once __DIR__ . '/../src/controllers/helpers.php';
	require_once __DIR__ . '/../src/controllers/plugins.php';

	$app = new App($config['db']);

	$jatbi = new Jatbi($app); 

	require_once __DIR__ . '/../src/controllers/requests.php';

	require_once __DIR__ . '/../src/controllers/middleware.php';

	$plugins = new plugin(__DIR__ .'/'.$config['app']['plugins'], $app, $jatbi);
	$plugins->loadRequests($requests);

	$app->JWT($config['app']['secret-key'], 'HS256');
	$app->setGlobalFile(__DIR__ . '/../src/controllers/global.php');
	$app->setValueData('setting', $config['app']);
	$app->setValueData('jatbi', $jatbi);
	$jatbi->checkAuthenticated($requests,$config['app']);

	require_once __DIR__ . '/../src/controllers/common.php';
	require_once __DIR__ . '/../src/controllers/components.php';

	$app->setValueData('common', $common);
	$app->setValueData('commonCss', $CommonCss);
	$app->setValueData('commonJs', $CommonJs);
	
	$userPermissions = [];
	if (!empty($requests) && is_array($requests)) {
	    foreach ($requests as $request) {
	        if (!isset($request['item']) || !is_array($request['item'])) {
	            continue;
	        }
	        foreach ($request['item'] as $key_item => $items) {
	            if (!empty($items['main']) && $items['main'] != 'true') {
	                $SelectPermission[$key_item]['permissions'] = $items['permission'] ?? [];
	                $SelectPermission[$key_item]['name'] = $items['menu'] ?? '';
	            }
	        }
	    }
	}
	$app->setValueData('permission', $SelectPermission);

	foreach (glob(__DIR__ . '/../src/routers/*.php') as $routeFile) {
	    require_once $routeFile;
	}
