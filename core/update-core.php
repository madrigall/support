<?php
    
    class updateCore
    {
     	private $server_url = null;

     	private $target = null;

     	private $updatePath = null;

     	private $backupPath = null;

     	private $license = null;

     	private $version = null;

        private $hashes_all = null;

        private $update_hashes = null;

        public function __construct($license, $server_url, $version)
        {
            $this->license = $license;
            $this->server_url = $server_url;

            $this->target = "update.zip";
            $this->version = $version;

            $this->updatePath = PATH_UPDATE;
            $this->updatePath = str_replace('\\', "/", $this->updatePath);

            $this->backupPath = PATH_BACKUP;
            $this->backupPath = str_replace('\\', "/", $this->backupPath);
        }

        public function __descruct()
        {

        }

        public function have_update()
        {
            $request_p = array(
                "key" => $this->license,
                "target" => "check-version",
                "version" => $this->version
            );

            $response = json_decode(self::request($request_p));

            if($response->status)
                switch ($response->status)
                {
                    case 'error':
                        return "params_error";
                    break;
                    
                    case 'valid_version':
                        return 'valid_version';
                    break;

                    case 'need_update':
                        return array("status" => "need_update", "version" => $response->version, "update_info" => $response->update_info);
                    break;

                    case 'invalid_target':
                        return "invalid_target";
                    break;

                    case 'license_error':
                        return "license_error";
                    break;

                    case 'technical_work':
                        return "technical_work";
                    break;

                    default:
                        return "server_error";
                    break;
                }
            else
                return "server_error";
        }

        public function getUpdateKey()
        {
            $request_p = array(
                "key" => $this->license,
                "target" => "need-update",
                "version" => $this->version
            );

            $response = json_decode(self::request($request_p));
           
            if(isset($response->status) && $response->status == "valid_version")
                return false;
            else
                return $response;
        }

        public function createAllHashes()
        {
            $hashes_admin           = self::createHashes("admin");
            $hashes_controller      = self::createHashes("controller");
            $hashes_model           = self::createHashes("model");
            $hashes_core            = self::createHashes("core");
            $hashes_main            = self::createHashes("main");


            $all_hashes = array_merge($hashes_admin, $hashes_controller, $hashes_model, $hashes_core, $hashes_main);

            return $all_hashes;
        }

        public function createHashes($dir_h, $delete_path = false)
        {
            if($dir_h == "main")
            {
                $hashes["index.php"] = hash_file("sha256", PATH . DIRECTORY_SEPARATOR . "index.php");
                $hashes["error.php"] = hash_file("sha256", PATH . DIRECTORY_SEPARATOR . "error.php");
            }
            else
            {
                $dir = PATH . DIRECTORY_SEPARATOR . $dir_h;
                $files = array();
                $hashes = array();
                
                self::allDir($dir, $files);

                for($ii = 0; $ii < count($files) ; $ii++)
                {
                    $fileReplace = $files[$ii];

                    $down_file = str_replace('../', '/', $fileReplace);
                    $down_file = str_replace('//', '/', $down_file);

                    if(!$delete_path)
                        $file_to_array = str_replace(PATH . DIRECTORY_SEPARATOR, "", $down_file);
                    else
                        $file_to_array = str_replace($dir . DIRECTORY_SEPARATOR, "", $down_file);

                    $hashes[$file_to_array] = hash_file("sha256", $down_file);
                }
            }

            return $hashes;   
        }

        public function allDir($dir, &$files)
        {
            $result = scandir($dir);

            unset($result[0], $result[1]);

            foreach($result as $v)
            {
                if ($v == '.' || $v == '..' || $v =='../' || $v == "Thumbs.db") continue;
                
                if (is_dir($dir . DIRECTORY_SEPARATOR . $v)) 
                {
                    self::allDir($dir . DIRECTORY_SEPARATOR . $v, $files);
                }
                else 
                {
                    $files[] = $dir . DIRECTORY_SEPARATOR . $v;
                }
            }
        }

        public function prepareUpdatePath()
        {
            if(is_dir($this->updatePath))
            {
                $result = self::delTree($this->updatePath);
                
                if($result)
                    return true;
                else
                    return false;
            }

            return true;
        }

        public function delTree($dir) 
        { 
            $files = array_diff(scandir($dir), array('.','..')); 

            foreach ($files as $file)  
                (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file"); 

            if(rmdir($dir))
                return true;
            else
                return false;
        }

        public function prepare()
        {
            $session_id = session_id();
            
            if($session_id)
                session_write_close();
            
            ignore_user_abort(true);

            return $session_id;
        }

        public function startUpdate()
        {
            global $coreLog;

            $coreLog->write("Подготовка папки update для обновлений.");

            $session_id = self::prepare();
            $result_clean = self::prepareUpdatePath();

            if(!$result_clean)
            {
                $coreLog->write("Ошибка при подготовки папки обновлений. Вероятней всего нет прав на папку.");
                return "error_clean";
            }

            $coreLog->write("Подготовка папки update прошла успешно.");
            $update_key = self::getUpdateKey();

            if(!$update_key)
            {
                $coreLog->write("Валидная версия, отмена обновлений.");
                return "valid_version";
            }

            $real_version = $update_key->update_version;

            $coreLog->write("Начало загрузки обновлений.");

            $hash = self::downloadFile($update_key);

            if($hash == "error" || $hash == "error_file")
            {    
                $coreLog->write("Обновления не загружены. " . $hash);
                return "error_in_hash";
            }

            $coreLog->write("Обновления загружены. Хеш - " . $hash);

            $real_hash = self::returnHash($hash);
            
            if($real_hash == "error")
            {
                $coreLog->write("Ошибка при проверке хеша. " . $real_hash);
                return "hash_error";
            }

            if($real_hash == "invalid_hash")
            {
                $coreLog->write("Ошибка при проверке хеша. Очистка загруженных файлов. " . $real_hash);
                self::prepareUpdatePath();

                return "invalid_hash";

            }

            self::restore_session($session_id);
            $coreLog->write("Распаковка обновлений.");

            $zip = new ZipArchive();
            $zip->open($this->updatePath . $this->target);
            
            $unzip_status = $zip->extractTo($this->updatePath);
            $zclose_status = $zip->close();

            $status_unlink = unlink($this->updatePath . $this->target);

            if(!$unzip_status)
            {
                $coreLog->write("Невозможно распаковать архив. Возможно он поврежден.");
                return "unzip_fail";
            }

            if(!$zclose_status)
            {
                $coreLog->write("Невозможно корректно закрыть архив. Возможно он поврежден.");
                return "zclose_fail";
            }

            if(!$status_unlink)
            {
                $coreLog->write("Невозможно удалить архив. Возможно нет прав.");
                return "unlink_fail";
            }

            $this->hashes_all = self::createAllHashes();
            $this->update_hashes = self::createHashes("update", true);

            $coreLog->write("Хеши получены.");

            $files_to_replace = self::replace_files($this->hashes_all, $this->update_hashes);

            $coreLog->write("Создание резервной копии.");
            
            self::doBackup($files_to_replace);

            return self::createArchive($this->backupPath);

            /*self::deleteFiles();

            self::executeSqlQuery();

            self::updateFiles();

            self::prepareUpdatePath();

            self::updateVersion($real_version);*/

            //return true;
            

        }

        /**
         * Functions
         */
        
        public function createArchive($dir)
        {
            
            $zip = new ZipArchive();
            $fileName = PATH . "backup.zip";
            return $fileName;
            $zip->open($fileName, ZIPARCHIVE::CREATE);

            
            $dirHandle = opendir($dir);
            while(false !== ($file = readdir($dirHandle))) 
                $zip->addFile($dir . DIRECTORY_SEPARATOR . $file, $file);

            $zip->close();
              

             
          
        }
        
        public function doBackup($files)
        { 
            if(!file_exists($this->backupPath))
                mkdir($this->backupPath);

            $errors = array();

            foreach ($files as $key => $value)
            {
                if($value == "delete.json" || $value == "sql.json")
                    continue;
                
                $result_c = self::copyFiles(PATH, $this->backupPath, $value);    
                
                if(!$result_c)
                    array_push($errors, $value);
            }

            if(empty($errors))
                return true;
            else
                return $errors;
        }
        
        public function replace_files($hashes_all, $update_hashes)
        {
            $files_to_replace = array();

            foreach ($update_hashes as $key => $value)
            {
                if($key == "delete.json" || $key == "sql.json")
                    continue;

                if($hashes_all[$key] != $value)
                    array_push($files_to_replace, $key);
            }

            return $files_to_replace;
        }

        public function copyFiles($from, $to, $file)
        {
            global $coreLog;

            if(!is_dir($to))
                mkdir($to);            
            
            $file_from = $from . DIRECTORY_SEPARATOR . $file;
            $file_to = $to . DIRECTORY_SEPARATOR . $file;

            $arr = explode(DIRECTORY_SEPARATOR, $file);  

            $curr = array(); 

            foreach($arr as $key => $val)
            { 
                if(!empty($val))
                { 
                    array_push($curr, $val); 
                    
                    if(is_dir($from . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $curr)) && !file_exists($to . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $curr)))
                        mkdir($to . implode(DIRECTORY_SEPARATOR, $curr) . DIRECTORY_SEPARATOR, 0777); 

                    //mkdir(implode(DIRECTORY_SEPARATOR, $curr) . DIRECTORY_SEPARATOR, 0777); 
                    //return PATH .implode('/',$curr)."/";
                }
            }  

            $file_from = str_replace("\\", "/", $file_from);
            $file_to = str_replace("\\", "/", $file_to);

            if(file_exists($file_from))
                if(!file_exists($file_to))
                {
                    //$file_to = str_replace('/', '\\', $file_to);
                    $copy_result = copy($file_from, $file_to);
                }

            if($copy_result)
                return true;
            else
                return false;

        }
        
        public function downloadFile($key)
        {
            $params = array(
                "key" => $this->license,
                "update_key" => $key->update,
                "target" => "get-update",
                "version" => $this->version
            );

            $data = "update_data=" . json_encode($params); 

            $curl = curl_init($this->server_url . "/api.php");

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);

            if(self::isJSON($response))
            {
                $response = json_decode($response);
                return $response->status;
            }
            else
            {
                $download_file = $this->updatePath . DIRECTORY_SEPARATOR . $this->target;
                if(!is_dir($this->updatePath))
                    mkdir($this->updatePath);

                $file = fopen($download_file,'wb');
                fwrite($file, $response);
                fclose($file);

                $hash = hash_file("sha256", $download_file);
                return $hash;
            }
        }

        public function restore_session($session_id)
        {
            if($session_id)
                session_start();
        }

        public function isJSON($string) 
        {
            return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
        }

        public function returnHash($hash)
        {
            $data = array(
                "key"       => $this->license,
                "target"    => "check-hash",
                "version"   => $this->version,
                "hash"      => $hash
            );

            $response = self::request($data);
            $response = json_decode($response);

            return $response->status;
        }
        
        public function request($data)
        {
            $string = "data=" . json_encode($data); 
            $curl = curl_init($this->server_url . "/api.php");

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $string);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);

            return $response;
        }
    }
?>