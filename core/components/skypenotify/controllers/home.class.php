<?php

/**
 * The home manager controller for skypenotify.
 *
 */
class skypenotifyHomeManagerController extends skypenotifyMainController {
	/* @var skypenotify $skypenotify */
	public $skypenotify;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('skypenotify');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addCss($this->skypenotify->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->skypenotify->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addJavascript($this->skypenotify->config['jsUrl'] . 'mgr/misc/utils.js');
		$this->addJavascript($this->skypenotify->config['jsUrl'] . 'mgr/widgets/items.grid.js');
		$this->addJavascript($this->skypenotify->config['jsUrl'] . 'mgr/widgets/items.windows.js');
		$this->addJavascript($this->skypenotify->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->skypenotify->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "skypenotify-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->skypenotify->config['templatesPath'] . 'home.tpl';
	}
}