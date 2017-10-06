<?php
class siteTransferRemoveProcessor extends modProcessor {
    public $languageTopics = array('sitetransfer');

    public function checkPermissions() {
        return $this->modx->hasPermission('file_create') &&
               $this->modx->hasPermission('settings');
    }
    
    public function process() {
        $object = array('success' => false);
        $files = scandir(MODX_ASSETS_PATH . 'components/sitetransfer/transfer');
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                unlink(MODX_ASSETS_PATH . 'components/sitetransfer/transfer/' . $file);
            }
        }
        $object['success'] = true;
        if (!$object['success']) {
            $o = $this->failure('Error', array('complete' => true, 'log' => 'Error'));
        } else {
            $o = $this->success('', $object);
        }
        return $o;
    }

}

return 'siteTransferRemoveProcessor';