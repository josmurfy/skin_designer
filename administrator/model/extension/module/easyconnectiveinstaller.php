<?php

class ModelExtensionModuleEasyconnectiveinstaller extends Model
{

    private $error = array();
    private $url = 'http://www.dilegitim.net/api/index.php?route=api/';
    private $branch_version = "1.0.1";
    private $new_version;

    public function getToken()
    {

        if (VERSION < 3) {
            $token = $this->session->data['token'];
            $token_link = 'token=' . $this->session->data['token'];
        } else {
            $token = $this->session->data['user_token'];
            $token_link = 'user_token=' . $this->session->data['user_token'];
        }
        $data['token'] = $token;
        $data['token_link'] = $token_link;

        return $data;


    }

    public function updateTest()
    {
        $this->error = array();

        //$this->easyconnective->log('Starting update test');

        if (!function_exists("exception_error_handler")) {
            function exception_error_handler($errno, $errstr, $errfile, $errline)
            {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        }

        set_error_handler('exception_error_handler');

        // create a tmp folder
        if (!is_dir(DIR_DOWNLOAD . '/tmp')) {
            try {
                mkdir(DIR_DOWNLOAD . '/tmp');
            } catch (ErrorException $ex) {
                $this->error[] = $ex->getMessage();
            }
        }

        // create tmp file
        try {
            $tmp_file = fopen(DIR_DOWNLOAD . '/tmp/test_file.php', 'w+');
        } catch (ErrorException $ex) {
            $this->error[] = $ex->getMessage();
        }

        // open and write over tmp file
        try {
            $output = '<?php' . "\n";
            $output .= '$test = \'12345\';' . "\n";
            $output .= 'echo $test;' . "\n";

            fwrite($tmp_file, $output);
            fclose($tmp_file);
        } catch (ErrorException $ex) {
            $this->error[] = $ex->getMessage();
        }

        // try and read the file

        // remove tmp file
        try {
            unlink(DIR_DOWNLOAD . '/tmp/test_file.php');
        } catch (ErrorException $ex) {
            $this->error[] = $ex->getMessage();
        }

        // delete tmp folder
        try {
            rmdir(DIR_DOWNLOAD . '/tmp');
        } catch (ErrorException $ex) {
            $this->error[] = $ex->getMessage();
        }

        // reset to the OC error handler
        restore_error_handler();

        //  $this->openbay->log('Finished update test');

        if (!$this->error) {
            // $this->openbay->log('Finished update test - no errors');
            return array('error' => 0, 'response' => '', 'percent_complete' => 20, 'status_message' => $this->language->get('text_check_new'));
        } else {
            // $this->openbay->log('Finished update test - errors: ' . print_r($this->error));
            return array('error' => 1, 'response' => $this->error);
        }
    }

    public function updateCheckVersion($beta = 0)
    {
        $current_version = $this->config->get('module_easyconnective_version');



        $post = array('version' => $this->branch_version, 'beta' => $beta);


        $data = $this->call('update/version/', $post);


        if ($this->lasterror == true) {
            // $this->openbay->log('Check version error: ' . $this->lastmsg);

            return array('error' => 1, 'response' => $this->lastmsg . ' (' . VERSION . ')');
        } else {
            if ($data['version'] > $current_version) {
                $this->new_version = $data['version'];
                $this->load->model('setting/setting');
                $saved_data = array('module_easyconnective_new_version' => $this->new_version);

                $this->model_setting_setting->editSetting('module', $saved_data);

                // $this->openbay->log('Check version new available: ' . $data['version']);
                return array('error' => 0, 'response' => $data['version'], 'percent_complete' => 40, 'status_message' => $this->language->get('text_downloading_last'));
            } else {
                //  $this->openbay->log('Check version - already latest');
                return array('error' => 1, 'response' => $this->language->get('text_version_ok') . $current_version);
            }
        }
    }

    public function updateDownload($beta = 0)
    {
        //$this->openbay->log('Downloading');

        $local_file = DIR_DOWNLOAD . '/easyconnective_update.zip';
        $handle = fopen($local_file, "w+");

        if (VERSION < 3) {
            $oc_version=2;
        }else {

            $oc_version=3;
        }


        $post = array('version' => $this->config->get('module_easyconnective_new_version'), 'current_version' =>$this->branch_version );
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $this->url . 'update/download&version='.$oc_version,
            CURLOPT_USERAGENT => 'Entegrasyon update script',
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_POSTFIELDS => http_build_query($post, '', "&"),
            CURLOPT_FILE => $handle
        );

        $curl = curl_init();
        curl_setopt_array($curl, $defaults);
        curl_exec($curl);

        $curl_error = curl_error($curl);
        // $this->openbay->log('Download errors: ' . $curl_error);

        curl_close($curl);

        return array('error' => 0, 'response' => $curl_error, 'percent_complete' => 60, 'status_message' => $this->language->get('text_extracting'));
    }


    public function updateExtract()
    {
        $this->error = array();

        $web_root = preg_replace('/system\/$/', '', DIR_SYSTEM);

        if (!function_exists("exception_error_handler")) {
            function exception_error_handler($errno, $errstr, $errfile, $errline)
            {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        }

        set_error_handler('exception_error_handler');


        try {
            $zip = new ZipArchive();

            if ($zip->open(DIR_DOWNLOAD . 'easyconnective_update.zip')) {
                $zip->extractTo($web_root);
                $zip->close();
            } else {
                // $this->openbay->log('Unable to extract update files');

                $this->error[] = $this->language->get('text_fail_patch');
            }
        } catch (ErrorException $ex) {
            // $this->openbay->log('Unable to extract update files');
            $this->error[] = $ex->getMessage();
        }

        // reset to the OC error handler
        restore_error_handler();

        if (!$this->error) {
            return array('error' => 0, 'response' => '', 'percent_complete' => 80, 'status_message' =>$this->language->get('text_remove_files'));
        } else {
            return array('error' => 1, 'response' => $this->error);
        }
    }

    public function updateRemove($beta = 0)
    {
        $this->error = array();

        $web_root = preg_replace('/system\/$/', '', DIR_SYSTEM);

        if (!function_exists("exception_error_handler")) {
            function exception_error_handler($errno, $errstr, $errfile, $errline)
            {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        }


        $post = array('beta' => $beta);

        $files = $this->call('update/getRemoveList/', $post);

        // $this->openbay->log("Remove Files: " . print_r($files, 1));

        if (!empty($files['asset']) && is_array($files['asset'])) {
            foreach ($files['asset'] as $file) {
                $filename = $web_root . implode('/', $file['locations']['location']) . '/' . $file['name'];

                if (file_exists($filename)) {
                    try {
                        unlink($filename);
                    } catch (ErrorException $ex) {
                        //  $this->openbay->log('Unable to remove file: ' . $filename . ', ' . $ex->getMessage());
                        $this->error[] = $filename;
                    }
                }
            }
        }

        // reset to the OC error handler
        restore_error_handler();

        if (!$this->error) {
            return array('error' => 0, 'response' => '', 'percent_complete' => 90, 'status_message' => $this->language->get('text_installation_complateting'));
        } else {
            $response_error = '<p>' . $this->language->get('error_file_delete') . '</p>';
            $response_error .= '<ul>';

            foreach ($this->error as $error_file) {
                $response_error .= '<li>' . $error_file . '</li>';
            }

            $response_error .= '</ul>';

            return array('error' => 1, 'response' => $response_error, 'percent_complete' => 90, 'status_message' =>'Yükleme işlemi tamamlanıyor');
        }
    }

    public function updateUpdateVersion($beta = 0)
    {


        $post = array('version' => $this->branch_version, 'beta' => $beta);

        $data = $this->call('update/version/', $post);


        if ($this->lasterror == true) {

            return array('error' => 1, 'response' => $this->lastmsg . ' (' . VERSION . ')');
        } else {


            $last_date = $this->config->get('module_easyconnective_last_update');
            if (!$last_date) {
                $query = $this->db->query("select now() as last_date");
                $last_date = $query->row['last_date'];
            }



            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('module', array('module_easyconnective_last_update' => $last_date, 'module_easyconnective_status' => 1, 'module_easyconnective_version' => $data['version']));
            
            return array('error' => 0, 'response' => $data['version'], 'percent_complete' => 100, 'status_message' => $this->language->get('text_updated_ok') . $data['version']);

        }
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getNotifications()
    {
        $data = $this->call('update/getNotifications/');
        return $data;
    }

    public function version()
    {
        $data = $this->call('update/getStableVersion/');

        // print_r($data);

        if ($this->lasterror == true) {
            $data = array(
                'error' => true,
                'msg' => $this->lastmsg . ' (' . VERSION . ')',
            );

            return $data;
        } else {

            if ($data) {
                $this->load->model('setting/setting');
                $this->model_setting_setting->editSettingValue('mir', 'mir_marketplaces', serialize($data['marketplaces']));
            }

            return $data;
        }
    }


    private function call($call, array $post = null, array $options = array(), $content_type = 'json') {


        $data = array(
            'language' => 1,
            'server' => 1,
            'domain' => $_SERVER['HTTP_HOST'],
            'udi'=> $this->config->get('mir_domain_id'),
            'easyconnective_version' => (int)$this->config->get('module_easyconnective_version'),
            'data' => $post,
            'content_type' => $content_type
        );




        $useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";

        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $this->url . $call,
            CURLOPT_USERAGENT => $useragent,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_POSTFIELDS => http_build_query($data, '', "&")
        );




        $curl = curl_init();


        curl_setopt_array($curl, ($options + $defaults));
        $result = curl_exec($curl);

        curl_close($curl);

        if ($content_type == 'json') {

            $encoding =json_decode($result,1);

            /* some json data may have BOM due to php not handling types correctly */
            if ($encoding == 'UTF-8') {
                $result = preg_replace('/[^(\x20-\x7F)]*/', '', $result);
            }

            $result = json_decode($result, 1);


            /*
                        $this->load->model('setting/setting');
                        $this->model_setting_setting->editSettingValue('mir','mir_marketplaces', serialize($result['marketplaces']));
            */

            if($result){
                $this->lasterror = $result['error'];
                $this->lastmsg = $result['msg'];
            }






            if (!empty($result['data'])) {

                return $result['data'];
            } else {
                return false;
            }


        } elseif ($content_type == 'xml') {
            $result = simplexml_load_string($result);
            $this->lasterror = $result->error;
            $this->lastmsg = $result->msg;

            if (!empty($result->data)) {
                return $result->data;
            } else {
                return false;
            }
        }
    }


    public function install() {

        $this->load->model('setting/setting');
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/category');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/category');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/dashboard');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/dashboard');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/genel');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/genel');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/manufacturer');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/manufacturer');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/order');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/order');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/product');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/product');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/product_question');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/product_question');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/setting');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/setting');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'easyconnective/support');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'easyconnective/support');

        $settings = $this->model_setting_setting->getSetting('easyconnective');

        $settings['module_easyconnective_status'] = 1;
        $settings['module_easyconnective_version']=$this->easyconnective_version;
        $settings['module_easyconnective_last_update']=date('Y-m-d H:i:s');
        $this->model_setting_setting->editSetting('module_easyconnective', $settings);
        $this->createDb();
        $this->refresh();


        //$this->load->controller('marketplace/modification/refresh',array('redirect'=>'marketplace/extension'));

    }

    public function refresh() {



        error_reporting(0);
        ini_set('display_errors', 0);


        if (true) {
            // Just before files are deleted, if config settings say maintenance mode is off then turn it on

            $this->load->model('setting/setting');

            //Log
            $log = array();

            // Clear all modification files
            $files = array();

            // Make path into an array
            $path = array(DIR_MODIFICATION . '*');

            // While the path array is still populated keep looping through
            while (count($path) != 0) {
                $next = array_shift($path);

                foreach (glob($next) as $file) {
                    // If directory add to path array
                    if (is_dir($file)) {
                        $path[] = $file . '/*';
                    }

                    // Add the file to the files to be deleted array
                    $files[] = $file;
                }
            }


            // Reverse sort the file array
            rsort($files);

            // Clear all modification files
            foreach ($files as $file) {
                if ($file != DIR_MODIFICATION . 'index.html') {
                    // If file just delete
                    if (is_file($file)) {
                        unlink($file);

                        // If directory use the remove directory function
                    } elseif (is_dir($file)) {
                        rmdir($file);
                    }
                }
            }

            // Begin
            $xml = array();

            // Load the default modification XML
            $xml[] = file_get_contents(DIR_SYSTEM . 'modification.xml');

            // This is purly for developers so they can run mods directly and have them run without upload after each change.
            $files = glob(DIR_SYSTEM . '*.ocmod.xml');

            if ($files) {
                foreach ($files as $file) {
                    $xml[] = file_get_contents($file);
                }
            }



            if(VERSION >= 3){

                $this->load->model('setting/modification');
                // Get the default modification file
                $results = $this->model_setting_modification->getModifications();

            }else {

                $this->load->model('extension/modification');
                // Get the default modification file
                $results = $this->model_extension_modification->getModifications();

            }


            foreach ($results as $result) {
                if ($result['status']) {
                    $xml[] = $result['xml'];
                }
            }

            $modification = array();

            foreach ($xml as $xml) {
                if (empty($xml)){
                    continue;
                }

                $dom = new DOMDocument('1.0', 'UTF-8');
                $dom->preserveWhiteSpace = false;
                $dom->loadXml($xml);

                // Log
                $log[] = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

                // Wipe the past modification store in the backup array
                $recovery = array();

                // Set the a recovery of the modification code in case we need to use it if an abort attribute is used.
                if (isset($modification)) {
                    $recovery = $modification;
                }

                $files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');

                foreach ($files as $file) {
                    $operations = $file->getElementsByTagName('operation');

                    $files = explode('|', $file->getAttribute('path'));

                    foreach ($files as $file) {
                        $path = '';

                        // Get the full path of the files that are going to be used for modification
                        if ((substr($file, 0, 7) == 'catalog')) {
                            $path = DIR_CATALOG . substr($file, 8);
                        }

                        if ((substr($file, 0, 5) == 'admin')) {
                            $path = DIR_APPLICATION . substr($file, 6);
                        }

                        if ((substr($file, 0, 6) == 'system')) {
                            $path = DIR_SYSTEM . substr($file, 7);
                        }

                        if ($path) {
                            $files = glob($path, GLOB_BRACE);

                            if ($files) {
                                foreach ($files as $file) {
                                    // Get the key to be used for the modification cache filename.
                                    if (substr($file, 0, strlen(DIR_CATALOG)) == DIR_CATALOG) {
                                        $key = 'catalog/' . substr($file, strlen(DIR_CATALOG));
                                    }

                                    if (substr($file, 0, strlen(DIR_APPLICATION)) == DIR_APPLICATION) {
                                        $key = 'admin/' . substr($file, strlen(DIR_APPLICATION));
                                    }

                                    if (substr($file, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
                                        $key = 'system/' . substr($file, strlen(DIR_SYSTEM));
                                    }

                                    // If file contents is not already in the modification array we need to load it.
                                    if (!isset($modification[$key])) {
                                        $content = file_get_contents($file);

                                        $modification[$key] = preg_replace('~\r?\n~', "\n", $content);
                                        $original[$key] = preg_replace('~\r?\n~', "\n", $content);

                                        // Log
                                        $log[] = PHP_EOL . 'FILE: ' . $key;
                                    }

                                    foreach ($operations as $operation) {
                                        $error = $operation->getAttribute('error');

                                        // Ignoreif
                                        $ignoreif = $operation->getElementsByTagName('ignoreif')->item(0);

                                        if ($ignoreif) {
                                            if ($ignoreif->getAttribute('regex') != 'true') {
                                                if (strpos($modification[$key], $ignoreif->textContent) !== false) {
                                                    continue;
                                                }
                                            } else {
                                                if (preg_match($ignoreif->textContent, $modification[$key])) {
                                                    continue;
                                                }
                                            }
                                        }

                                        $status = false;

                                        // Search and replace
                                        if ($operation->getElementsByTagName('search')->item(0)->getAttribute('regex') != 'true') {
                                            // Search
                                            $search = $operation->getElementsByTagName('search')->item(0)->textContent;
                                            $trim = $operation->getElementsByTagName('search')->item(0)->getAttribute('trim');
                                            $index = $operation->getElementsByTagName('search')->item(0)->getAttribute('index');

                                            // Trim line if no trim attribute is set or is set to true.
                                            if (!$trim || $trim == 'true') {
                                                $search = trim($search);
                                            }

                                            // Add
                                            $add = $operation->getElementsByTagName('add')->item(0)->textContent;
                                            $trim = $operation->getElementsByTagName('add')->item(0)->getAttribute('trim');
                                            $position = $operation->getElementsByTagName('add')->item(0)->getAttribute('position');
                                            $offset = $operation->getElementsByTagName('add')->item(0)->getAttribute('offset');

                                            if ($offset == '') {
                                                $offset = 0;
                                            }

                                            // Trim line if is set to true.
                                            if ($trim == 'true') {
                                                $add = trim($add);
                                            }

                                            // Log
                                            $log[] = 'CODE: ' . $search;

                                            // Check if using indexes
                                            if ($index !== '') {
                                                $indexes = explode(',', $index);
                                            } else {
                                                $indexes = array();
                                            }

                                            // Get all the matches
                                            $i = 0;

                                            $lines = explode("\n", $modification[$key]);

                                            for ($line_id = 0; $line_id < count($lines); $line_id++) {
                                                $line = $lines[$line_id];

                                                // Status
                                                $match = false;

                                                // Check to see if the line matches the search code.
                                                if (stripos($line, $search) !== false) {
                                                    // If indexes are not used then just set the found status to true.
                                                    if (!$indexes) {
                                                        $match = true;
                                                    } elseif (in_array($i, $indexes)) {
                                                        $match = true;
                                                    }

                                                    $i++;
                                                }

                                                // Now for replacing or adding to the matched elements
                                                if ($match) {
                                                    switch ($position) {
                                                        default:
                                                        case 'replace':
                                                            $new_lines = explode("\n", $add);

                                                            if ($offset < 0) {
                                                                array_splice($lines, $line_id + $offset, abs($offset) + 1, array(str_replace($search, $add, $line)));

                                                                $line_id -= $offset;
                                                            } else {
                                                                array_splice($lines, $line_id, $offset + 1, array(str_replace($search, $add, $line)));
                                                            }
                                                            break;
                                                        case 'before':
                                                            $new_lines = explode("\n", $add);

                                                            array_splice($lines, $line_id - $offset, 0, $new_lines);

                                                            $line_id += count($new_lines);
                                                            break;
                                                        case 'after':
                                                            $new_lines = explode("\n", $add);

                                                            array_splice($lines, ($line_id + 1) + $offset, 0, $new_lines);

                                                            $line_id += count($new_lines);
                                                            break;
                                                    }

                                                    // Log
                                                    $log[] = 'LINE: ' . $line_id;

                                                    $status = true;
                                                }
                                            }

                                            $modification[$key] = implode("\n", $lines);
                                        } else {
                                            $search = trim($operation->getElementsByTagName('search')->item(0)->textContent);
                                            $limit = $operation->getElementsByTagName('search')->item(0)->getAttribute('limit');
                                            $replace = trim($operation->getElementsByTagName('add')->item(0)->textContent);

                                            // Limit
                                            if (!$limit) {
                                                $limit = -1;
                                            }

                                            // Log
                                            $match = array();

                                            preg_match_all($search, $modification[$key], $match, PREG_OFFSET_CAPTURE);

                                            // Remove part of the the result if a limit is set.
                                            if ($limit > 0) {
                                                $match[0] = array_slice($match[0], 0, $limit);
                                            }

                                            if ($match[0]) {
                                                $log[] = 'REGEX: ' . $search;

                                                for ($i = 0; $i < count($match[0]); $i++) {
                                                    $log[] = 'LINE: ' . (substr_count(substr($modification[$key], 0, $match[0][$i][1]), "\n") + 1);
                                                }

                                                $status = true;
                                            }

                                            // Make the modification
                                            $modification[$key] = preg_replace($search, $replace, $modification[$key], $limit);
                                        }

                                        if (!$status) {
                                            // Abort applying this modification completely.
                                            if ($error == 'abort') {
                                                $modification = $recovery;
                                                // Log
                                                $log[] = 'NOT FOUND - ABORTING!';
                                                break 5;
                                            }
                                            // Skip current operation or break
                                            elseif ($error == 'skip') {
                                                // Log
                                                $log[] = 'NOT FOUND - OPERATION SKIPPED!';
                                                continue;
                                            }
                                            // Break current operations
                                            else {
                                                // Log
                                                $log[] = 'NOT FOUND - OPERATIONS ABORTED!';
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Log
                $log[] = '----------------------------------------------------------------';
            }

            // Log


            // Write all modification files
            foreach ($modification as $key => $value) {
                // Only create a file if there are changes
                if ($original[$key] != $value) {
                    $path = '';

                    $directories = explode('/', dirname($key));

                    foreach ($directories as $directory) {
                        $path = $path . '/' . $directory;

                        if (!is_dir(DIR_MODIFICATION . $path)) {
                            @mkdir(DIR_MODIFICATION . $path, 0777);
                        }
                    }

                    $handle = fopen(DIR_MODIFICATION . $key, 'w');

                    fwrite($handle, $value);

                    fclose($handle);
                }
            }

            // Maintance mode back to original settings
            //   $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);

            // Do not return success message if refresh() was called with $data


        }



        if(VERSION >= 3){

            $this->refreshTheme();
        }


    }



    public function refreshTheme()
    {
        error_reporting(0);


        $directories = glob(DIR_CACHE . '*', GLOB_ONLYDIR);

        if ($directories) {
            foreach ($directories as $directory) {
                $files = glob($directory . '/*');

                foreach ($files as $file) {

                    if (is_file($file) || is_dir($file)) {
                        unlink($file);
                    }
                }

                if (is_dir($directory)) {
                    rmdir($directory);
                }
            }
        }


    }


    private function createDb()
    {

        $this->runExecute(" CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_category` (
`id` int(11) NOT NULL,
`category_id` int(11) NOT NULL,
`etsy` text CHARACTER SET utf8 NOT NULL,
`amz` text CHARACTER SET utf8 NOT NULL,
`ebay` text CHARACTER SET utf8 NOT NULL,
`ali` text CHARACTER SET utf8 NOT NULL,
`wish` text CHARACTER SET utf8 NOT NULL,
`date_added` datetime NOT NULL,
`date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf16;");

//MANUFACTURER
        $this->runExecute(" CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_manufacturer` (
`id` int(11) NOT NULL,
`manufacturer_id` int(11) NOT NULL,
`etsy` text CHARACTER SET utf8 NOT NULL,
`amz` text CHARACTER SET utf8 NOT NULL,
`ebay` text CHARACTER SET utf8 NOT NULL,
`ali` text CHARACTER SET utf8 NOT NULL,
`wish` text CHARACTER SET utf8 NOT NULL,
`date_added` datetime NOT NULL,
`date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        try {

            $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_order` (
`order_id` int(11) NOT NULL,
`code` varchar(11) NOT NULL,
`market_order_id` varchar(256) NOT NULL,
`order_status` varchar(64) CHARACTER SET utf8 NOT NULL,
`first_name` varchar(32) CHARACTER SET utf8 NOT NULL,
`last_name` varchar(32) CHARACTER SET utf8 NOT NULL,
`total` float(10,2) NOT NULL,
`shipping_address` text CHARACTER SET utf8 NOT NULL,
`billing_address` text CHARACTER SET utf8 NOT NULL,
`phone` varchar(15) NOT NULL,
`email` varchar(32) CHARACTER SET utf8 NOT NULL,
`city` varchar(32) CHARACTER SET utf8 NOT NULL,
`town` varchar(256) CHARACTER SET utf8 NOT NULL,
`shipping_info` text CHARACTER SET utf8 NOT NULL,
`payment_info` text CHARACTER SET utf8 NOT NULL,
`date_added` datetime NOT NULL,
`date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");



            $this->db->query("ALTER TABLE `".DB_PREFIX."ex_order`
  ADD PRIMARY KEY (`order_id`);");

            $this->db->query("ALTER TABLE `" . DB_PREFIX . "ex_order`
MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");
        }catch (Exception $exception){

        }

        $this->runExecute("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_ordered_product` (
`order_id` int(11) NOT NULL,
`product_id` int(11) NOT NULL,
`model` varchar(64) CHARACTER SET utf8 NOT NULL,
`item_id` int(11) NOT NULL,
`quantity` int(3) NOT NULL,
`price` float(10,2) NOT NULL,
`name` varchar(256) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->runExecute("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_order_status` (
`order_status_id` int(11) NOT NULL,
`name` varchar(64) CHARACTER SET utf8 NOT NULL,
`etsy` varchar(64) CHARACTER SET utf8 NOT NULL,
`amz` varchar(64) CHARACTER SET utf8 NOT NULL,
`ali` varchar(64) CHARACTER SET utf8 NOT NULL,
`ebay` varchar(64) CHARACTER SET utf8 NOT NULL,
`wish` varchar(64) CHARACTER SET utf8 NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;");



        $this->runExecute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ex_product_variant` (
 
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 NOT NULL,
  `model` varchar(50) CHARACTER SET utf8 NOT NULL,
  `barcode` varchar(255) CHARACTER SET utf8 NOT NULL,
  `quantity` int(5) NOT NULL,
  `price` float(10,2) NOT NULL,
  `variant_info` varchar(255) CHARACTER SET utf8 NOT NULL,
  `variant_count` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->runExecute("ALTER TABLE   `".DB_PREFIX."ex_product_variant`
  ADD PRIMARY KEY (`variant_id`);");

        $this->runExecute("ALTER TABLE `".DB_PREFIX."ex_product_variant`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT;");

        $this->runExecute("ALTER TABLE `".DB_PREFIX."product_option` ADD INDEX( `product_id`);");


        $isExists=$this->db->query("select * from `" . DB_PREFIX . "ex_order_status` ");
        if(!$isExists->num_rows){
            $this->runExecute("INSERT INTO `" . DB_PREFIX . "ex_order_status` (`order_status_id`,`name`, `oc`, `n11`, `gg`, `ty`, `eptt`, `hb`) VALUES
(1,'Onay Bekliyor', '1', '1', '0', '0', '0', '0'),
(2, 'Kargolanma Aşamasında','2', '5', 'STATUS_WAITING_CARGO_INFO', 'ReadyToShip', 'kargo_yapilmasi_bekleniyor', 'Open'),
(3, 'Kargolandı','3', '6', 'STATUS_WAITING_APPROVAL', 'Shipped', 'gonderilmis', 'Unpacked');");
        }
        $this->runExecute("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_product` (
`product_id` int(11) NOT NULL,

`etsy` text CHARACTER SET utf8 NOT NULL,
`amz` text CHARACTER SET utf8 NOT NULL,
`ebay` text CHARACTER SET utf8 NOT NULL,
`ali` text CHARACTER SET utf8 NOT NULL,
`wish` text CHARACTER SET utf8 NOT NULL,
`date_added` datetime NOT NULL,
`date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->runExecute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ex_attribute` (
  `attribute_id` int(11) NOT NULL,
  `category_id` varchar(64) CHARACTER SET utf8 NOT NULL,
  `attribute` longtext CHARACTER SET utf8 NOT NULL,
   `required` varchar(256) NOT NULL,
  `code` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        $this->runExecute("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_product_error` (
  `product_id` int(11) NOT NULL,
   `code` varchar(10) NOT NULL,
     `type` int(1) NOT NULL,
  `error` text CHARACTER SET utf8 NOT NULL,
  `solution_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
`date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        $this->runExecute("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ex_product_to_marketplace` (
`product_id` int(11) NOT NULL,

`etsy` text CHARACTER SET utf8 NOT NULL,
`amz` text CHARACTER SET utf8 NOT NULL,
`ebay` text CHARACTER SET utf8 NOT NULL,
`ali` text CHARACTER SET utf8 NOT NULL,
`wish` text CHARACTER SET utf8 NOT NULL,
`date_added` datetime NOT NULL,
`date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        $this->runExecute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ex_product_question` (
 
  `product_question_id` int(11) NOT NULL,
  `question_id` varchar(255) NOT NULL,
  `user` varchar(50) CHARACTER SET utf8 NOT NULL,
  `product` varchar(255) CHARACTER SET utf8 NOT NULL,
  `code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `is_rejected` int(1) NOT NULL,
  `answered` int(1) NOT NULL,
  `message` text CHARACTER SET utf8 NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->runExecute("ALTER TABLE   `".DB_PREFIX."ex_product_question`
  ADD PRIMARY KEY (`product_question_id`);");

        $this->runExecute("ALTER TABLE `".DB_PREFIX."ex_product_question`
  MODIFY `product_question_id` int(11) NOT NULL AUTO_INCREMENT;");

        $this->runExecute("CREATE TABLE `".DB_PREFIX."ex_option` (
  `option_id` int(11) NOT NULL,
  `code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `category_id` varchar(11) NOT NULL,
  `order_number` int(4) NOT NULL,
  `oc_option_id` int(11) NOT NULL,
  `market_option_id` varchar(64) CHARACTER SET utf8 NOT NULL,
  `market_option_name` varchar(64) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


        $this->runExecute("CREATE TABLE `".DB_PREFIX."ex_option_value` (
  `option_value_id` int(11) NOT NULL,
  `matched_option_id` int(11) NOT NULL,
  `oc_option_value_id` int(11) NOT NULL,
  `market_option_value_id` varchar(64) CHARACTER SET utf8 NOT NULL,
  `market_option_value_name` varchar(64) CHARACTER SET utf8 NOT NULL,
  `market_option_value_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        if(!$isExists->num_rows) {

            $this->runExecute("ALTER TABLE `".DB_PREFIX."ex_option`
  ADD PRIMARY KEY (`option_id`);");

            $this->runExecute("ALTER TABLE `".DB_PREFIX."ex_option_value`
  ADD PRIMARY KEY (`option_value_id`);");

            $this->runExecute("ALTER TABLE `".DB_PREFIX."ex_option`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT;");

            $this->runExecute("ALTER TABLE `".DB_PREFIX."ex_option_value`
  MODIFY `option_value_id` int(11) NOT NULL AUTO_INCREMENT;");


            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_category`
ADD PRIMARY KEY (`id`);");

            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_product_to_marketplace`
ADD PRIMARY KEY (`product_id`);");

            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_manufacturer`
ADD PRIMARY KEY (`id`);");

            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_order_status`
ADD PRIMARY KEY (`order_status_id`);");

            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_attribute`
  ADD PRIMARY KEY (`attribute_id`);");

            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_attribute`
  MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT;");

            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_category`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");
            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_manufacturer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");

            $this->runExecute("ALTER TABLE `" . DB_PREFIX . "ex_order_status`
MODIFY `order_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");
        }

    }


    private function runExecute($sql)
    {
        try {
            $this->db->query($sql);
        } catch (Exception $exception) {

            // echo $exception->getMessage();
        }

    }

}