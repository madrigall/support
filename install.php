<?php

$databases = NULL;
error_reporting(FALSE);

function generateSalt($base)
{
    $salt = '';
    $length = rand(70, 80);

    for($i=0; $i<$length; $i++) 
    {
        $salt .= (chr(rand(33, 126)));
    }

    $salt = str_replace("'", "", $salt);

    if($base)
    	return base64_encode($salt);
    else
    	return $salt;
}
if($_POST["first-step"])
{	
	$switch  = (bool) $_POST["SWITCH"];
	$db_user = (string) $_POST["DB_USER"];
	$db_host = (string) $_POST["DB_HOST"];
	$db_pass = (string) $_POST["DB_PASS"];

	try
	{
		$db_t = new PDO('mysql:host=' . $db_host . ';charset=utf8', $db_user, $db_pass);  
    	$db_t->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$db_t->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);
    	$db_t->setAttribute(PDO::ATTR_EMULATE_PREPARES , true);
    	$db_t->exec('set names utf8');
    }
    catch(PDOException $e)
	{  
	   header("Location: ?fail=db_first");
	   exit();
	}

	if($switch)
	{
		if((string) $_POST["DB_LIST"])
			$db_name = (string) $_POST["DB_LIST"];

		if((string) $_POST["DB"])
		{
			$db_name = (string) $_POST["DB"];
			
			$sql_p = "CREATE DATABASE IF NOT EXISTS `$db_name`";

			$stmt_p = $db_t->prepare($sql_p);

			$stmt_p->execute();
			if(!$stmt_p)
			{
				if(!file_exists("config.php"))
				{
					header("Location: ?fail=db");
					exit();
				}
			}

		}
    
		$salt_g = generateSalt(false);
		$salt_p = generateSalt(false);

		//Create config (fist step)
		$config = "<?php
if(!defined('SUPPORT_SYSTEM')) 
    die('У вас нет прав на выполнение данного файла!');

require_once ('core/functions.php');
require_once ('core/update-core.php');
require_once ('core/log.php');

/**
 * Сервер БД
 */

define('DB_HOST', '$db_host');

/**
 * Пользователь
 */

define('DB_USER', '$db_user');

/**
 * Пароль
 */

define('DB_PASS', '$db_pass');

/**
 * БД
 */

define('DB', '$db_name');

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

define('SALT', '$salt_g'); 

/**
 * Соль паролей
 */
 
define('PASS_SALT', '$salt_p'); 

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
    \$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB . ';charset=utf8', DB_USER, DB_PASS);  
    \$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);
    \$db->setAttribute(PDO::ATTR_EMULATE_PREPARES , true);
    \$db->exec('set names utf8');
}  

catch(PDOException \$e)
{  
    echo \$e->getMessage();
    \$coreLog ->write(\$e->getMessage());  
}";

        //Config content ended
        
        if(!file_exists("config.php"))
            file_put_contents("config.php", $config);
        else
        {
        	header("Location: ?fail=config_isset");
        	exit();
        }

        if(file_exists("config.php"))
        {
            //Import Sql
            //
            define("SUPPORT_SYSTEM", true);
            include "config.php";

            global $db;

		
                $sql = "
                SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
                SET time_zone = '+00:00';


                /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
                /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
                /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
                /*!40101 SET NAMES utf8 */;

                --
                -- Структура таблицы `support_agents`
                --

                CREATE TABLE IF NOT EXISTS `support_agents` (
                  `support_id` int(11) NOT NULL,
                  `support_name` varchar(64) NOT NULL,
                  `support_pass` varchar(128) NOT NULL,
                  `support_email` varchar(64) NOT NULL,
                  `support_rank` int(11) NOT NULL DEFAULT '5',
                  `support_key` varchar(32) NOT NULL,
                  `support_last_key` varchar(32) NOT NULL,
                  `support_remember` tinyint(1) NOT NULL DEFAULT '0',
                  `banned` tinyint(1) NOT NULL DEFAULT '0'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

                --
                -- Структура таблицы `support_answers`
                --

                CREATE TABLE IF NOT EXISTS `support_answers` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `category_id` int(11) NOT NULL,
                  `question` text NOT NULL,
                  `support_answer` text NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `question` (`question`(255)),
                  FULLTEXT KEY `question_2` (`question`),
                  FULLTEXT KEY `question_3` (`question`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

                --
                -- Структура таблицы `support_categories`
                --

                CREATE TABLE IF NOT EXISTS `support_categories` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` text NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

                --
                -- Структура таблицы `support_config`
                --

                CREATE TABLE IF NOT EXISTS `support_config` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `name` varchar(64) NOT NULL,
                  `value` longtext NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

                --
                -- Дамп данных таблицы `support_config`
                --

                INSERT INTO `support_config` (`id`, `name`, `value`) VALUES
                (1, 'ACTIVE_TEMPLATE', '/content/themes/veysiot/'),
                (2, 'TITLE', 'Akteam Support System'),
                (3, 'DESCRIPTION', 'Support System'),
                (4, 'DEFAULT_RANK', '5'),
                (5, 'TICKETS_AMOUNT', '1'),
                (6, 'CURRENT_VERSION', '1.0.0.0'),
                (7, 'KEYWORDS', 'поддержка, support, пользователи, помощь'),
                (8, 'ANONYM_USERS', 'FALSE'),
                (9, 'LOGGING', 'TRUE'),
                (10, 'MAIN_SERVER', 'http://server.akteam.16mb.com/'),
                (11, 'UPDATE_SERVER', 'http://akteam.16mb.com/update/');

                -- --------------------------------------------------------

                --
                -- Структура таблицы `support_error_login`
                --

                CREATE TABLE IF NOT EXISTS `support_error_login` (
                  `ip` varchar(39) NOT NULL,
                  `date` datetime NOT NULL,
                  `errors` int(1) NOT NULL,
                  PRIMARY KEY (`ip`)
                ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

                -- --------------------------------------------------------

                --
                -- Структура таблицы `support_messages`
                --

                CREATE TABLE IF NOT EXISTS `support_messages` (
                  `ticket_id` bigint(20) NOT NULL,
                  `support_id` int(11) NOT NULL,
                  `support_message` text NOT NULL,
                  `message_id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_message_id` bigint(20) NOT NULL,
                  `message_time` datetime NOT NULL,
                  PRIMARY KEY (`message_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


                -- --------------------------------------------------------

                --
                -- Структура таблицы `support_plugins`
                --

                CREATE TABLE IF NOT EXISTS `support_plugins` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `plugin_name` varchar(32) NOT NULL DEFAULT '',
                  `plugin_path` varchar(64) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

                -- --------------------------------------------------------

                --
                -- Структура таблицы `support_user_messages`
                --

                CREATE TABLE IF NOT EXISTS `support_user_messages` (
                  `ticket_id` bigint(20) NOT NULL,
                  `user` varchar(60) NOT NULL,
                  `user_email` varchar(256) NOT NULL,
                  `user_message` text NOT NULL,
                  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `message_time` datetime NOT NULL,
                  PRIMARY KEY (`message_id`),
                  UNIQUE KEY `message_id` (`message_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


                --
                -- Структура таблицы `support_user_tickets`
                --

                CREATE TABLE IF NOT EXISTS `support_user_tickets` (
                  `ticket_id` bigint(20) NOT NULL,
                  `ticket_name` varchar(100) NOT NULL,
                  `user` varchar(60) NOT NULL,
                  `support_id` int(11) NOT NULL,
                  `category` varchar(125) NOT NULL DEFAULT 'Общие вопросы',
                  `close` tinyint(1) NOT NULL DEFAULT '0',
                  `time` datetime NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


                /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
                /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
                /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";

                $stmt = $db->prepare($sql);
                $stmt->execute();

                if(!$stmt)
                {
                    header("Location: ?step=1&fail=true");
                    exit();
                }
            

            //Add config
            
            $config_2 = "

require_once ('core/config-functions.php');

/**
 * Определяем константы из БД
 */

define_constants();

/**
 * Подключаем активные плагины
 */

\$coreLog = new coreLog(PATH . 'core/logs/', LOGGING);

include_active_plugins();

";

            //Add
            if(file_exists("config.php"))
                file_put_contents("config.php", $config_2, FILE_APPEND);
            else
            {
                header("Location: ?fail=config");
                exit();
            }

            header("Location: ?step=2");
            exit();
        }

	}
	else
	{
		$sql_p = "show databases";
		$stmt_p = $db_t->prepare($sql_p);

		$stmt_p->execute();

		$databases_t = $stmt_p->fetchAll();

		if(empty($databases_t))
		{
			$databases_n = true;
		}
		else
		{
			$databases = $databases_t;
		}
	}
}

if($_POST["second-step"])
{
	$l_key = (string) $_POST["l_key"];

	define("SUPPORT_SYSTEM", true);

	if(!file_exists("config.php"))
	{
		header("Location: ?fail=config");
		exit();
	}

	include "config.php";

	global $db;

	$sql_p = "SELECT value FROM support_config WHERE name = :l_key";
	$stmt_p = $db->prepare($sql_p);

	$stmt_p->bindValue(':l_key', "LICENSE_KEY");
	$stmt_p->execute();

	$row = $stmt_p->fetchAll();

	if(!empty($row))
	{
		header("Location: ?step=3");
		exit();
	}

	$sql = "INSERT INTO support_config (name, value) VALUES(:l_name, :l_key)";

	$stmt = $db->prepare($sql);

	$stmt->bindValue(':l_name', "LICENSE_KEY");
	$stmt->bindValue(':l_key', $l_key);
  
    $stmt->execute();

    header("Location: ?step=3");
	exit();

}

if($_POST["third-step"])
{
    //Check DB connection
	$db_user_u = (string) $_POST["DB_USERS_USER"];
	$db_host_u = (string) $_POST["DB_USERS_HOST"];
	$db_pass_u = (string) $_POST["DB_USERS_PASS"];
	$method = (string) $_POST["method"];

	$switch  = (bool) $_POST["SWITCH"];

	try
	{
		$db_users_t = new PDO('mysql:host=' . $db_host_u . ';charset=utf8', $db_user_u, $db_pass_u);  
    	$db_users_t->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$db_users_t->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);
    	$db_users_t->setAttribute(PDO::ATTR_EMULATE_PREPARES , true);
    	$db_users_t->exec('set names utf8');
    }
    catch(Exception $e)
	{  
	    header("Location: ?step=3&fail=db_first");
	    exit();
	}
    
    if(!file_exists("config.php"))
    {
        header("Location: ?fail=config");
        exit();
    }

    define("SUPPORT_SYSTEM", true);
    
    include "config.php";

    global $db;
   
    if($method == "none")
    {    
        $sql_m = "UPDATE support_config SET value = :ANONYM_USERS_V WHERE name = :ANONYM_USERS";

        $stmt_m = $db->prepare($sql_m);

        $stmt_m->bindValue(':ANONYM_USERS', "ANONYM_USERS");
        $stmt_m->bindValue(':ANONYM_USERS_V', "TRUE");
        
        $stmt_m->execute();

        if(!$stmt_m)
        {
            header("Location: ?step=3&fail=add");
            exit();
        }

        $sql_p = "SELECT value FROM support_config WHERE name = :name_c";
		$stmt_p = $db->prepare($sql_p);

		$stmt_p->bindValue(':name_c', "DB_USERS");
		$stmt_p->execute();

		$row = $stmt_p->fetchAll();
       
		if(!empty($row))
		{	
            header("Location: ?step=5");
            exit();
		}

		$sql = "INSERT INTO support_config (name, value) VALUES(:db_name, :db_name_v), (:db_user, :db_user_v), (:db_host, :db_host_v), (:db_pass, :db_pass_v), (:method, :method_v)";

		$stmt = $db->prepare($sql);

		$stmt->bindValue(':db_name', "DB_USERS");
		$stmt->bindValue(':db_name_v', "");

		$stmt->bindValue(':db_user', "DB_USERS_USER");
		$stmt->bindValue(':db_user_v', "");

		$stmt->bindValue(':db_host', "DB_USERS_HOST");
		$stmt->bindValue(':db_host_v', "");

		$stmt->bindValue(':db_pass', "DB_USERS_PASS");
		$stmt->bindValue(':db_pass_v', "");

		$stmt->bindValue(':method', "HASH_CRYPT");
		$stmt->bindValue(':method_v', "none");
	  
	    $stmt->execute();
        
        //Добавляем в конфиг
        if(!$stmt)
	    {
	    	header("Location: ?step=3&fail=add");
	    	exit();
	    }
	    else
	    {
            $content = "

try
{
    \$db_users = new PDO('mysql:host=' . DB_USERS_HOST . ';dbname=' . DB_USERS . ';charset=utf8', DB_USERS_USER, DB_USERS_PASS);  
    \$db_users->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$db_users->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);
    \$db_users->setAttribute(PDO::ATTR_EMULATE_PREPARES , true);
    \$db_users->exec('set names utf8');
}

catch(PDOException \$e)
{  
    echo \$e->getMessage();
    \$coreLog ->write(\$e->getMessage());  
}
?>";

	    	if(file_exists("config.php"))
				file_put_contents("config.php", $content, FILE_APPEND);
			else
			{
				header("Location: ?fail=config");
				exit();
			}

			if($method != "none")
			{
	    		header("Location: ?step=4&method=$method");
	    		exit();
	    	}
            else
            {
                header("Location: ?step=5");
	    		exit();
            }
            
	    }
        
        header("Location: ?step=5");
	    exit();
    }
    //Выла выбрана база данных
    if($switch)
	{
		if((string) $_POST["DB_USERS_LIST"])
			$db_name_u = (string) $_POST["DB_LIST"];

		$sql_p = "SELECT value FROM support_config WHERE name = :name_c";
		$stmt_p = $db->prepare($sql_p);

		$stmt_p->bindValue(':name_c', "DB_USERS");
		$stmt_p->execute();

		$row = $stmt_p->fetchAll();
        
		if(!empty($row))
		{	
			header("Location: ?step=4&method=$method");
			exit();
		}

		$sql = "INSERT INTO support_config (name, value) VALUES(:db_name, :db_name_v), (:db_user, :db_user_v), (:db_host, :db_host_v), (:db_pass, :db_pass_v), (:method, :method_v)";

		$stmt = $db->prepare($sql);

		$stmt->bindValue(':db_name', "DB_USERS");
		$stmt->bindValue(':db_name_v', $db_name_u);

		$stmt->bindValue(':db_user', "DB_USERS_USER");
		$stmt->bindValue(':db_user_v', $db_user_u);

		$stmt->bindValue(':db_host', "DB_USERS_HOST");
		$stmt->bindValue(':db_host_v', $db_host_u);

		$stmt->bindValue(':db_pass', "DB_USERS_PASS");
		$stmt->bindValue(':db_pass_v', $db_pass_u);

		$stmt->bindValue(':method', "HASH_CRYPT");
		$stmt->bindValue(':method_v', $method);
	  
	    $stmt->execute();

	    if(!$stmt)
	    {
	    	header("Location: ?step=3&fail=add");
	    	exit();
	    }
	    else
	    {
	    	$content = "

	    	try
	    	{
	    		\$db_users = new PDO('mysql:host=' . DB_USERS_HOST . ';dbname=' . DB_USERS . ';charset=utf8', DB_USERS_USER, DB_USERS_PASS);  
		    	\$db_users->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    	\$db_users->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY , true);
		    	\$db_users->setAttribute(PDO::ATTR_EMULATE_PREPARES , true);
		    	\$db_users->exec('set names utf8');
	    	}

	    	catch(PDOException \$e)
	    	{  
	    	    echo \$e->getMessage();
	    	    \$coreLog ->write(\$e->getMessage());  
	    	}
	    	?>";

	    	if(file_exists("config.php"))
				file_put_contents("config.php", $content, FILE_APPEND);
			else
			{
				header("Location: ?fail=config");
				exit();
			}

            header("Location: ?step=4&method=$method");
            exit();
	    }
	}
	else
	{
		$sql_p = "show databases";
		$stmt_p = $db_users_t->prepare($sql_p);

		$stmt_p->execute();

		$databases_a_t = $stmt_p->fetchAll();

		if(empty($databases_a_t))
		{
			$databases_a_u = true;
		}
		else
		{
			$databases_authorization = $databases_a_t;
		}
	}
}

if($_POST["fourth-step"])
{
	define("SUPPORT_SYSTEM", true);
	include "config.php";

	global $db;

	$USERS_TABLE = (string) $_POST["USERS_TABLE"];
	$USERS_COLUMN_ID = (string) $_POST["USERS_COLUMN_ID"];
	$USERS_COLUMN_USER = (string) $_POST["USERS_COLUMN_USER"];
	$USERS_COLUMN_EMAIL = (string) $_POST["USERS_COLUMN_EMAIL"];
	$USERS_COLUMN_PASS = (string) $_POST["USERS_COLUMN_PASS"];

	if($_POST["USERS_TABLE_OTHER"])
		$USERS_TABLE_OTHER = (string) $_POST["USERS_TABLE"];

	if($_POST["USERS_COLUMN_SALT"])
		$USERS_COLUMN_SALT = (string) $_POST["USERS_TABLE"];

	$sql = "INSERT INTO support_config (name, value) VALUES(:USERS_TABLE, :USERS_TABLE_V), (:USERS_COLUMN_ID, :USERS_COLUMN_ID_V), (:USERS_COLUMN_USER, :USERS_COLUMN_USER_V), (:USERS_COLUMN_EMAIL, :USERS_COLUMN_EMAIL_V), (:USERS_COLUMN_PASS, :USERS_COLUMN_PASS_V)";
	
	if($USERS_TABLE_OTHER)
		$sql .= ", (:USERS_TABLE_OTHER, :USERS_TABLE_OTHER_V)";

	if($USERS_COLUMN_SALT)
		$sql .= ", (:USERS_COLUMN_SALT, :USERS_COLUMN_SALT_V)";


	$stmt = $db->prepare($sql);

	if($USERS_TABLE_OTHER)
	{
		$stmt->bindValue(':USERS_TABLE_OTHER', "USERS_TABLE_OTHER");
		$stmt->bindValue(':USERS_TABLE_OTHER_V', $USERS_TABLE_OTHER);
	}

	if($USERS_COLUMN_SALT)
	{
		$stmt->bindValue(':USERS_COLUMN_SALT', "USERS_COLUMN_SALT");
		$stmt->bindValue(':USERS_COLUMN_SALT', $USERS_COLUMN_SALT);
	}

	$stmt->bindValue(':USERS_TABLE', "USERS_TABLE");
	$stmt->bindValue(':USERS_TABLE_V', $USERS_TABLE);

	$stmt->bindValue(':USERS_COLUMN_ID', "USERS_COLUMN_ID");
	$stmt->bindValue(':USERS_COLUMN_ID_V', $USERS_COLUMN_ID);

	$stmt->bindValue(':USERS_COLUMN_USER', "USERS_COLUMN_USER");
	$stmt->bindValue(':USERS_COLUMN_USER_V', $USERS_COLUMN_USER);

	$stmt->bindValue(':USERS_COLUMN_EMAIL', "USERS_COLUMN_EMAIL");
	$stmt->bindValue(':USERS_COLUMN_EMAIL_V', $USERS_COLUMN_EMAIL);

	$stmt->bindValue(':USERS_COLUMN_PASS', "USERS_COLUMN_PASS");
	$stmt->bindValue(':USERS_COLUMN_PASS_V', $USERS_COLUMN_PASS);

	$stmt->execute();

	if($stmt)
	{
		header("Location: ?step=5");
		exit();
	}
	else
	{
		header("Location: ?step=4&fail=add_");
		exit();
	}
}

if($_POST["fifth-step"])
{
	define("SUPPORT_SYSTEM", true);
	include "config.php";
	include "admin/functions.php";

	global $db;

	$id = rand(10000, 99999);
	$user = (string) $_POST["user"];
	$pass = (string) $_POST["pass"];
	$email = (string) $_POST["email"];

	$pass = passHash($pass);

	$sql = "INSERT INTO support_agents (support_id, support_name, support_pass, support_email, support_rank) VALUES (:support_id, :support_name, :support_pass, :support_email, :support_rank)";
	$stmt = $db->prepare($sql);

	$stmt->bindValue(':support_id', $id);
	$stmt->bindValue(':support_name', $user);
	$stmt->bindValue(':support_pass', $pass);
	$stmt->bindValue(':support_email', $email);
	$stmt->bindValue(':support_rank', 10);

	$stmt->execute();

    $CAPTCHA_SECRET_KEY = (string) $_POST["CAPTCHA_SECRET_KEY"];
    $CAPTCHA_KEY = (string) $_POST["CAPTCHA_KEY"];
    
    $sql_с = "INSERT INTO support_config (name, value) VALUES(:CAPTCHA_KEY, :CAPTCHA_KEY_V), (:CAPTCHA_SECRET_KEY, :CAPTCHA_SECRET_KEY_V)";

    $stmt_c = $db->prepare($sql_с);
    
    $stmt_c->bindValue(':CAPTCHA_KEY', "CAPTCHA_KEY");
    $stmt_c->bindValue(':CAPTCHA_KEY_V', $CAPTCHA_KEY);
    
    $stmt_c->bindValue(':CAPTCHA_SECRET_KEY', "CAPTCHA_SECRET_KEY");
    $stmt_c->bindValue(':CAPTCHA_SECRET_KEY_V', $CAPTCHA_SECRET_KEY);
    
    $stmt_c->execute();
    
    if($stmt)
	{
		header("Location: ?step=6");
		exit();
	}
	else
	{
		header("Location: ?step=5&fail=add_");
		exit();
	}
}
?>
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8" />
	    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	    <title>Установка</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1"> 
	    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Lato:400,400italic' rel='stylesheet' type='text/css'>
	    <link rel="stylesheet" href="admin/templates/style.min.css">
    </head>
    <body>
    	<div class="main-wrapper">
    	    <section id="container">
    	    	<?php if($_GET["fail"]) : ?>
    	    		<?php if($_GET["fail"] == "config") : ?>
	    				<div class="information-message error">
	    			    	<p>Ошибка! Файл конфигурации отсутствует!</p>
	    				</div>
					<?php endif;?>
	    			<?php if($_GET["fail"] == "db") : ?>
	    				<div class="information-message error">
	    			    	<p>Ошибка! Не удалось создать базу данных!</p>
	    				</div>
	    			<?php endif;?>

	    			<?php if($_GET["fail"] == "db_first") : ?>
	    				<div class="information-message error">
	    			    	<p>Ошибка! Не удалось установить соединение с базой данных!</p>
	    				</div>
	    			<?php endif;?>

	    			<?php if($_GET["fail"] == "add") : ?>
	    				<div class="information-message error">
	    			    	<p>Ошибка! Информация о авторизации не добавлена в базу!</p>
	    				</div>
	    			<?php endif;?>

	    			<?php if($_GET["fail"] == "add_") : ?>
	    				<div class="information-message error">
	    			    	<p>Ошибка! Информация не добавлена в базу!</p>
	    				</div>
	    			<?php endif;?>
	    			<?php if($_GET["fail"] == "config_isset") : ?>
	    				<div class="information-message error">
	    			    	<p>Ошибка! Конфиг уже существует, повторное создание невозможно!</p>
	    				</div>
	    			<?php endif;?>
				<?php endif;?>

	    		<?php if($_GET["step"] == 1 || !isset($_GET["step"]) && !$databases && !$databases_n && !$databases_authorization) : ?>
			    	<form method="POST" id="install-form" action="/install.php">
			    		<table id="install-table">
			    			<p style="text-align:center;margin-bottom:30px;">Введите данные от сервера базы данных, куда вы желаете установить поддержку!</p>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="DB_USER">Имя пользователя</label>
			    				</td>
			    				<td>
			    					<input type="text" name="DB_USER" class="input" value="root">
			    				</td>
			    				<td>
			    					<p class="description text-align-left">Введите имя пользователя базы данных</p>
			    				</td>
			    			</tr>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="DB_HOST">Адрес базы данных</label>
			    				</td>
			    				<td>
			    					<input type="text" name="DB_HOST" class="input" value="localhost">
			    				</td>
			    				<td>
			    					<p class="description text-align-leftt">Введите хост базы данных</p>
			    				</td>
			    			</tr>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="DB_PASS">Пароль пользователя</label>
			    				</td>
			    				<td>
			    					<input type="text" name="DB_PASS" class="input" >
			    				</td>
			    				<td>
			    					<p class="description align-left">Введите пароль базы данных</p>
			    				</td>
			    			</tr>
			    			<tr>
								<td>
									<br>
									<input type="submit" name="first-step" id="first-step" class="btn btn-wide btn-primary mrm" value="Отправить">
								</td>
							</tr>
			    		</table>
			    	</form>
			    <?php endif;?>



				<?php if($databases) : ?>
			    	<form method="POST" id="install-form" action="/install.php">
			    		<table id="install-table">
			    			<p style="text-align:center;margin-bottom:30px;">Выберите базу данных из списка или введите название новой в соответствующем поле для установки поддержки.<br>Если база выбрана из списка и введена в поле, будет создана новая база данных, имя которой вы ввели!</p>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="DB_LIST">База данных</label>
			    				</td>
			    				<td>
			    					<select name="DB_LIST" class="input">
				    					<?php foreach($databases as $value) : ?>
				    						<option><?php echo $value["Database"]?></option>
				    					<?php endforeach;?>
			    					</select>
			    				</td>
			    				<td>
			    					<p class="description align-left">Выберите базу данных</p>
			    				</td>
			    			</tr>

							<tr>
								<td colspan="1"></td>
								<td><p style="text-align:center;margin:30px;">Новая база данных</p></td>
							</tr>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="DB">База данных</label>
			    				</td>
			    				<td>
			    					<input type="text" name="DB" class="input">
			    				</td>
			    				<td>
			    					<p class="description align-left">Введите имя базы данных</p>
			    				</td>
			    			</tr>

			    			<input type="hidden" name="DB_USER" class="input" value="<?php echo $db_user?>">
			    			<input type="hidden" name="DB_HOST" class="input" value="<?php echo $db_host?>">
			    			<input type="hidden" name="DB_PASS" class="input" value="<?php echo $db_pass?>">
							<input type="hidden" name="SWITCH" class="input" value="true">

			    			<tr>
								<td>
									<br>
									<input type="submit" name="first-step" id="first-step" class="btn btn-wide btn-primary mrm" value="Отправить">
								</td>
							</tr>
			    		</table>
			    	</form>
				<?php endif;?>

				<?php if($databases_n) : ?>
			    	<form method="POST" id="install-form" action="/install.php">
			    		<table id="install-table">
			    			<p style="text-align:center;margin-bottom:30px;">Выведите название новой в базы данных.</p>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="DB">База данных</label>
			    				</td>
			    				<td>
			    					<input type="text" name="DB" class="input">
			    				</td>
			    				<td>
			    					<p class="description align-left">Введите имя базы данных</p>
			    				</td>
			    			</tr>

			    			<input type="hidden" name="DB_USER" class="input" value="<?php echo $db_user?>">
			    			<input type="hidden" name="DB_HOST" class="input" value="<?php echo $db_host?>">
			    			<input type="hidden" name="DB_PASS" class="input" value="<?php echo $db_pass?>">
							<input type="hidden" name="SWITCH" class="input" value="true">

			    			<tr>
								<td>
									<br>
									<input type="submit" name="first-step" id="first-step" class="btn btn-wide btn-primary mrm" value="Отправить">
								</td>
							</tr>
			    		</table>
			    	</form>
				<?php endif;?>

	    		<?php if($_GET["step"] == 2) : ?>
			    	<form method="POST" id="install-form" action="/install.php">
			    		<table id="install-table">
			    			<p style="text-align:center;margin-bottom:30px;">Введите ключ лицензии, который вам выдали при покупке</p>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="l_key">Ключ лицензии</label>
			    				</td>
			    				<td>
			    					<input type="text" name="l_key" class="input">
			    				</td>
			    				<td>
			    					<p class="description text-align-left">Введите ключ лицензии</p>
			    				</td>
			    			</tr>
			    			<tr>
								<td>
									<br>
									<input type="submit" name="second-step" id="second-step" class="btn btn-wide btn-primary mrm" value="Отправить">
								</td>
							</tr>
			    		</table>
			    	</form>
			    <?php endif;?>

		        <?php if($_GET["step"] == 3 && !$databases_authorization) : ?>
		        	<form method="POST" id="install-form" action="/install.php">
		        		<table id="install-table">
		        			<p style="text-align:center;margin-bottom:30px;">Авторизация!</p>

		        			<p style="text-align:center;margin-bottom:30px;">Введите данные от сервера базы данных, с которой будет проходить авторизация пользователей(сторонняя)!</p>
		        			<tr>
		        				<td>
		        					<label class="text-align-left" for="DB_USERS_USER">Имя пользователя</label>
		        				</td>
		        				<td>
		        					<input type="text" name="DB_USERS_USER" class="input" value="root">
		        				</td>
		        				<td>
		        					<p class="description text-align-left">Введите имя пользователя базы данных для авторизации пользователей через различные CMS</p>
		        				</td>
		        			</tr>
		        			<tr>
		        				<td>
		        					<label class="text-align-left" for="DB_USERS_HOST">Адрес базы данных</label>
		        				</td>
		        				<td>
		        					<input type="text" name="DB_USERS_HOST" class="input" value="localhost">
		        				</td>
		        				<td>
		        					<p class="description text-align-leftt">Введите хост базы данных для авторизации пользователей через различные CMS</p>
		        				</td>
		        			</tr>
		        			<tr>
		        				<td>
		        					<label class="text-align-left" for="DB_USERS_PASS">Пароль пользователя</label>
		        				</td>
		        				<td>
		        					<input type="text" name="DB_USERS_PASS" class="input" >
		        				</td>
		        				<td>
		        					<p class="description align-left">Введите пароль базы данных для авторизации пользователей через различные CMS</p>
		        				</td>
		        			</tr>
		        			<tr>
		        				<td>
		        					<label class="text-align-left" for="method">Метод авторизации</label>
		        				</td>
		        				<td>
		        					<select class="margin-left-15" name="method">
		        						<option selected value="wordpress">WordPress</option>
		        						<option value="joomla">Joomla</option>
		        						<option value="ipb">IPB</option>
		        						<option value="xenforo">XenForo</option>
		        						<option value="vbulletin">vBulletin</option>
		        						<option value="dle">DLE</option>
		        						<option value="drupal">Drupal</option>
		        						<option value="hash_md5">Md5</option>
		        						<option value="custom">Свой</option>
		        						<option value="none">Отсутствует - анонимные тикеты</option>
		        					</select>
		        				</td>
		        				<td>
		        					<p class="description text-align-left">С какой CMS или форумум вы будете интегрировать аторизацию</p>
		        				</td>
		        			</tr>
		        			
		        			<tr>
		    					<td>
		    						<br>
		    						<input type="submit" name="third-step" id="third-step" class="btn btn-wide btn-primary mrm" value="Отправить">
		    					</td>
		    				</tr>
		        		</table>
		        	</form>
		        <?php endif;?>

				<?php if($databases_authorization) : ?>
			    	<form method="POST" id="install-form" action="/install.php">
			    		<table id="install-table">
			    			<p style="text-align:center;margin-bottom:30px;">Авторизация!</p>
			    			<p style="text-align:center;margin-bottom:30px;">Выберите базу данных из списка!</p>
			    			<tr>
			    				<td>
			    					<label class="text-align-left" for="DB_USERS">База данных</label>
			    				</td>
			    				<td>
			    					<select name="DB_USERS_LIST" class="input">
				    					<?php foreach($databases_authorization as $value) : ?>
				    						<option><?php echo $value["Database"]?></option>
				    					<?php endforeach;?>
			    					</select>
			    				</td>
			    				<td>
			    					<p class="description align-left">Выберите базу данных</p>
			    				</td>
			    			</tr>

			    			<input type="hidden" name="DB_USERS_USER" class="input" value="<?php echo $db_user_u?>">
			    			<input type="hidden" name="DB_USERS_HOST" class="input" value="<?php echo $db_host_u?>">
			    			<input type="hidden" name="DB_USERS_PASS" class="input" value="<?php echo $db_pass_u?>">
			    			<input type="hidden" name="method" class="input" value="<?php echo $method?>">
							<input type="hidden" name="SWITCH" class="input" value="true">

			    			<tr>
								<td>
									<br>
									<input type="submit" name="third-step" id="first-step" class="btn btn-wide btn-primary mrm" value="Отправить">
								</td>
							</tr>
			    		</table>
			    	</form>
				<?php endif;?>

			    <?php if($_GET["step"] == 4) : ?>
			    	<form method="POST" id="install-form" >
			    		<table id="install-table">
			    			<p style="text-align:center;margin-bottom:30px;">Настройка авторизациии!</p>
	                        <?php if($_GET["method"] == "wordpress") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="wp_users">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="id">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="user_login">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="user_email">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="user_pass">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
	                        
	                        <?php if($_GET["method"] == "joomla") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="jos_users">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="id">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="name">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="user_email">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="password">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
	                        
	                        
	                        <?php if($_GET["method"] == "ipb") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="members">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="member_id">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="name">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="user_email">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="members_pass_hash">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_SALT">Соль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_SALT" class="input" value="members_pass_salt">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку c солью</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
	                        
	                        
	                        <?php if($_GET["method"] == "xenforo") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="xf_user">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="user_id">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="username">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE_OTHER">XenForo authenticate</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE_OTHER" class="input" value="xf_user_authenticate">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">XenForo authenticate</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="user_email">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="data">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
	                        
	                        
	                        <?php if($_GET["method"] == "vbulletin") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="bb_user">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="userid">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="username">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="user_email">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="password">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_SALT">Соль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_SALT" class="input" value="salt">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку c солью</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
	                        
	                        <?php if($_GET["method"] == "dle") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="dle_users">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="user_id">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="name">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="user_email">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="password">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
	                        
	                        <?php if($_GET["method"] == "drupal") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="drupal_users">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="uid">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="name">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="user_email">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="pass">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
	                        <?php if($_GET["method"] == "custom" || $_GET["method"] == "hash_md5") : ?>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_TABLE" class="input" value="">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_ID" class="input" value="">
	                                </td>
	                                <td>
	                                    <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_USER" class="input" value="">
	                                </td>
	                                <td>
	                                    <p class="description text-align-leftt">Введите колонку логина пользователя</p>
	                                </td>
	                            </tr>
	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_EMAIL" class="input" value="">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку email'a пользователя</p>
	                                </td>
	                            </tr>

	                            <tr>
	                                <td>
	                                    <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
	                                </td>
	                                <td>
	                                    <input type="text" name="USERS_COLUMN_PASS" class="input" value="">
	                                </td>
	                                <td>
	                                    <p class="description align-left">Введите колонку пароля пользователя</p>
	                                </td>
	                            </tr>
	                        <?php endif;?>
				    			<tr>
									<td>
										<br>
										<input type="submit" name="fourth-step" id="fourth-step" class="btn btn-wide btn-primary mrm" value="Отправить">
									</td>
								</tr>
			    		</table>
			    	</form>
			    <?php endif;?>

		        <?php if($_GET["step"] == 5) : ?>
		        <form method="POST" id="install-form" >
			        <p style="text-align:center;margin-bottom:30px;">Добавление аккаунта администратора и настройка reCAPTCHA!</p>
		        	<table id="install-table">
		        		<tr>
		        			<td>
		        				<label class="text-align-left" for="user">Имя</label>
		        			</td>
		        			<td>
		        				<input type="text" name="user" class="input" value="admin">
		        			</td>
		        			<td>
		        				<p class="description text-align-left">Введите имя администратора</p>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>
		        				<label class="text-align-left" for="pass">Пароль</label>
		        			</td>
		        			<td>
		        				<input type="text" name="pass" class="input">
		        			</td>
		        			<td>
		        				<p class="description text-align-left">Введите пароль администратора</p>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td>
		        				<label class="text-align-left" for="email">Email</label>
		        			</td>
		        			<td>
		        				<input type="text" name="email" class="input" value="">
		        			</td>
		        			<td>
		        				<p class="description text-align-left">Введите email администратора</p>
		        			</td>
		        		</tr>
                        <tr>
		        			<td>
		        				<label class="text-align-left" for="CAPTCHA_KEY">Ключ reCaptcha</label>
		        			</td>
		        			<td>
		        				<input type="text" name="CAPTCHA_KEY" class="input" value="">
		        			</td>
		        			<td>
		        				<p class="description text-align-left">Его можно посмотреть в администраторской reCaptcha</p>
		        			</td>
		        		</tr>
                        <tr>
		        			<td>
		        				<label class="text-align-left" for="CAPTCHA_SECRET_KEY">Секретный ключ reCaptcha</label>
		        			</td>
		        			<td>
		        				<input type="text" name="CAPTCHA_SECRET_KEY" class="input" value="">
		        			</td>
		        			<td>
		        				<p class="description text-align-left">Его можно посмотреть в администраторской reCaptcha</p>
		        			</td>
		        		</tr>
		        		<tr>
		    				<td>
		    					<br>
		    					<input type="submit" name="fifth-step" id="fourth-step" class="btn btn-wide btn-primary mrm" value="Создать">
		    				</td>
		    			</tr>
		        	</table>
		        </form>
		        <?php endif;?>

		        <?php if($_GET["step"] == 6) : ?>
		        	<div class="information-message success">
		        	    <p>Вы успешно завершили установку поддержки!</p>	 	    
		        	</div>

		        	<a href="/" class="btn btn-wide btn-primary mrm block text-align-center">Главная</a>
		    	  	<?php unlink("install.php");?>		
		    	<?php endif;?>
    	    </section>
    	</div>
    </body>
</html>