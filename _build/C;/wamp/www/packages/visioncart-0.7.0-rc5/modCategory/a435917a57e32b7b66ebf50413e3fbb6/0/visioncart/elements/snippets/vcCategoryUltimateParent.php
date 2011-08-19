<?php
if ($modx->visioncart->category != null) {
	$default = $modx->visioncart->category->get('id');
} else {
	$default = 0;
}

$id = $modx->getOption('id', $scriptProperties, $default);
$return = $modx->getOption('return', $scriptProperties, 'id');

$maxLoops = 50;
$count = 0;

if ($id != 0) {
	$parentFound = false;
	$parent = null;
	
	while(!$parentFound) {
		if ($count == $maxLoops) {
			if ($category != null) {
			}
			return 'infinite';
		}
		
		$category = $modx->getObject('vcCategory', $id);
		if ($category == null) {
			return '';	
		}
		
		if ($category->get('parent') == 0) {
			return $category->get($return);	
			break;	
		} else {
			$id = $category->get('parent');	
		}
		
		$count++;
	}
	
	if ($parentFound) {
		return $category->get($return);	
	}
}