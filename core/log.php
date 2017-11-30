<?php
date_default_timezone_set('Europe/Kiev');
session_start();

class coreLog 
{
    private $text = null;
    
    private $file = null;
    
    private $path = null;
    
    private $logging = null;
  
    public function __construct($path, $logging)
    {
        //error_reporting(E_ALL);

        $this->path = $path;
        $this->file = date("d-m-Y") . ".log";
        $this->logging = $logging;
        
        if(!file_exists($path . DIRECTORY_SEPARATOR . $this->file))
        {
            if(!is_dir($path))
                mkdir($path);
                
            $fp = fopen($path . DIRECTORY_SEPARATOR .  $this->file, "w+");
            fwrite($fp, iconv("UTF-8", "UTF-8", "\r\n")); 
            fclose($fp);  
        }

        self::extendedLog();
       
    }
    
    public function get_logging()
    {
        if($this->logging == "TRUE")
            return true;
        else
            return false;
    }

   
    public function extendedLog()
    {   
        //$start = microtime(true);
        
        $ip = getenv("HTTP_X_FORWARDED_FOR");

        if(empty($ip) || $ip =='unknown') 
            $ip = getenv("REMOTE_ADDR"); 

        /*
        if(strstr($_SERVER['HTTP_USER_AGENT'], 'YandexBot'))
        {
            $agent = 'YandexBot';
        }
        elseif(strstr($_SERVER['HTTP_USER_AGENT'], 'Googlebot'))
        {
            $agent = 'Googlebot';
        }
        else 
        { 
            $agent = $_SERVER['HTTP_USER_AGENT']; 
        }
        */
        if($this->logging == "TRUE")
        {
            $fp = fopen($this->path . DIRECTORY_SEPARATOR . $this->file, "a");
            
            $referer = $_SERVER["HTTP_REFERER"];
            $page = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];    //какая страница сайта
            $date = "[" . date("d-m-Y H:i") . "]";
            $log = $date . "[" . $ip . "] " . "{$_SESSION["support"]["name"]} перешел на " . $page . ",referer: " . $referer;
             
            fwrite($fp, iconv("UTF-8", "UTF-8", "$log\r\n"));
            fclose($fp);
        }
        
        //$time = microtime(true) - $start;
        //printf('Скрипт выполнялся %.4F сек.', $time);

    }

    public function write($text)
    {
        if($this->logging == "TRUE")
        {
            $fp = fopen($this->path . DIRECTORY_SEPARATOR . $this->file, "a");
            
            if(!$fp)
            {
                fclose($fp);
                return false;
            }
            
            $date = "[" . date("d-m-Y H:i") . "]";

            $ip = getenv("HTTP_X_FORWARDED_FOR");

            if(empty($ip) || $ip =='unknown') 
                $ip = getenv("REMOTE_ADDR"); 
           
            
            $log = $date . "[" . $ip . "] " . $text;
      
            fwrite($fp, iconv("UTF-8", "UTF-8", "$log\r\n"));
            fclose($fp);
        }
    }

}    
    

?>
