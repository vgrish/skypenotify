<?php

/**
 * Class skypenotifyMainController
 */
abstract class skypenotifyMainController extends modExtraManagerController {
	/** @var skypenotify $skypenotify */
	public $skypenotify;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('skypenotify_core_path', null, $this->modx->getOption('core_path') . 'components/skypenotify/');
		require_once $corePath . 'model/skypenotify/skypenotify.class.php';

		$this->skypenotify = new skypenotify($this->modx);
		$this->addCss($this->skypenotify->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->skypenotify->config['jsUrl'] . 'mgr/skypenotify.js');
		$this->addHtml('
		<script type="text/javascript">
			skypenotify.config = ' . $this->modx->toJSON($this->skypenotify->config) . ';
			skypenotify.config.connector_url = "' . $this->skypenotify->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('skypenotify:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends skypenotifyMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}