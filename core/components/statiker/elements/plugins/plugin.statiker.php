<?php/* * Events that do count here: *   OnDocFromSave *   OnResourceDuplicate *   OnDocUnPublished *   OnDocPublished *   OnResourceDelete (dont forget about $scriptProperties['children']) *   OnResourceUndelete (dont forget about $scriptProperties['children']) */if(!empty($scriptProperties['resource']) && $scriptProperties['resource'] instanceof modResource) {    //     $corePath = $modx->getOption('statiker.core_path',null,$modx->getOption('core_path').'components/statiker/');    require_once($corePath.'lib/statiker.class.php');    $modx->statiker = new Statiker($modx);    require_once($corePath.'lib/statikerbuilder.class.php');    unset($corePath);    $builder = new StatikerBuilder($modx);    //     $builder->build($scriptProperties['resource']);    // parent    $pid = $scriptProperties['resource']->get('parent');    if($pid) {        $parent = $modx->getObject('modResource', $pid);        if($parent) {            $builder->build($parent);        }    }    unset($pid, $parent);    // children    if(!empty($scriptProperties['children']) && is_array($scriptProperties['children'])) {        foreach($scriptProperties['children'] as $cid) {            $c = $modx->getObject('modResource', $cid);            if($c) {                $builder->build($c);            }        }        unset($cid, $c);    }    //     unset($builder);}