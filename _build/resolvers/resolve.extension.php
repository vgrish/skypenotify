<?php

if (!$modx = $object->xpdo AND !$modx instanceof modX) {
	return true;
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
		$modx->addExtensionPackage('skypenotify', '[[++core_path]]components/skypenotify/model/');
		break;
	case xPDOTransport::ACTION_UNINSTALL:
		$modx->removeExtensionPackage('skypenotify');
		break;
}
return true;