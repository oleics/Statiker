<?php
require_once dirname(dirname(__FILE__)).'/lib/statiker.class.php';
$statiker = new Statiker($modx);
return $statiker->initialize('mgr');