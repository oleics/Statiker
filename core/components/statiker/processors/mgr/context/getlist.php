<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('modContext');
if(!empty($query)) {
    $c->where(array(
        'key:LIKE' => '%'.$query.'%'
    ));
}
$count = $modx->getCount('modContext',$c);
//$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$sites = $modx->getIterator('modContext', $c);
 
/* iterate */
$list = array();
foreach($sites as $site) {
    $list[]= $site->toArray();
}
return $this->outputArray($list,$count);
