<?php
/**
 * @package visioncart
 */

if ($modx->context->get('key') == 'mgr') {
	switch((string) $modx->event->name) {
		case 'OnBeforeCacheUpdate':
			$modx->cacheManager->refresh(array(
				'visioncart' => array(
					'.php'
				)
			));
			break;
	}
	return false;
}

switch((string) $modx->event->name) {
	case 'OnWebPageInit':
		$corePath = $modx->getOption('visioncart.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH));
		$modx->addPackage('visioncart', $corePath.'components/visioncart/model/');
		$modx->visioncart = $modx->getService('visioncart', 'VisionCart', $corePath.'components/visioncart/model/visioncart/', array(
			'method' => (string) (isset($_REQUEST['method']) && $_REQUEST['method'] != '') ? strtolower($_REQUEST['method']) : 'resource',
			'initialize' => 'plugin',
			'context' => (string) $modx->context->get('key'),
			'event' => (string) $modx->event->name
		));
		break;
        
    case 'OnPageNotFound':
        $corePath = $modx->getOption('visioncart.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH));
        $modx->addPackage('visioncart', $corePath.'components/visioncart/model/');
        $modx->visioncart = $modx->getService('visioncart', 'VisionCart', $corePath.'components/visioncart/model/visioncart/', array(
            'method' => (string) (isset($_REQUEST['method']) && $_REQUEST['method'] != '') ? strtolower($_REQUEST['method']) : 'resource',
            'initialize' => 'plugin',
            'context' => (string) $modx->context->get('key'),
            'event' => (string) $modx->event->name
        ));

		$modx->visioncart->route(array(
			'method' => (string) (isset($_REQUEST['method']) && $_REQUEST['method'] != '') ? strtolower($_REQUEST['method']) : 'resource',
			'initialize' => 'plugin',
			'context' => (string) $modx->context->get('key'),
			'event' => (string) $modx->event->name
		));
		break;

	case 'OnLoadWebDocument':
		$modx->visioncart->assign(array(
			'method' => (string) (isset($_REQUEST['method']) && $_REQUEST['method'] != '') ? strtolower($_REQUEST['method']) : 'resource',
			'initialize' => 'plugin',
			'context' => (string) $modx->context->get('key'),
			'event' => (string) $modx->event->name
		));
		break;
	default:
		//exit($modx->event->name);
		break;
}