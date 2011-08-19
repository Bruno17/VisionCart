<?php
/**
 * @package visioncart
 */
 
$vc =& $modx->visioncart;

$scriptProperties['config'] = $modx->getOption('config', $scriptProperties, 'default');
$config = $vc->getConfigFile($vc->shop->get('id'), 'getProduct', null, array('config' => $scriptProperties['config']));

$scriptProperties = array_merge($config, $scriptProperties);
$scriptProperties['category'] = $modx->getOption('category', $scriptProperties, 0);
$scriptProperties['scheme'] = $modx->getOption('scheme', $scriptProperties, -1);

// Load the product
if (isset($scriptProperties['id'])) {
    $product = $vc->getProduct($scriptProperties['id']);
} else {
	if (isset($vc->product)) {
		$product = $vc->product;
	} else {
    	$product = $vc->getProduct($vc->router['product']['id']);
	}
}

if (!isset($product) || is_null($product) || $product == false) {
	return false;
}

if ($scriptProperties['category'] != 0) {
	$link = $product->getOne('ProductCategory', array(
		'categoryid' => $scriptProperties['category']
	));
	
	// Add the product URL for reference
	$product->set('url', $vc->makeUrl(array(
		'productCategory' => $link->get('id'),
		'scheme' => $scriptProperties['scheme']
	)));
}

// Convert prices and so on for display purposes, leaving the database values intact for re-usage
$product->set('display', array(
	'price' => $vc->calculateProductPrice($product, true),
	'shippingprice' => $vc->money($product->get('shippingprice'), array('shopId' => $product->get('shopid')))
));



if (isset($scriptProperties['id'])) {
    $modx->toPlaceholders($product->toArray(), 'vc.product');
}

if ($scriptProperties['tpl'] != '') { 
	return $vc->parseChunk($scriptProperties['tpl'], array_merge($product->toArray(), array(
		'link' => $link != null ? $link->toArray() : array()
	)));
}

return '';