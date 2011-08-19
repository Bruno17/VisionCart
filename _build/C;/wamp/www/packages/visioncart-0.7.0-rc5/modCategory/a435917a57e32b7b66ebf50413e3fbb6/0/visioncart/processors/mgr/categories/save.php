<?php

if (!$modx->user->isAuthenticated('mgr')) return $modx->error->failure($modx->lexicon('permission_denied'));

$visionCart = $modx->visioncart;
$categoryConfig = json_decode($_REQUEST['categoryConfig'], true);
$parent = $_REQUEST['parent'];
$parent = explode('|', $parent);
$parent = explode(':', $parent[0]);
$parent = (int) $parent[1];	

$categoryConfig['description'] = $modx->visioncart->cleanExt($categoryConfig['description']);

$configArray = array(
	array('key' => 'chunk', 'defaultValue' => 0),
	array('key' => 'resource', 'defaultValue' => 0)
);

$configOutput = array();
foreach($configArray as $config) {
	$categoryConfig['config'][$config['key']] = $categoryConfig[$config['key']];
}

if (isset($categoryConfig['id']) && !empty($categoryConfig['id']) && $categoryConfig['id'] != 0) {
	$category = $modx->getObject('vcCategory', $categoryConfig['id']);
	
	$config = $category->get('config');
	$categoryConfig['config'] = array_merge($config, $categoryConfig['config']);
	$category->fromArray($categoryConfig);
	$category->save();
} else {
	$category = $modx->newObject('vcCategory', $categoryConfig);
	$category->set('parent', $parent);
	$category->set('shopid', (int) $_REQUEST['shop']);
	$category->save();
}

if ($categoryConfig['emptyCache'] == true) {
	// Remove all links to this category from cache
	$modx->cacheManager->refresh(array(
		'visioncart/makeUrl/category/'.$categoryConfig['id'] => array(
			'.php'
		),
		'visioncart/general' => array(
			'.php'
		)
	));
	
	// Remove all product links that contain this category
	$productLinks = $modx->getCollection('vcProductCategory', array(
		'categoryid' => $categoryConfig['id']
	));
	foreach($productLinks as $productLink) {
		$modx->cacheManager->refresh(array(
			'visioncart/makeUrl/product/'.$productLink->get('productid') => array(
				'.php'
			)
		));
	}
	
	// Clear cache for parents
	$currentCategory = $category;
	while($currentCategory->get('parent') != 0) {
		$newCategory = $modx->getObject('vcCategory', $currentCategory->get('parent'));
		
		if ($newCategory != null) {
			// Remove all links to this category from cache
			$modx->cacheManager->refresh(array(
				'visioncart/makeUrl/category/'.$newCategory->get('id') => array(
					'.php'
				)
			));
			
			// Remove all product links that contain this category
			$productLinks = $modx->getCollection('vcProductCategory', array(
				'categoryid' => $newCategory->get('id')
			));
			foreach($productLinks as $productLink) {
				$modx->cacheManager->refresh(array(
					'visioncart/makeUrl/product/'.$productLink->get('productid') => array(
						'.php'
					)
				));
			}
			
			$currentCategory = $newCategory;
		} else {
			break;
		}
	}
	
	// Clear cache for siblings
	$continue = true;
	$lastCategories = array($category->get('id'));
	while($continue) {
		$siblings = array();
		
		foreach($lastCategories as $id) {
			$siblings[] = $modx->getCollection('vcCategory', array(
				'parent' => $id
			));
		}
		
		$lastCategories = array();
		
		if (sizeof($siblings) == 0) {
			$continue = false;
			break;
		}
		
		foreach($siblings as $sibling) {
			// Remove all links to this category from cache
			$modx->cacheManager->refresh(array(
				'visioncart/makeUrl/category/'.$sibling->get('id') => array(
					'.php'
				)
			));
			
			// Remove all product links that contain this category
			$productLinks = $modx->getCollection('vcProductCategory', array(
				'categoryid' => $sibling->get('id')
			));
			foreach($productLinks as $productLink) {
				$modx->cacheManager->refresh(array(
					'visioncart/makeUrl/product/'.$productLink->get('productid') => array(
						'.php'
					)
				));
			}
			
			$lastCategories[] = $sibling->get('id');
		}
	}
}

// Return values
if ($category->save()) {
	return $modx->error->success('', $category);
} else {
	return $modx->error->failure('');
}