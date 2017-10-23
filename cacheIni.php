<?php

use phpFastCache\CacheManager;
 
CacheManager::setDefaultConfig(array(
	"path" => 'cache/tree/',
));
$InstanceCache = CacheManager::getInstance('auto');
	
?>