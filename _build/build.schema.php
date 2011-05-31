<?php
/**
 * Build Schema script
 *
 * @package statiker
 * @subpackage build
 */

// 
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

require_once dirname(__FILE__) . '/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'core' => $root.'core/components/statiker/',
    'model' => $root.'core/components/statiker/model/',
    'assets' => $root.'assets/components/statiker/',
    'schema' => $root.'core/components/statiker/model/schema/',
);

// 
$manager= $modx->getManager();
$generator= $manager->getGenerator();

// 
$generator->parseSchema($sources['schema'].'statiker.mysql.schema.xml', $sources['model']);

// 
$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);
echo "\nExecution time: {$totalTime}\n";
exit();