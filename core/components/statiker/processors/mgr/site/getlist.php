<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'name');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('statikerSite');
if(!empty($query)) {
    $c->where(array(
        'name:LIKE' => '%'.$query.'%',
        'OR:context_key:LIKE' => '%'.$query.'%',
    ));
}
$count = $modx->getCount('statikerSite',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$sites = $modx->getIterator('statikerSite', $c);
 
/* iterate */
$list = array();
foreach($sites as $site) {
    $list[]= $site->toArray();
}
return $this->outputArray($list,$count);
