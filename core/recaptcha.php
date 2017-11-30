<?php
class recaptcha 
{
    private $publicKey;

    public function __construct($sekretKey)
    {
        $this->secretKey = $sekretKey;
    }
    
    public function check($data)
    {
        $ip = getenv("HTTP_X_FORWARDED_FOR");

        if(empty($ip) || $ip =='unknown') 
            $ip = getenv("REMOTE_ADDR"); 

        $params = array(
            "secret" => $this->secretKey,
            "response" => $data,
            "remoteip" => $ip
        );

        $url = "https://www.google.com/recaptcha/api/siteverify?" . http_build_query($params);
        
        if(empty($data) || is_null($data))
            return false;

        if(function_exists("curl_version"))
        {
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
        }
        else
            $response = file_get_contents($url);

        if(empty($response) || is_null($response))
            return false;

        
        $json = json_decode($response);

        if($json->error-codes)
            return $json->error-codes;
        else
            return $json->success;
    }
}

?>