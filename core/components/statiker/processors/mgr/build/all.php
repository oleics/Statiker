<?php

require_once($modx->statiker->config['corePath'].'lib/statikerbuilder.class.php');
$builder = new StatikerBuilder($modx);

$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,1);
$context_key = $modx->getOption('context_key',$scriptProperties,null);

/* build query */
$c = $modx->newQuery('modResource');
$where = array(
    // 'cacheable'=>1,
    // 'published'=>1
);
if($context_key) {
    $where['context_key'] = $context_key;
}
if($where) {
    $c->where($where);
}
unset($where);
$count = $modx->getCount('modResource', $c);
$c->limit($limit,$start);
$resources = $modx->getIterator('modResource', $c);
 
/* iterate */
$list = array();
foreach($resources as $resource) {
    $list[] = $builder->build($resource);
    #$list[] = $resource->toArray();
}
return $this->outputArray($list,$count);
