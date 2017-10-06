<?php

/**
 * The home manager controller for siteTransfer.
 *
 */
class siteTransferHomeManagerController extends siteTransferMainController {
	/* @var siteTransfer $siteTransfer */
	public $siteTransfer;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('sitetransfer');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addCss($this->siteTransfer->config['cssUrl'] . 'mgr/main.css');
		//$this->addCss($this->siteTransfer->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addJavascript($this->siteTransfer->config['jsUrl'] . 'mgr/misc/utils.js');
		$this->addJavascript($this->siteTransfer->config['jsUrl'] . 'mgr/widgets/export.panel.js');
		$this->addJavascript($this->siteTransfer->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->siteTransfer->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "sitetransfer-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->siteTransfer->config['templatesPath'] . 'home.tpl';
	}
}