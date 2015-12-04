<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var skypenotify $skypenotify */
$skypenotify = $modx->getService('skypenotify', 'skypenotify', $modx->getOption('skypenotify_core_path', null, $modx->getOption('core_path') . 'components/skypenotify/') . 'model/skypenotify/');
$modx->lexicon->load('skypenotify:default');

// handle request
$corePath = $modx->getOption('skypenotify_core_path', null, $modx->getOption('core_path') . 'components/skypenotify/');
$path = $modx->getOption('processorsPath', $skypenotify->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));