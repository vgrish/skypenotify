<?php

if (!$modx = $object->xpdo && !$object->xpdo instanceof modX) {
	return true;
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
		$modelPath = $modx->getOption('skypenotify_core_path', null, $modx->getOption('core_path') . 'components/skypenotify/') . 'model/';
		$modx->addPackage('skypenotify', $modelPath);

		$manager = $modx->getManager();
		$objects = array(
			//'skypenotifyItem',
		);
		foreach ($objects as $tmp) {
			$manager->createObjectContainer($tmp);
		}
		break;

	case xPDOTransport::ACTION_UNINSTALL:
		break;
}

return true;
