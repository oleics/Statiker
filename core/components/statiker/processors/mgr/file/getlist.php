<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'resource');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('statikerFile');
if(!empty($query)) {
    $c->where(array(
        'resource:LIKE' => '%'.$query.'%',
        'OR:context_key:LIKE' => '%'.$query.'%',
        'OR:static_path:LIKE' => '%'.$query.'%',
    ));
}
$count = $modx->getCount('statikerFile',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$files = $modx->getIterator('statikerFile', $c);
 
/* iterate */
$list = array();
foreach($files as $site) {
    $list[]= $site->toArray();
}
return $this->outputArray($list,$count);
