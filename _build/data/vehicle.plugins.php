<?php

$directory = $sources['source_core'].'/elements/plugins/';

$vehicles = array();

$i = 1;
$iterator = new DirectoryIterator($directory);
foreach($iterator as $item) {
    $extension = array_pop(explode('.', $item->getBasename()));
    if($item->isFile()&&$extension=='php') {
    //if($item->isFile()&&$item->getExtension()=='php') {
        $prefix = explode('.', $item->getBasename('.php'), 2);
        $name = array_pop($prefix);
        $prefix = array_shift($prefix);
        if($prefix=='plugin') {
        
            /* create the plugin object */
            $plugin = $modx->newObject('modPlugin');
            $plugin->fromArray(array(
                'id' => $i,
                'name' => $name,
                'description' => '',
                'plugincode' => getSnippetContent($item->getRealPath()),
            ),'',true,true);
            
            /* add plugin events */
            if(file_exists($directory.'events.'.$name.'.php')) {
                $events = include($directory.'events.'.$name.'.php');
                if($events&&is_array($events)) {
                    $plugin->addMany($events);
                    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for Plugin '.$name.'.'); flush();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_WARN,'No events returned for Plugin '.$name.'.'); flush();
                }
                unset($events);
            }
            
            /* add plugin properties */
            if(file_exists($directory.'properties.'.$name.'.php')) {
                $properties = include($directory.'properties.'.$name.'.php');
                if($properties&&is_array($properties)) {
                    $plugin->setProperties($properties);
                    $modx->log(xPDO::LOG_LEVEL_INFO,'Setting '.count($properties).' Plugin Properties for Plugin '.$name.'.'); flush();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_WARN,'No properties returned for Plugin '.$name.'.'); flush();
                }
                unset($properties);
            }
            
            /*  */
            $attributes= array(
                xPDOTransport::UNIQUE_KEY => 'name',
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => true,
                xPDOTransport::RELATED_OBJECTS => true,
                xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                    'PluginEvents' => array(
                        xPDOTransport::PRESERVE_KEYS => true,
                        xPDOTransport::UPDATE_OBJECT => false,
                        xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
                    ),
                ),
            );
            
            /*  */
            $vehicles[$i] = $builder->createVehicle($plugin, $attributes);
            unset($plugin, $attributes);
            
            $i++;
        }
    }
}

return $vehicles;