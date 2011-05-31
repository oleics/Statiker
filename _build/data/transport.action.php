<?php

$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => PKG_NAME_LOWER,
    'parent' => 0,
    'controller' => 'index',
    'haslayout' => true,
    'lang_topics' => PKG_NAME_LOWER.':default,lexicon',
    'assets' => '',
),'',true,true);

/* load action into menu */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => PKG_NAME_LOWER,
    'parent' => 'components',
    'description' => PKG_NAME_LOWER.'.desc',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);

return $menu;