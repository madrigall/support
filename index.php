<?php
// Запрет прямого обращения
define('SUPPORT_SYSTEM', TRUE);

if(!file_exists('config.php'))
{
    if(file_exists('install.php'))
    {
        header('Location: /install.php');
        exit();
    }
    else
    {
        echo "Файла установки не существует!";
        exit();
    }
}

// подключение файла конфигурации
require_once 'config.php';

// подключение контроллера
require_once CONTROLLER;
?>