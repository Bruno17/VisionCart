<?php
/**
 * @package visioncart
 */

$vc =& $modx->visioncart;

$processor = $modx->runProcessor('user/update', $scriptProperties, array(
	'location' => 'web',
	'processors_path' => $vc->config['processorsPath'],
	'scriptProperties' => &$scriptProperties
));

return $processor->getResponse();