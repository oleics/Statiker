<?php
$tvs = array();

$tvs[1]= $modx->newObject('modTemplateVar');
$tvs[1]->fromArray(array(
    'id' => 1,
    'name' => 'statiker.build',
    'caption' => 'Build',
    'description' => 'Shall we create a static file for this Resource?',
    'type' => 'option',
    'elements' => 'Yes==1||No==0',
    'default_text' => '0',
    'display' => 'default',
    'locked' => 0,
    'rank' => 1,
    'display_params' => '',
),'',true,true);

$tvs[2]= $modx->newObject('modTemplateVar');
$tvs[2]->fromArray(array(
    'id' => 2,
    'name' => 'statiker.compress',
    'caption' => 'Compress',
    'description' => 'Compress (minify) the static file.',
    'type' => 'option',
    'elements' => 'Yes==1||No==0',
    'default_text' => '0',
    'display' => 'default',
    'locked' => 0,
    'rank' => 2,
    'display_params' => '',
),'',true,true);

$tvs[3]= $modx->newObject('modTemplateVar');
$tvs[3]->fromArray(array(
    'id' => 3,
    'name' => 'statiker.gzencode',
    'caption' => 'GZip Encode',
    'description' => 'Create a gzip-encoded version right beside the static file.',
    'type' => 'option',
    'elements' => 'Yes==1||No==0',
    'default_text' => '0',
    'display' => 'default',
    'locked' => 0,
    'rank' => 3,
    'display_params' => '',
),'',true,true);

$tvs[4]= $modx->newObject('modTemplateVar');
$tvs[4]->fromArray(array(
    'id' => 4,
    'name' => 'statiker.overwrite',
    'caption' => 'Overwrite',
    'description' => '',
    'type' => 'option',
    'elements' => 'Yes==1||No==0',
    'default_text' => '0',
    'display' => 'default',
    'locked' => 0,
    'rank' => 4,
    'display_params' => '',
),'',true,true);

/*
$tvs[5]= $modx->newObject('modTemplateVar');
$tvs[5]->fromArray(array(
    'id' => 5,
    'name' => 'statiker.rewriteonupdate',
    'caption' => 'Rewrite on change',
    'description' => '',
    'type' => 'option',
    'elements' => 'Yes==1||No==0',
    'default_text' => '1',
    'display' => 'default',
    'locked' => 0,
    'rank' => 5,
    'display_params' => '',
),'',true,true);
*/

return $tvs;