<?php
$modx->visioncart->assign(array(
			'method' => (string) (isset($_REQUEST['method']) && $_REQUEST['method'] != '') ? strtolower($_REQUEST['method']) : 'resource',
			'initialize' => 'plugin',
			'context' => (string) $modx->context->get('key'),
			'event' => (string) $modx->event->name
		));