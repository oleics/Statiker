<?php
/**
* Creates the tables on install
*
* @package statiker
* @subpackage build
*/
if($object->xpdo) {
    switch($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('statiker.core_path',null,$modx->getOption('core_path').'components/statiker/').'model/';
            $modx->addPackage('statiker',$modelPath);

            $manager = $modx->getManager();
            $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
            $manager->createObjectContainer('statikerSite');
            $manager->createObjectContainer('statikerFile');
            $modx->setLogLevel(modX::LOG_LEVEL_INFO);
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('statiker.core_path',null,$modx->getOption('core_path').'components/statiker/').'model/';
            $modx->addPackage('statiker',$modelPath);
            $manager = $modx->getManager();
            $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
            $manager->removeObjectContainer('statikerSite');
            $manager->removeObjectContainer('statikerFile');
            $modx->setLogLevel(modX::LOG_LEVEL_INFO);
            break;
    }
}
return true;
