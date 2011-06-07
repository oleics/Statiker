<?php

$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);

/* define package names */
define('PKG_NAME','Statiker');
define('PKG_NAME_LOWER','statiker');
define('PKG_VERSION','1.0.0');
define('PKG_RELEASE','beta1');
define('WORKSPACE_ID', 2);
 
/* define build paths */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'chunks' => $root.'core/components/'.PKG_NAME_LOWER.'/chunks/',
    'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'elements' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/',
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
);
unset($root);

/* define paths to copy */
$pathsToCopy = array(
    array(
        'source' => $sources['source_core'],
        'target' => "return MODX_CORE_PATH . 'components/';",
    ),
    array(
        'source' => $sources['source_assets'],
        'target' => "return MODX_ASSETS_PATH . 'components/';",
    ),
);

/* load modx */
/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . 'build.config.php';
require_once $sources['build'] . 'includes/functions.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

/* load and create builder */
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
if(WORKSPACE_ID) {
    $builder->setWorkspace(WORKSPACE_ID);
}
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');

/* add category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category',PKG_NAME);

/* category: add tvs */
$tvs = include $sources['data'].'transport.tvs.php';
if($tvs && is_array($tvs)) {
    $category->addMany($tvs, 'TemplateVars');
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($tvs).' tvs.');
} else {
    $modx->log(modX::LOG_LEVEL_WARN,'No template vars returned.');
}
unset($tvs);

/* category: add plugins */
$vehicles = include $sources['data'].'vehicle.plugins.php';
if(is_array($vehicles)&&!empty($vehicles)) {
    $plugins = array();
    foreach($vehicles as $vehicle) {
        $builder->putVehicle($vehicle);
        $plugins[] = $vehicle->obj;
    }
    $category->addMany($plugins, 'Plugins');
    unset($plugins);
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($vehicles).' plugin-vehicles.');
} else {
    $modx->log(modX::LOG_LEVEL_WARN,'No plugin-vehicles returned.');
}
unset($vehicles);

/* category: create vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'TemplateVars' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Plugins' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
$vehicle = $builder->createVehicle($category,$attr);
unset($category, $attr);

/* category: resolvers */
$modx->log(modX::LOG_LEVEL_INFO,'Adding '.count($pathsToCopy).' file resolvers to category...');
while($pathsToCopy) {
    $vehicle->resolve('file', array_shift($pathsToCopy));
}

/* category: end */
$builder->putVehicle($vehicle);
unset($vehicle);

/* load system settings */
$settings = include $sources['data'].'transport.settings.php';
if($settings) {
    $attributes = array(
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false,
    );
    foreach($settings as $setting) {
        $vehicle = $builder->createVehicle($setting,$attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($settings).' System Settings.'); flush();
}
unset($settings,$attributes);

/* load action/menu */
$menu = include $sources['data'].'transport.action.php';
$vehicle = $builder->createVehicle($menu,array (
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'text',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Action' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
        ),
    ),
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'tables.resolver.php',
));
$builder->putVehicle($vehicle);
unset($vehicle,$menu);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'setup-options' => array(
        'source' => $sources['build'].'setup.options.php',
    ),
));
$modx->log(xPDO::LOG_LEVEL_INFO,'Set Package Attributes.'); flush();

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();

$tend= explode(" ", microtime());
$tend= $tend[1] + $tend[0];
$totalTime= sprintf("%2.4f s",($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");
exit();