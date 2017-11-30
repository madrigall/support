<?php

defined('SUPPORT_SYSTEM') or die('Access denied');

function hash_md5($postPass)
{   
    $cryptPass = false;
    $cryptPass = md5($postPass);
    
    return $cryptPass;
}

function dle($postPass)
{   
    $cryptPass = false;
    $cryptPass = md5(md5($postPass));
    
    return $cryptPass;
}


function joomla($realPass, $postPass)
{   
    $cryptPass = false;
    $parts = explode( ':', $realPass);
    $salt = $parts[1];
    $cryptPass = md5($postPass . $salt) . ":" . $salt;
    
    return $cryptPass;
}

function ipb($postPass, $salt)
{   
    $cryptPass = false;
    $cryptPass = md5(md5($salt).md5($postPass));
    
    return $cryptPass;
}

function xenforo($postPass, $salt)
{   
    $cryptPass = false;
    $cryptPass = hash('sha256', hash('sha256', $postPass) . $salt);
    
    return $cryptPass;
}

function wordpress($realPass, $postPass)
{
    $cryptPass = false;
    $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $count_log2 = strpos($itoa64, $realPass[3]);
    $count = 1 << $count_log2;
    $salt = substr($realPass, 4, 8);
    $input = md5($salt . $postPass, TRUE);
    do 
    {
        $input = md5($input . $postPass, TRUE);
    } 
    while (--$count);
                
    $output = substr($realPass, 0, 12);
                
    $count = 16;
    $i = 0;
    do 
    {
        $value = ord($input[$i++]);
        $cryptPass .= $itoa64[$value & 0x3f];
        if ($i < $count)
            $value |= ord($input[$i]) << 8;
        $cryptPass .= $itoa64[($value >> 6) & 0x3f];
        if ($i++ >= $count)
            break;
        if ($i < $count)
            $value |= ord($input[$i]) << 16;
        $cryptPass .= $itoa64[($value >> 12) & 0x3f];
        if ($i++ >= $count)
            break;
        $cryptPass .= $itoa64[($value >> 18) & 0x3f];
    } 
        while ($i < $count);
                
    $cryptPass = $output . $cryptPass;

    return $cryptPass;
}

function vbulletin($postPass, $salt)
{   
    $cryptPass = false;
    $cryptPass = md5(md5($postPass) . $salt);
    
    return $cryptPass;
}

function drupal($postPass, $realPass)
{   
    $cryptPass = false;
    $setting = substr($realPass, 0, 12);
    $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $count_log2 = strpos($itoa64, $setting[3]);
    $salt = substr($setting, 4, 8);
    $count = 1 << $count_log2;
    $input = hash('sha512', $salt . $postPass, TRUE);
    do
    {
        $input = hash('sha512', $input . $postPass, TRUE);
    }
    while (--$count);

    $count = strlen($input);
    $i = 0;
  
    do
    {
        $value = ord($input[$i++]);
        $cryptPass .= $itoa64[$value & 0x3f];
        if ($i < $count)
            $value |= ord($input[$i]) << 8;
        $cryptPass .= $itoa64[($value >> 6) & 0x3f];
        if ($i++ >= $count)
            break;
        if ($i < $count)
            $value |= ord($input[$i]) << 16;
        $cryptPass .= $itoa64[($value >> 12) & 0x3f];
        if ($i++ >= $count)
            break;
        $cryptPass .= $itoa64[($value >> 18) & 0x3f];
    }
    while ($i < $count);
    $cryptPass =  $setting . $cryptPass;
    $cryptPass =  substr($cryptPass, 0, 55);

    return $cryptPass;
}

function custom($postPass, $realPass)
{
    if (hs_has_hook(__FUNCTION__))
        return hs_call_hook(__FUNCTION__, func_get_args());
}
?>