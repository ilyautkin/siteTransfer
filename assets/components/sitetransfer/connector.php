<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var siteTransfer $siteTransfer */
$siteTransfer = $modx->getService('sitetransfer', 'siteTransfer', $modx->getOption('sitetransfer_core_path', null, $modx->getOption('core_path') . 'components/sitetransfer/') . 'model/sitetransfer/');
$modx->lexicon->load('sitetransfer:default');

// handle request
$corePath = $modx->getOption('sitetransfer_core_path', null, $modx->getOption('core_path') . 'components/sitetransfer/');
$path = $modx->getOption('processorsPath', $siteTransfer->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));