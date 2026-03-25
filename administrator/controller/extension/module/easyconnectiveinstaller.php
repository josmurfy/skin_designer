<?php
class ControllerExtensionModuleEasyconnectiveinstaller extends Controller {
    private $error = array();
    private $token_data;


    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->model('extension/module/easyconnectiveinstaller');
        $this->token_data=$this->model_extension_module_easyconnectiveinstaller->getToken();
        $this->load->language('extension/module/easyconnectiveinstaller');

    }

    public function index() {
        //$this->response->redirect($this->url->link('easyconnective/setting', 'user_token=' . $this->session->data['user_token'], true));

        $this->updater();
    }

    public function updater()
    {




        $data = $this->language->all();
        $this->load->model('setting/setting');
        $this->document->setTitle($this->language->get('text_manage'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/dashboard', $this->token_data['token_link'], true),
            'text' => $this->language->get('text_home'),
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('easyconnective/setting', $this->token_data['token_link'], true),
            'text' => $this->language->get('heading_title'),
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/module/easyconnectiveinstaller', $this->token_data['token_link'], true),
            'text' => $this->language->get('text_manage'),
        );


        $this->load->model('extension/module/easyconnectiveinstaller');

        $versiyon_data = '1.1.1';//$this->model_easyconnective_general->getVersionInfo($this->model_extension_module_easyconnectiveinstaller->version()['version']);

        $data['versiyon_content'] = '';//$.versiyon_data ?  html_entity_decode($versiyon_data['info']):'';
        $data['versiyon_number'] = '';//$versiyon_data ? $versiyon_data['versiyon']:'';

        $data['text_version'] = $this->config->get('module_easyconnective_version');
        $data['action'] = $this->url->link('extension/module/easyconnectiveinstaller', $this->token_data['token_link'], true);
        $data['cancel'] = $this->url->link('easyconnective/setting', $this->token_data['token_link'], true);

        $data['token_link'] = $this->token_data['token_link'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/easyconnectiveinstaller', $data));
    }


    public function update()
    {

       // $this->load->model('extension/module/easyconnectiveinstaller');


        if (!isset($this->request->get['stage'])) {
            $stage = 'check_server';
        } else {
            $stage = $this->request->get['stage'];
        }

        if (!isset($this->request->get['beta']) || $this->request->get['beta'] == 0) {
            $beta = 0;
        } else {
            $beta = 1;
        }


        switch ($stage) {
            case 'check_server': // step 1
                $response = $this->model_extension_module_easyconnectiveinstaller->updateTest();
                sleep(1);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($response));
                break;
            case 'check_version': // step 2
                $response = $this->model_extension_module_easyconnectiveinstaller->updateCheckVersion($beta);

                sleep(1);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($response));

                break;
            case 'download': // step 3
                $response = $this->model_extension_module_easyconnectiveinstaller->updateDownload($beta);

                sleep(1);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($response));
                break;
            case 'extract': // step 4
                $response = $this->model_extension_module_easyconnectiveinstaller->updateExtract();

                sleep(1);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($response));
                break;
            case 'remove': // step 5 - remove any files no longer needed
                $response = $this->model_extension_module_easyconnectiveinstaller->updateRemove($beta);

                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($response));
                break;
            case 'run_patch': // step 6 - run any db updates or other patch files



                $response = array('error' => 0, 'response' => '', 'percent_complete' => 90, 'status_message' => $this->language->get('text_update_db'));
                $this->refreshTheme();
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($response));
                break;
                case 'update_version': // step 7 - update the version number

                $this->load->model('setting/setting');

                $response = $this->model_extension_module_easyconnectiveinstaller->updateUpdateVersion($beta);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($response));

                //$this->refresh();


                break;

            default;
        }
    }

    public function install()
    {
        $this->response->redirect($this->url->link('extension/module/easyconnectiveinstaller',$this->token_data['token_link'] , 'SSL'));
    }


    public function install_module()
    {

        $this->load->model('extension/module/easyconnectiveinstaller');
        $this->model_extension_module_easyconnectiveinstaller->install();
        $this->response->redirect($this->url->link('easyconnective/setting',$this->token_data['token_link'] , 'SSL'));


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

}
