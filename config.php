<?php
if(!defined('SUPPORT_SYSTEM')) 
    die('У вас нет прав на выполнение данного файла!');

require_once ('core/functions.php');
require_once ('core/update-core.php');
require_once ('core/log.php');

/**
 * Сервер БД
 */

define('DB_HOST', '');

/**
 * Пользователь
 */

define('DB_USER', '');

/**
 * Пароль
 */

define('DB_PASS', '');

/**
 * БД
 */

define('DB', '');

/**
 * Модель
 */
  
define('MODEL', 'model/model.php');

/**
 * Админ модель
 */

define('ADMIN_MODEL', 'model/admin-model.php');

/**
 * Шаблон админки
 */

define('ADMIN_TEMPLATE', 'templates/');

/**
 * Контроллер
 */

define('CONTROLLER', 'controller/controller.php');

/**
 * Темы
 */

define('CONTENT', '/content/themes/');

/**
 * Глобальный путь
 */

define('PATH', __DIR__ . '/');

/*
 * Путь к папке обновлений
 */

define('PATH_UPDATE', PATH . 'update/');
	
/**
 * Путь к резервным копиям
 */

define('PATH_BACKUP', PATH . 'backup/');

/**
 * Соль
 */

define('SALT', '$2a$10$WDdjgt1cZTV6v36sxnzVJH/A0Dm8sT6'); 

/**
 * Соль паролей
 */
 
define('PASS_SALT', '$2a$10$WDdjgkyfnPlSt1cZTV6v3A$'); 

/**
 * Директория плагинов
 */

define('PLUGINS', PATH . 'content/plugins/');

/**
 * Таблица банов при неверной авторизации
 */

define('ERROR_LOG_TABLE', 'support_error_login');

/**
 * Колонка с IP забаненого пользователя
 */

define('ERROR_IP_COLUMN', 'ip');

/**
 * Колонка с датой разбана
 */

define('ERROR_DATE_COLUMN', 'date');

/**
 * Колонка с количество ошибок
 */

define('ERROR_NUM_COLUMN', 'errors');

/**
 * Создание объектов
 */

try
{ 
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB . ';charset=utf8', DB_USER, DB_PASS);  
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND , "SET NAMES utf8");
    $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES , true);
    $db->exec('set names utf8');
}  

catch(PDOException $e)
{  
    echo $e->getMessage();
    $coreLog ->write($e->getMessage());  
}

require_once ('core/config-functions.php');

/**
 * Определяем константы из БД
 */

define_constants();

/**
 * Подключаем активные плагины
 */

$coreLog = new coreLog(PATH . 'core/logs/', LOGGING);

include_active_plugins();

try
{
	$db_users = new PDO('mysql:host=' . DB_USERS_HOST . ';dbname=' . DB_USERS . ';charset=utf8', DB_USERS_USER, DB_USERS_PASS);  
	$db_users->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db_users->exec('set names utf8');
}

catch(PDOException $e)
{  
    echo $e->getMessage();
    $coreLog ->write($e->getMessage());  
}

?>