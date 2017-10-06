<?php
class siteTransferUpdateProcessor extends modProcessor {
    public $languageTopics = array('sitetransfer');

    public function checkPermissions() {
        return $this->modx->hasPermission('file_create') &&
               $this->modx->hasPermission('settings');
    }
    
    public function process() {
        $object = array('success' => false);
        switch ($this->getProperty('step')) {
            case 0:
                $object['log'] = $this->modx->lexicon('sitetransfer_update_started');
                $object['step'] = 1;
                $object['success'] = true;
                break;
            case 1:
                $configs = array(
                    MODX_BASE_PATH . 'config.core.php',
                    MODX_CONNECTORS_PATH . 'config.core.php',
                    MODX_MANAGER_PATH . 'config.core.php',
                );
                $core_path = explode('/', trim(MODX_CORE_PATH, '/'));
                foreach ($configs as $config) {
                    if (!file_exists($config)) {
                        return 'File ' . $config . ' not found';
                    }
                }
                $start_sep = "define('MODX_CORE_PATH',";
                $end_sep = ");";
                foreach ($configs as $config) {
                    $file_content = file_get_contents($config);
                    file_put_contents($config . '.backup', $file_content);
                    $start = explode($start_sep, $file_content);
                    if (count($start) != 2) {
                        return 'Could not edit core path in ' . $config;
                    }
                    $end = explode($end_sep, $start[1]);
                    if (count($start) < 2) {
                        return 'Could not edit core path in ' . $config;
                    }
                    $path = explode('/', trim(dirname($config), '/'));
                    $dirnames = count($path) + 1;
                    $tail = '';
                    foreach ($core_path as $k => $dir) {
                        if (isset($path[$k]) && $path[$k] == $dir) {
                            $dirnames--;
                        } else {
                            $tail .= $dir . '/';
                        }
                    }
                    $out_path = '__FILE__';
                    for ($i = 0; $i < $dirnames; $i++) {
                        $out_path = 'dirname(' . $out_path . ')';
                    }
                    $out_path = " {$out_path} . '/{$tail}'";
                    $end[0] = $out_path;
                    $start[1] = implode($end_sep, $end);
                    $file_content = implode($start_sep, $start);
                    file_put_contents($config, $file_content);
                }
                
                /* core/config/config.inc.php */
                $config_file = MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
                $file_content = file_get_contents($config_file);
                $pattern = "/database_server = '(.*)';/";
                $replacement = "database_server = 'localhost';";
                $file_content = preg_replace($pattern, $replacement, $file_content);
                $pattern = "/database_user = '(.*)';/";
                $replacement = "database_user = 'DATABASE_USER';";
                $file_content = preg_replace($pattern, $replacement, $file_content);
                $pattern = "/database_password = '(.*)';/";
                $replacement = "database_password = 'DATABASE_PASSWORD';";
                $file_content = preg_replace($pattern, $replacement, $file_content);
                $pattern = "/dbase = '(.*)';/";
                $replacement = "dbase = 'DATABASE_NAME';";
                $file_content = preg_replace($pattern, $replacement, $file_content);
                $pattern = "/database_dsn = '.*;/";
                $replacement = 'database_dsn = \'mysql:host=\'.$database_server.\';dbname=\'.$dbase.\';charset=\'.$database_connection_charset;';
                $file_content = preg_replace($pattern, $replacement, $file_content);
                $file_content = preg_replace($pattern, $replacement, $file_content);
                
                $pattern = "'" . MODX_CORE_PATH . "'";
                $replacement = 'dirname(dirname(__FILE__)) . \'/\'';
                $file_content = str_replace($pattern, $replacement, $file_content);
                $pattern = "'" . MODX_CORE_PATH;
                $replacement = 'dirname(dirname(__FILE__)) . \'/';
                $file_content = str_replace($pattern, $replacement, $file_content);
                
                $base_path = explode('/', trim(MODX_BASE_PATH, '/'));
                $path = explode('/', trim(dirname($config_file), '/'));
                $dirnames = count($path) + 1;
                $tail = '';
                foreach ($base_path as $k => $dir) {
                    if (isset($path[$k]) && $path[$k] == $dir) {
                        $dirnames--;
                    } else {
                        $tail .= $dir . '/';
                    }
                }
                $out_path = '__FILE__';
                for ($i = 0; $i < $dirnames; $i++) {
                    $out_path = 'dirname(' . $out_path . ')';
                }
                $out_path = " {$out_path} . '/{$tail}";
                
                $pattern = "'" . MODX_BASE_PATH;
                $replacement = $out_path;
                $file_content = str_replace($pattern, $replacement, $file_content);
                
                file_put_contents($config_file . '.backup', $file_content);
                /* core/config/config.inc.php */
                
                $object['log'] = $this->modx->lexicon('sitetransfer_configs_edited');
                $object['step'] = 2;
                $object['success'] = true;
                break;
            case 2:
                set_time_limit(0);
                ini_set('max_execution_time', 0);
                $modx_config = $this->modx->getConfig();
                $date = date("Ymd-His");
                $base = $this->modx->getOption('dbname');
                $server = $modx_config['host'];
                $user = $modx_config['username'];
                $password = $modx_config['password'];
                $dump_file = MODX_ASSETS_PATH . "components/sitetransfer/transfer/{$base}_{$date}_mysql.sql";
                system("mysqldump --host=$server --user=$user --password=$password --databases $base --no-create-db --default-character-set=utf8 --result-file={$dump_file}");
                if (file_exists($dump_file) or filesize($dump_file) <= 0) {
                	system(sprintf('mysqldump --no-tablespaces --opt -h%s -u%s -p"%s" %s --result-file=%s', $server, $user, $password, $base, $dump_file));
                }
                $_SESSION['transfer_dump_filename'] = "{$base}_{$date}_mysql.sql";
                $object['log'] = $this->modx->lexicon('sitetransfer_dump_created');
                $object['step'] = 3;
                $object['success'] = true;
                break;
            case 3:
                set_time_limit(0);
                ini_set('max_execution_time', 0);
                $date = date("Ymd-His");
                $base = $this->modx->getOption('dbname');
                $core_path = MODX_CORE_PATH;
                $www_path = MODX_BASE_PATH;
                $transfer_dir =  MODX_ASSETS_PATH . "components/sitetransfer/transfer";
                $file_name = "{$base}_{$date}_files.tar";
                $command = "tar cf {$transfer_dir}/{$file_name}";
                $command .= " --exclude=$transfer_dir";
                $command .= " --exclude={$core_path}cache/*";
                $configs = array(
                    MODX_BASE_PATH . 'config.core.php',
                    MODX_CONNECTORS_PATH . 'config.core.php',
                    MODX_MANAGER_PATH . 'config.core.php',
                );
                foreach ($configs as $config) {
                    $command .= " --exclude=$config.backup";
                }
                $core_config = MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
                $command .= " --exclude=$core_config";
                
                $command .= " $www_path";
                if (strpos(MODX_CORE_PATH, MODX_BASE_PATH) === false) {
                    $command .= "  $core_path";
                }
                system($command);
                foreach ($configs as $config) {
                    if (file_exists($config . '.backup')) {
                        file_put_contents($config, file_get_contents($config . '.backup'));
                        unlink($config . '.backup');
                    }
                }
                unlink("$core_config.backup");
                
                if (isset($_SESSION['transfer_dump_filename']) && $_SESSION['transfer_dump_filename']) {
                    $dump_filename = $_SESSION['transfer_dump_filename'];
                    $dump_in_root = dirname(MODX_BASE_PATH) . "/$dump_filename";
                    copy("{$transfer_dir}/{$dump_filename}", $dump_in_root);
                    unlink("{$transfer_dir}/{$dump_filename}");
                    unset($_SESSION['transfer_dump_filename']);
                    $command = "tar -r -f {$transfer_dir}/{$file_name} {$dump_in_root}";
                    system($command);
                    unlink($dump_in_root);
                    $_SESSION['transfer_archive'] = $file_name;
                }
                $object['log'] = $this->modx->lexicon('sitetransfer_files_archived');
                $object['step'] = 4;
                $object['success'] = true;
                break;
            case 4:
                $object['log'] = $this->modx->lexicon('sitetransfer_sitearchive_created');
                $object['step'] = 5;
                if (isset($_SESSION['transfer_archive']) && $_SESSION['transfer_archive']) {
                    $object['filepath'] = $this->modx->getOption('assets_url') .
                    'components/sitetransfer/transfer/' . $_SESSION['transfer_archive'];
                }
                $object['success'] = true;
                break;
            case 5:
                $object['log'] = $this->modx->lexicon('sitetransfer_finish');
                $object['complete'] = true;
                $object['success'] = true;
                break;
            default:
                break;
        }
        if (!$object['success']) {
            $o = $this->failure('Error', array('complete' => true, 'log' => 'Error'));
        } else {
            $o = $this->success('', $object);
        }
        return $o;
    }

}

return 'siteTransferUpdateProcessor';