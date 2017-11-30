<?php
defined('SUPPORT_SYSTEM') or die('Access denied');

/**
 * Авторизирован ли support
 */

function is_support_login()
{
	if($_SESSION['support']['key'])
		$key = $_SESSION['support']['key'];

	$last_key = $_COOKIE['key'];

	//Проверяем, есть ли глобальный ключ
	if(isset($key) && !empty($key))
	{
		global $db;
		//if(!empty($last_key))
	    	//$sql = "SELECT support_key, support_remember FROM support_agents WHERE support_last_key = '$last_key'";
	    //else
	   	$sql = "SELECT support_key, support_remember FROM support_agents WHERE support_key = :key";

	    $stmt = $db->prepare($sql);

		$stmt->bindValue(':key', $key);
	    $stmt->execute();

	    //Получаем данные из бд
	    while($row = $stmt->fetch())
    	{
    		$db_key = $row['support_key'];
    		$remember = $row['support_remember'];
    	}

    	if($remember == "0" && $key != $db_key)
    		return false;

        if(!empty($remember) && $remember == "1" && $key == $db_key)
            return true;

        if(!empty($remember) && $remember == "1" && $key != $db_key)
            return false;    

        if($remember == "0" && $key == $db_key)
            return true;

	}//if_key
	else
		//Пользователь на авторизирован
		return false;
}

/**
 * Авторизация
 */

function authorization($name, $pass, $remember = "0")
{
	$ip = getenv("HTTP_X_FORWARDED_FOR");

	if(empty($ip) || $ip =='unknown') 
	    $ip = getenv("REMOTE_ADDR");

	$pass = passHash($pass);

	global $db, $coreLog;

	$errors_c = getErrorsCount($ip);

	if($errors_c >= 3)
	{
	    header("Location: ?status=banned");
	    exit();
	}

    $sql = "SELECT * FROM support_agents WHERE support_name = :name LIMIT 1";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':name', $name);
    $stmt->execute();

    //Получаем данные из бд
    while($row = $stmt->fetch())
	{
		$sup_name = $row['support_name'];
		$sup_pass = $row['support_pass'];
		$sup_rank = $row['support_rank'];
    	$sup_id = $row['support_id'];
        $sup_remember = $row['support_remember'];
        $sup_key = $row['support_key'];
        $sup_email = $row['support_email'];
	}

	if(!empty($name) && !empty($name) && $name == $sup_name && $pass == $sup_pass)
	{

		//Создаем ключ авторизации
		$key = uniqid($name);
		$key = md5($key . SALT);

		$sql = "UPDATE support_agents SET support_last_key = :last_key, support_key = :key, support_remember = :remember WHERE support_name = :name";
	    $stmt = $db->prepare($sql);

		$stmt->bindValue(':key', $key);
		$stmt->bindValue(':last_key', $sup_key);
		$stmt->bindValue(':remember', $remember);
		$stmt->bindValue(':name', $name);
	    $stmt->execute();
	
		$_SESSION['support']['name'] = $sup_name;
    	$_SESSION['support']['id'] = $sup_id;
    	$_SESSION['support']['rank'] = $sup_rank;
        $_SESSION['support']['remember'] = $sup_remember;
		$_SESSION['support']['key'] = $key;
		$_SESSION['support']['email'] = $sup_email;

		if($remember)
			setcookie("key", $key, time() + 2*7*24*60*60);

		return true;
	}
	else
	{
		$ip_db = selectIp($ip);

		if($ip == $ip_db) 
		{
		    $errors_c = getErrorsCount($ip);
		    
		    $count = $errors_c + 1; 

		    updateErrorsCount($ip, $count);
		}          
		else 
		    insertIpToErrorTable($ip);
		
		$errors_c = getErrorsCount($ip);

		if($errors_c >= 3) 
		{
		    $coreLog->write("$name забанен на 15 минут.");

		    header("Location: ?status=banned");
		    exit();
		}
		else
			return false;
	}	
	
	return false;
}

/**
 * Активируем тему
 */

function activeTheme($theme_name)
{
	global $db;

    $sql = "UPDATE support_config SET value = :theme_name WHERE name = :template";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':template', "ACTIVE_TEMPLATE");
    $stmt->bindValue(':theme_name', CONTENT . $theme_name . "/");
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Активируем плагин
 */

function activePlugin($plugin_name)
{
	global $db;

	$sql_p = "SELECT plugin_name FROM support_plugins";
	$stmt_p = $db->prepare($sql_p);

    $stmt_p->execute();
    while ($row = $stmt_p->fetch()) 
    {
    	if($plugin_name == $row['plugin_name'])
    		return;
    }

    $sql = "INSERT INTO support_plugins (plugin_name, plugin_path) VALUES (:plugin_name, :plugin_path)";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':plugin_name', $plugin_name);
    $stmt->bindValue(':plugin_path', PLUGINS . $plugin_name . "/");
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Деактивируем плагин
 */

function deActivePlugin($plugin_name)
{
	global $db;

    $sql = "DELETE FROM support_plugins WHERE plugin_name = :plugin_name LIMIT 1";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':plugin_name', $plugin_name);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Добавление support'a
 */

function addSupport($support_id, $support_name, $support_rank, $support_email, $support_pass)
{
    global $db;

    $sql = "INSERT INTO support_agents (support_id, support_name, support_pass, support_email, support_rank) VALUES(:support_id, :support_name, :support_pass, :support_email, :support_rank)";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':support_id', $support_id);
    $stmt->bindValue(':support_name', $support_name);
    $stmt->bindValue(':support_pass', $support_pass);
    $stmt->bindValue(':support_email', $support_email);
    $stmt->bindValue(':support_rank', $support_rank);
    
    $stmt->execute();
    
    if($stmt)
    	return true;
    else
    	return false;
}

/*
 * Выводим сообщения для агентов тех поддержки
 */

function getTicketsToSupport($support_id, $filter = false, $start = 0)
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());

    global $db;

    $amount = TICKETS_AMOUNT;

    $sql = null;

    switch ($filter)
    {
        case 'waiting':
            $sql = "SELECT * FROM support_user_tickets WHERE support_id = :support_id AND close = 0 ORDER BY time DESC LIMIT $start, $amount";
        break;

        case 'open':
            $sql = "SELECT * FROM support_user_tickets WHERE support_id = :support_id AND close = 2 ORDER BY time DESC LIMIT $start, $amount";
        break;

        case 'close':
            $sql = "SELECT * FROM support_user_tickets WHERE support_id = :support_id AND close = 1 ORDER BY time DESC LIMIT $start, $amount";
        break;
        
        default:
            $sql = "SELECT * FROM support_user_tickets WHERE support_id = :support_id ORDER BY close, time DESC LIMIT $start, $amount";
        break;
    }

    //$sql = "SELECT * FROM support_user_tickets WHERE support_id = :support_id ORDER BY close, time DESC LIMIT $start, $amount";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':support_id', $support_id);

    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Все тикеты для чата
 */

function getAllOpenTicketsToSupport($support_id)
{
    global $db;

    $sql = "SELECT ticket_id FROM support_user_tickets WHERE support_id = :support_id AND close = 0 OR close = 2";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    $row = $stmt->fetchAll();

    return $row;
}


/**
 * Получаем количество тикетов
 */

function countPosts($support_id, $filter = false)
{
	global $db;

    $amount = TICKETS_AMOUNT;

    switch ($filter)
    {
        case 'waiting':
            $sql = "SELECT COUNT(*) FROM support_user_tickets WHERE support_id = :support_id AND close = 0";
        break;

        case 'open':
            $sql = "SELECT COUNT(*) FROM support_user_tickets WHERE support_id = :support_id AND close = 2";
        break;

        case 'close':
            $sql = "SELECT COUNT(*) FROM support_user_tickets WHERE support_id = :support_id AND close = 1";
        break;
            
        case 'all':
            $sql = "SELECT COUNT(*) FROM support_user_tickets WHERE support_id = :support_id";
        break;

        default:
            $sql = "SELECT COUNT(*) FROM support_user_tickets WHERE support_id = :support_id";
        break;
    }

    //$sql = "SELECT COUNT(*) FROM support_user_tickets WHERE support_id = :support_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':support_id', $support_id);

    $stmt->execute();

    return $stmt->fetch();
}

/*
 * Проверяем, есть ли тикеты у support'a
 */

function have_support_tickets($support_id)
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());

    global $db;

    $sql = "SELECT ticket_id FROM support_user_tickets WHERE support_id = :support_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    $row = $stmt->fetch();

    if($row)
    	return true;
    else
    	return false;
}

/** 
 * Получаем статус тикета в админке 
 */

function ticket_status_admin($ticket_id, $text = false, $return = false)
{
    global $db;

    $sql = "SELECT DISTINCT close FROM support_user_tickets WHERE ticket_id = :ticket_id LIMIT 1";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();
    
    if(!$return)
        while($row = $stmt->fetch())
        {
            if($text)
                switch ($row["close"])
                {
                    case 0:
                        echo "Ожидает ответа";
                    break;

                    case 1:
                        echo "Закрыт";
                    break;
                    
                    case 2:
                        echo "Открыт";
                    break;
                    
                    default:
                        echo "Открыт";
                    break;
                }
            else
                switch ($row["close"])
                {
                    case 0:
                        echo "statement-waiting";
                    break;

                    case 1:
                        echo "statement-close";
                    break;
                    
                    case 2:
                        echo "statement-open";
                    break;
                    
                    default:
                        echo "statement-open";
                    break;
                }

        }  
    else
        if($text)
            switch ($row["close"])
            {
                case 0:
                    return "Ожидает ответа";
                break;

                case 1:
                    return "Закрыт";
                break;
                
                case 2:
                    return "Открыт";
                break;
                
                default:
                    return "Открыт";
                break;
            }
        else
            switch ($row["close"])
            {
                case 0:
                    return "statement-waiting";
                break;

                case 1:
                    return "statement-close";
                break;
                
                case 2:
                    return "statement-open";
                break;
                
                default:
                    return "statement-open";
                break;
            }


}

/**
 * Выход их админки
 */

function logout($support_id, $key)
{
	global $db;

    $sql = "UPDATE support_agents SET support_last_key =:last_key, support_key = :key, support_remember = 0 WHERE support_id = :id";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':key', "");
	$stmt->bindValue(':last_key', $key);
    $stmt->bindValue(':id', $support_id);
    $stmt->execute();

    unset($_SESSION["support"]);
    setcookie("key", '', time() - 1);

	session_destroy();
}

/**
 * Получаем максимальный ID сообщения пользователя в тикете
 */

function getMaxIdMessageInTicket($ticket_id)
{
	global $db;

    $sql = "SELECT MAX(message_id) FROM support_user_messages WHERE ticket_id = :ticket_id GROUP BY ticket_id";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    while ($row = $stmt->fetch())
    {
    	$message_id = $row['MAX(message_id)'];
    }

    return $message_id;
}

/**
 * Если есть тикет без овтета выводим оповещение в боковом меню
 */

function issetNewTicketsToAnswer($support_id)
{
	global $db;

	$sql = "SELECT close FROM support_user_tickets WHERE support_id = :support_id and close = :close";
    $stmt = $db->prepare($sql);

	//$stmt->bindValue(':ticket_id', $ticket_id);
	$stmt->bindValue(':support_id', $support_id);
	$stmt->bindValue(':close', 0);
    $stmt->execute();

    $row = $stmt->fetchAll();
    
    if(empty($row))
    	return false;
    else
    	return true;
}


/*
 * Добавление сообщения агента тех поддержки
 */

function addSupportMessage($ticket_id, $support_id, $support_message, $user_message_id, $message_time)
{
    global $db;

    $sql = "INSERT INTO support_messages (ticket_id, support_id, support_message, user_message_id, message_time) VALUES(:ticket_id, :support_id, :support_message, :user_message_id, :message_time)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->bindValue(':support_id', $support_id);
    $stmt->bindValue(':support_message', $support_message);
    $stmt->bindValue(':user_message_id', $user_message_id);
    $stmt->bindValue(':message_time', $message_time);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}


/**
 * Ищем дубликат сообщения в заявке
 */

function findSupportDublicateMessage($ticket_id, $support_id, $message, $user_message_id)
{
	global $db;

	$sql = "SELECT support_message FROM support_messages WHERE user_message_id = :user_message_id AND support_id = :support_id";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':user_message_id', $user_message_id);
	$stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    while ($row = $stmt->fetch())
    {
    	$support_message = $row['support_message'];
    }

    if($support_message == $message)
    	return true;
    else
    	return false;
}


/**
 * Удаляем сообщение support'a
 */

function deleteMessage($message_id, $written_by)
{
	global $db;

	if($written_by == "support-message")
		$sql = "DELETE FROM support_messages WHERE message_id = :message_id LIMIT 1";
    else
    	$sql = "DELETE FROM support_user_messages WHERE message_id = :message_id LIMIT 1";

    $stmt = $db->prepare($sql);

	$stmt->bindValue(':message_id', $message_id);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Удаляем support'a
 */

function deleteSupport($support_id)
{
	global $db;

	$sql = "DELETE FROM support_agents WHERE support_id = :support_id LIMIT 1";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Получаем support_rank по support_id 
 */

function support_rank($support_id) 
{
	global $db;

	$sql = "SELECT support_rank FROM support_agents WHERE support_id = :support_id";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    while ($row = $stmt->fetch())
    {
    	$support_rank = $row['support_rank'];
    }

    return $support_rank;
}

/**
 * UPDATE запрос в настройках
 */
function updateSettings($name, $value)
{
	global $db;

    $sql = "UPDATE support_config SET value = :value WHERE name = :name";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':value', $value);
    $stmt->bindValue(':name', $name);

    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * INSERT запрос в настройках
 */

function insertSettings($name, $value)
{
	global $db;

    $sql = "INSERT INTO support_config(name, value) VALUES(:name, :value)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':value', $value);
    $stmt->bindValue(':name', $name);

    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Добавление категории в админке
 */
function addCategory($name)
{
	global $db;

    $sql = "INSERT INTO support_categories(name) VALUES (:name)";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':name', $name);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Удаление категории в админке
 */

function deleteCategory($name)
{
	global $db;

    $sql = "DELETE FROM support_categories WHERE name = :name LIMIT 1";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':name', $name);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Добавление ответа на частый вопрос в админке
 */

function addAnswer($category_id, $question, $support_answer)
{
	global $db;

    $sql = "INSERT INTO support_answers(category_id, question, support_answer) VALUES (:category_id, :question, :support_answer)";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':category_id', $category_id);
	$stmt->bindValue(':question', $question);
	$stmt->bindValue(':support_answer', $support_answer);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Удаление ответа на частый вопрос в админке
 */

function deleteAnswer($id)
{
	global $db;

    $sql = "DELETE FROM support_answers WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':id', $id);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * При добавлении support'a проверяем на наличие таких-же данных
 */

function findDublicateId($support_id, $ifYou = false)
{
	global $db;

	$sql = "SELECT support_id FROM support_agents WHERE support_id = :id";

	$stmt = $db->prepare($sql);

	$stmt->bindValue(':id', $support_id);
	$stmt->execute();
	
	$row = $stmt->fetchAll();

	if($ifYou)
	{
		if($row["support_id"] == $support_id)
			return false;
	}
	else
		if(empty($row))
			return false;
		else
			return true;
}

function findDublicateEmail($support_email, $ifYou = false)
{
	global $db;

	$sql = "SELECT support_email FROM support_agents WHERE support_email = :email";

	$stmt = $db->prepare($sql);

	$stmt->bindValue(':email', $support_email);
	$stmt->execute();
	
	$row = $stmt->fetchAll();
	
	if($ifYou)
	{
		if($row["support_email"] == $support_email)
			return false;
	}
	else
		if(empty($row))
			return false;
		else
			return true;
}

function findDublicateName($support_name, $ifYou = false)
{
	global $db;

	$sql = "SELECT support_name FROM support_agents WHERE support_name = :name";

	$stmt = $db->prepare($sql);

	$stmt->bindValue(':name', $support_name);
	$stmt->execute();
	
	$row = $stmt->fetchAll();

	if($ifYou)
	{
		if($row["support_name"] == $support_name = false)
			return false;
	}
	else
		if(empty($row))
			return false;
		else
			return true;
}

/**
 * Получаем всех support'ов
 */

function getUsers()
{
	global $db;

    $sql = "SELECT support_id, support_name, support_rank FROM support_agents";
    $stmt = $db->prepare($sql);

    $stmt->execute();

    $row = $stmt->fetchAll();
    
    return $row;
}

/**
 * Обновляем текущую версию до последней после обновления
 */

function updateVersion($version)
{
	global $db;

    $sql = "UPDATE support_config SET value = :value WHERE name = :name";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':value', $version);
	$stmt->bindValue(':name', "CURRENT_VERSION");
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/**
 * Проверка на бан support'a
 */

function isSupportBanned($support_id = "", $support_name = "")
{
    global $db;

    if($support_name)
        $sql = "SELECT banned FROM support_agents WHERE support_name = :support_name";
    else
        $sql = "SELECT banned FROM support_agents WHERE support_id = :support_id";

    $stmt = $db->prepare($sql);

    if($support_name)
        $stmt->bindValue(':support_name', $support_name);

    if($support_id)
        $stmt->bindValue(':support_id', $support_id);

    $stmt->execute();

    $row = $stmt->fetch();

    if($row["banned"])
        return true;
    else
        return false;
}

/**
 * Баним support'a
 */

function ban($support_id)
{
    global $db;

    $sql = "UPDATE support_agents SET banned = :banned WHERE support_id = :support_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':banned', true);
    $stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    if($stmt)
        return true;
    else
        return false;
}

/**
 * Разбаниваем support'a
 */

function unban($support_id)
{
    global $db;

    $sql = "UPDATE support_agents SET banned = :banned WHERE support_id = :support_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':banned', false);
    $stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    if($stmt)
        return true;
    else
        return false;
}

/**
 * Получаем значение текущих настроек 
 * @var name - имя настройки
 * @var echo - вывести или вернуть значение 
 */

function getSettings($name = NULL, $isEcho = true)
{
    global $db;

    if($name)
    {
        $sql = "SELECT value FROM support_config WHERE name = :name";
        
    }
    else
        $sql = "SELECT * FROM support_config";

    
    $stmt = $db->prepare($sql);
    
    if($name)
        $stmt->bindValue(':name', $name);
    
    $stmt->execute();

    if($name)
        while ($row = $stmt->fetch())
        {
            if($isEcho)
                echo $row["value"];
            else
                return $row["value"]; 
        }
    else
        return $stmt->fetchAll();
}

?>