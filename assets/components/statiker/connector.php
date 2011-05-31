<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('statiker.core_path',null,$modx->getOption('core_path').'components/statiker/');

require_once $corePath.'lib/statiker.class.php';
$modx->statiker = new Statiker($modx);
$modx->lexicon->load('statiker:default');
$path = $modx->getOption('processorsPath', $modx->statiker->config, $corePath.'processors/');

// $path = $corePath.'processors/';
$modx->request->handleRequest(array(
    'processors_path'   => $path,
    'location'          => ''
));
