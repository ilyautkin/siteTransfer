<?php
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        $modx->controller->addLexiconTopic('sitetransfer:default');
        $modx->controller->addCss($modx->getOption('assets_url').'components/sitetransfer/css/mgr/main.css');
        $modx->controller->addJavascript($modx->getOption('assets_url').'components/sitetransfer/js/mgr/widgets/update.button.js');
        $response = $modx->runProcessor('mgr/version/check', array(), array('processors_path' => $modx->getOption('core_path') . 'components/sitetransfer/processors/'));
        $resObj = $response->getObject();
        $_html = "<script>	var simpleUpdateConfig = " . $modx->toJSON($resObj) . ";</script>";
        $modx->controller->addHtml($_html);
        break;
}