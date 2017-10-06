<?php

/**
 * Class siteTransferMainController
 */
abstract class siteTransferMainController extends modExtraManagerController {
	/** @var siteTransfer $siteTransfer */
	public $siteTransfer;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('sitetransfer_core_path', null, $this->modx->getOption('core_path') . 'components/sitetransfer/');
		require_once $corePath . 'model/sitetransfer/sitetransfer.class.php';

		$this->siteTransfer = new siteTransfer($this->modx);
		$this->addCss($this->siteTransfer->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->siteTransfer->config['jsUrl'] . 'mgr/sitetransfer.js');
		$this->addHtml('
		<script type="text/javascript">
			siteTransfer.config = ' . $this->modx->toJSON($this->siteTransfer->config) . ';
			siteTransfer.config.connector_url = "' . $this->siteTransfer->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('sitetransfer:default');
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
class IndexManagerController extends siteTransferMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}