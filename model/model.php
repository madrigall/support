<?php
defined('SUPPORT_SYSTEM') or die('Access denied');

function is_user_login()
{
    if($_SESSION['user']['key'])
        $key = $_SESSION['user']['key'];

    $last_key = $_COOKIE['user']['key'];
    //Проверяем, есть ли глобальный ключ

    if(isset($key) && !empty($key))
    {
        global $db;

        $sql = "SELECT user_key, user_remember FROM support_user_data WHERE user_key = :key";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':key', $key);
        $stmt->execute();

        //Получаем данные из бд
        while($row = $stmt->fetch())
        {
            $db_key = $row['user_key'];
            $remember = $row['user_remember'];
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

function user_anonym_authorization($anonym)
{
    deleteBannedIps();

    if(ANONYM_USERS && $anonym == true)
    {
        $_SESSION["user"] = "anonymous";
        $_SESSION["id"] = uniqid();
        $_SESSION["email"] = "anonymous@supportsystem";
        $_SESSION["rememberMe"] = true;

        setcookie("Remember", true);

        return true;
    }

    return false;
}

function user_authorization($user, $pass, $remember = false)
{

    global $db, $db_users, $coreLog;

    // Получаем IP
    $ip = getenv("HTTP_X_FORWARDED_FOR");

    if(empty($ip) || $ip =='unknown') 
        $ip = getenv("REMOTE_ADDR");

    deleteBannedIps();

    $errors_c = getErrorsCount($ip);

    if($errors_c >= 3)
    {
        header("Location: ?action=login&auth_fail=1");
        exit();
    }
    
    $sql = "SELECT " . USERS_COLUMN_ID . "," . USERS_COLUMN_EMAIL . "," . USERS_COLUMN_USER . "," . USERS_COLUMN_PASS . " FROM " . USERS_TABLE . " WHERE " .  USERS_COLUMN_USER . " = :user";
    
    if(HASH_CRYPT == "ipb" || HASH_CRYPT == "vbulletin")
        $sql = "SELECT " . USERS_COLUMN_ID . "," . USERS_COLUMN_EMAIL . "," . USERS_COLUMN_USER . "," . USERS_COLUMN_PASS . "," . USERS_COLUMN_SALT . " FROM " . USERS_TABLE . " WHERE " .  USERS_COLUMN_USER . " = :user";

    if(HASH_CRYPT == "xenforo")
        $sql = "SELECT " . USERS_TABLE.USERS_COLUMN_ID . ", " . USERS_TABLE.USERS_COLUMN_USER . ", " . USERS_TABLE_OTHER.USERS_COLUMN_ID . "," . USERS_TABLE_OTHER.USERS_COLUMN_PASS . " FROM " . USERS_TABLE . ", " . USERS_TABLE_OTHER . " WHERE " . USERS_TABLE.USERS_COLUMN_ID . " = :other AND " . USERS_TABLE.USERS_COLUMN_USER ."= :user";

    $stmt = $db_users->prepare($sql);

    if(HASH_CRYPT == "xenforo")
        $stmt->bindValue(':other', USERS_TABLE_OTHER.USERS_COLUMN_ID);

    $stmt->bindValue(':user', $user);
    $stmt->execute();

    while($row = $stmt->fetch())
    {   
        if(HASH_CRYPT == "xenforo")
        {
            $user_real_pass = substr($row[USERS_COLUMN_PASS],22,64);
            $salt = substr($row[USERS_COLUMN_SALT],105,64);
        }
        else
            $user_real_pass = $row[USERS_COLUMN_PASS];    

        if(HASH_CRYPT == "ipb" || HASH_CRYPT == "vbulletin")
            $salt = $row[USERS_COLUMN_SALT];

        $crypt = HASH_CRYPT;

        switch ($crypt)
        {
            case 'hash_md5':
                $checkPass = $crypt($pass);
            break;

            case 'dle':
                $checkPass = $crypt($pass);
            break;

            case 'joomla':
                $checkPass = $crypt($user_real_pass, $pass);
            break;

            case 'ipb':
                $checkPass = $crypt($pass, $salt);
            break;

            case 'xenforo':
                $checkPass = $crypt($pass, $salt);
            break;

            case 'wordpress':
                $checkPass = $crypt($user_real_pass, $pass);
            break;

            case 'vbulletin':
                $checkPass = $crypt($pass, $salt);
            break;

            case 'drupal':
                $checkPass = $crypt($user_real_pass, $pass);
            break;

            case 'custom':
                $checkPass = $crypt($user_real_pass, $pass);
            break;
            
            default:
                $coreLog->write("Метод авторизации отсутствует.");

                header("Location: ?action=login&auth_fail=2");
                exit();
            break;
        }

        if($user_real_pass == $checkPass)
        {
        
            $_SESSION["user"] = $user;
            $_SESSION["id"] = $row[USERS_COLUMN_ID];
            $_SESSION["email"] = $row[USERS_COLUMN_EMAIL];
            $_SESSION["rememberMe"] = $remember;

            setcookie("Remember", $remember);
                
            return true;
        }
        else
        {
            //Если ошибется 3 раза - в бан на 15 мин

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
                $coreLog->write("$user забанен на 15 минут.");

                header("Location: ?action=login&auth_fail=1");
                exit();
            }
            else
            {
                header("Location: ?action=login&auth_fail=3");
                exit();
            }

            return false;
        }
    }     
}

function user_tickets($user)
{

	/*
	 * Подключение к БД для получения тикетов пользователя 
	 */

    //PDO объект
    global $db;

    $sql = "SELECT * FROM support_user_tickets WHERE user = :usermane ORDER BY time DESC";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':usermane', $user);
    $stmt->execute();

    //$stmt->execute(array(':username' => $usrnm, ':password' => $psswrd));

    $row = $stmt->fetchAll();
    return $row;
   
}

/*
 * End printUserTickets
 */

function ticket_status($ticket_id, $return = false)
{
    global $db;

    $sql = "SELECT DISTINCT close FROM support_user_tickets WHERE ticket_id = :ticket_id LIMIT 1";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    if(!$return)
        while($row = $stmt->fetch())
        {
            switch ($row["close"])
            {
                case 2:
                    echo "<span class='statement-status-open'>Открыт</span>";
                break;

                case 1:
                    echo "<span class='statement-status-close'>Закрыт</span>";
                break;
                
                case 0:
                    echo "<span class='statement-status-waiting'>Ожидание ответа</span>";
                break;
                
                default:
                    echo "<span class='statement-status-open'>Открыт</span>";
                break;
            }

        }  
    else
        return $stmt->fetch();
}

function first_message_from_ticket($ticket_id, $return = false)
{
    global $db;

    $sql = "SELECT user_message FROM `support_user_messages` WHERE `ticket_id` = :ticket_id AND `message_id` = (SELECT MIN(`message_id`) FROM `support_user_messages` WHERE `ticket_id` = :ticket_id)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();
    $row_t = "";


    while($row = $stmt->fetch())
    {
        $row_t = $row["user_message"];
        if(!empty($row["user_message"]) && $row["user_message"] != NULL)
        {
            /*
            $array = str_word_count($row["user_message"], 2); 

            $lenght= 600; 
            $result = ''; 

            foreach ($array as $key => $value ) 
            { 
               if($key >= $lenght) 
               { 
                   $result .= "...";
                   break;
               } 

               $result .= $value . ' '; 
            } 

            echo $result; 
            */
            if(!$return)
            {
                echo substr($row["user_message"], 0, strrpos(substr($row["user_message"],0, 600), ' '));
                echo "...";
            }
            else
            {
                return substr($row["user_message"], 0, strrpos(substr($row["user_message"],0, 600), ' ')) . "...";
            }
        }
        else
            if(!$return)
                echo "Сообщений нет!";
            else
                return "Сообщений нет!";
    }

    if(empty($row_t))
        if(!$return)
            echo "Сообщений нет!";
        else
            return "Сообщений нет!";
    

}

function have_messages($ticket_id)
{
    global $db;

    $sql = "SELECT user_message FROM support_user_messages WHERE ticket_id = :ticket_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    $row = $stmt->fetchAll();

    if($row)
        return true;
    else
        return false;
}

function user_messages($ticket_id)
{
    global $db;

    $sql = "SELECT * FROM support_user_messages WHERE ticket_id = :ticket_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    /*
     * Получаем данные о сообщениях в тикете
     */

    $row = $stmt->fetchAll();

    return $row;
}

function support_messages($message_id)
{
    global $db;

    $sql = "SELECT * FROM support_messages WHERE user_message_id = :user_message_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':user_message_id', $message_id);
    $stmt->execute();

    $row = $stmt->fetchAll();

    return $row;

}

/*
 * Проверка на существования тикетов у пользователя
 */

function have_user_tickets($user)
{
    global $db;

    $sql = "SELECT ticket_id FROM support_user_tickets WHERE user = :usermane";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':usermane', $user);
    $stmt->execute();

    $row = $stmt->fetch();

    if($row)
        return true;
    else
        return false;
}

/*
 * End have_user_tickets
 */

function ticket_open($ticket_id)
{
    global $db;

    $sql = "SELECT close FROM support_user_tickets WHERE ticket_id = :ticket_id LIMIT 1";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    $row = $stmt->fetch();

    if($row["close"] == 0 || $row["close"] == 2)
        return true;
    else
        return false;
}

function closeTicket($ticket_id)
{
    global $db;

    $sql = "UPDATE `support_user_tickets` SET `close`= 1 WHERE `ticket_id` = :ticket_id";

    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    if($stmt)
        return true;
    else
        return false;
}

function search($question)
{
    global $db;

    $sql = "SELECT * FROM support_answers WHERE MATCH(question) AGAINST(:question'*' IN BOOLEAN MODE)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':question', $question);
    $stmt->execute();

    return $stmt->fetchAll();

}

function isBanned($user, $ip)
{
    /*global $db;

    $date = new DateTime("NOW"); 

    if($user)
        $sql = "SELECT * FROM support_banned WHERE user = :user";

    if($user && $ip)
        $sql = "SELECT * FROM support_banned WHERE user = :user OR ip = :ip";
    else
        $sql = "SELECT * FROM support_banned WHERE ip = :ip";
    
    $stmt = $db->prepare($sql);
    
    if($user)
        $stmt->bindValue(':user', $ip);
    
    $stmt->bindValue(':ip', $ip);
    $stmt->execute();

    return $stmt->fetchAll();*/
    return false;
}

function selectIp($ip)
{
    global $db;

    $sql = "SELECT " . ERROR_IP_COLUMN . " FROM " . ERROR_LOG_TABLE . " WHERE " .  ERROR_IP_COLUMN . " = :ip";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':ip', $ip);
    $stmt->execute();

    while($row = $stmt->fetch())
        $your_ip = $row[ERROR_IP_COLUMN];

    return $your_ip;
}

function updateErrorsCount($ip, $count)
{
    global $db;

    $sql = "UPDATE " . ERROR_LOG_TABLE . " SET " . ERROR_NUM_COLUMN . " = :count, ". ERROR_DATE_COLUMN ." = NOW() WHERE " .  ERROR_IP_COLUMN . " = :ip";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':count', $count);
    $stmt->bindValue(':ip', $ip);
    $stmt->execute();

    if($stmt)
        return true;
    else
        return false;
}

function getErrorsCount($ip)
{
    global $db;

    $sql = "SELECT " . ERROR_NUM_COLUMN . " FROM " . ERROR_LOG_TABLE . " WHERE " . ERROR_IP_COLUMN . " = :ip";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':ip', $ip);
    $stmt->execute();

    while($row = $stmt->fetch())
        $error_c = $row[ERROR_NUM_COLUMN];

    return $error_c;
}

function deleteBannedIps()
{
    global $db;

    $sql = "DELETE FROM " . ERROR_LOG_TABLE . " WHERE UNIX_TIMESTAMP() - UNIX_TIMESTAMP(" . ERROR_DATE_COLUMN . ") > 900";
    
    $stmt = $db->prepare($sql);

    $stmt->execute();

    if($stmt)
        return true;
    else
        return false;
}

function insertIpToErrorTable($ip)
{
    global $db;

    $sql = "INSERT INTO " . ERROR_LOG_TABLE . "(" . ERROR_IP_COLUMN . ", " . ERROR_DATE_COLUMN . ", " . ERROR_NUM_COLUMN . ") VALUES (:ip, NOW(), 1)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ip', $ip);
    $stmt->execute();

    if($stmt)
        return true;
    else
        return false;
}

function support_description($echo = true, $text = null)
{
    if($text)
        if($echo)
            echo $text;
        else
            return $text;
    else
    {
        global $db;

        $sql = "SELECT value FROM support_config WHERE name = :name LIMIT 1";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', "DESCRIPTION");
        $stmt->execute();

        $description = $stmt->fetch();

        if($echo)
            echo $description["value"];
        else
            return $description["value"];
    }
}

function support_keywords($echo = true, $text = null)
{
    if($text)
        if($echo)
            echo $text;
        else
            return $text;
    else
    {
        global $db;

        $sql = "SELECT value FROM support_config WHERE name = :name LIMIT 1";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', "KEYWORDS");
        $stmt->execute();

        $keywords = $stmt->fetch();

        if($echo)
            echo $keywords["value"];
        else
            return $keywords["value"];
    }
}

function support_categories($category_id = false)
{
    global $db;

    if($category_id)
        $sql = "SELECT * FROM support_categories WHERE id = :id";
    else
        $sql = "SELECT * FROM support_categories";

    $stmt = $db->prepare($sql);

    if($category_id)
        $stmt->bindValue(':id', $category_id);

    $stmt->execute();

    $row = $stmt->fetchAll();

    return $row;
}



function support_answers($category_id = NULL)
{
    global $db;

    if(!isset($category_id))
        $sql = "SELECT * FROM support_answers";
    else
        $sql = "SELECT * FROM support_answers WHERE category_id = :category_id";
    

    $stmt = $db->prepare($sql);

    if($category_id)
        $stmt->bindValue(':category_id', $category_id);

    $stmt->execute();

    $row = $stmt->fetchAll();

    return $row;
}

function has_questions($category_id = null)
{
    global $db;

    if($category_id)
        $sql = "SELECT question FROM support_answers WHERE category_id = :category_id";
    else
        $sql = "SELECT question FROM support_answers";

    $stmt = $db->prepare($sql);

    if($category_id)
        $stmt->bindValue(':category_id', $category_id);

    $stmt->execute();

    $row = $stmt->fetch();

    if($row)
        return true;
    else
        return false;
}

function get_support($support_id)
{
	global $db;

	$sql = "SELECT * FROM support_agents WHERE support_id = :support_id";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    return $stmt->fetch();
}

function support_title($echo = true, $text = null)
{
    if($text)
        if($echo)
            echo $text;
        else
            return $text;

    else
    {
        global $db;

        $sql = "SELECT value FROM support_config WHERE name = :name LIMIT 1";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', "TITLE");
        $stmt->execute();

        $title = $stmt->fetch();
        $second = parsePages();

        if(empty($second)) 
            $title_string = $title["value"];
        else
            $title_string = $title["value"] . " | " . parsePages();


        if($echo)
            echo $title_string;
        else
            return $title_string;
    }

}

/*
 * Добавление сообщения 
 */

function addUserMessage($ticket_id, $user, $user_email, $user_message, $message_time)
{
    global $db;

    $sql = "INSERT INTO support_user_messages (ticket_id, user, user_email, user_message, message_time) VALUES(:ticket_id, :user, :user_email, :user_message, :message_time)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->bindValue(':user', $user);
    $stmt->bindValue(':user_email', $user_email);
    $stmt->bindValue(':user_message', $user_message);
    $stmt->bindValue(':message_time', $message_time);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

/*
 * End addSupportMessage
 */

function findUserDublicateMessage($ticket_id, $user, $message)
{
	global $db;

	$sql = "SELECT user_message FROM support_user_messages WHERE user = :user AND ticket_id = :ticket_id";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':user', $user);
	$stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    while ($row = $stmt->fetch())
    {
    	$user_message = $row['user_message'];
    }

    if($user_message == $message)
    	return true;
    else
    	return false;
}

function findDublicateTicket($user, $ticket_name)
{
	global $db;

	$sql = "SELECT ticket_name FROM support_user_tickets WHERE user = :user AND ticket_name = :ticket_name";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':user', $user);
	$stmt->bindValue(':ticket_name', $ticket_name);
    $stmt->execute();

    while ($row = $stmt->fetch())
    {
    	$ticket_name_r = $row['ticket_name'];
    }

    if($ticket_name_r == $ticket_name)
    	return true;
    else
    	return false;

}

function updateTicketStatus($ticket_id, $status)
{
    global $db;

    $sql = "UPDATE support_user_tickets SET close = :close WHERE ticket_id = :ticket_id";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':close', $status);
    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}


function createTicket($ticket_id, $ticket_name, $user, $support_id, $category, $time)
{
	global $db;

    $sql = "INSERT INTO support_user_tickets (ticket_id, ticket_name, user, support_id, category, time) VALUES(:ticket_id, :ticket_name, :user, :support_id, :category, :time)";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':ticket_id', $ticket_id);
    $stmt->bindValue(':ticket_name', $ticket_name);
    $stmt->bindValue(':user', $user);
    $stmt->bindValue(':support_id', $support_id);
    $stmt->bindValue(':category', $category);
    $stmt->bindValue(':time', $time);
    $stmt->execute();

    if($stmt)
    	return true;
    else
    	return false;
}

function selectSupport()
{
	global $db;

    $sql = "SELECT support_id FROM support_agents WHERE banned = 0";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $row = $stmt->fetchAll();

    $rand_keys = array_rand($row, 1);

    return $row[$rand_keys];
}


/**
 * Выводим тикет по его id
 */

function getTicketById($ticket_id)
{
    global $db;

    $sql = "SELECT * FROM support_user_tickets WHERE ticket_id = :ticket_id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':ticket_id', $ticket_id);

    $stmt->execute();

    return $stmt->fetch();
}

function getSupportFromTicket($ticket_id)
{
    global $db;

    $sql = "SELECT support_id FROM support_user_tickets WHERE ticket_id = :ticket_id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':ticket_id', $ticket_id);

    $stmt->execute();

    return $stmt->fetch();
}

/**
 * Проверка существование тикета
 */

function issetTicket($ticket_id)
{
    global $db;

    $sql = "SELECT ticket_id FROM support_user_tickets WHERE ticket_id = :ticket_id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':ticket_id', $ticket_id);

    $stmt->execute();

    $row = $stmt->fetch();
        
    if(empty($row["ticket_id"]))
        return false;
    else
        return true;
}

function thisSupport($ticket_id, $support_id)
{
    global $db;

    $sql = "SELECT support_id FROM support_user_tickets WHERE ticket_id = :ticket_id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':ticket_id', $ticket_id);

    $stmt->execute();

    $row = $stmt->fetch();
        
    if($row["support_id"] != $support_id)
        return false;
    else
        return true;
}

function thisUser($ticket_id, $user)
{
    global $db;

    $sql = "SELECT user FROM support_user_tickets WHERE ticket_id = :ticket_id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':ticket_id', $ticket_id);

    $stmt->execute();

    $row = $stmt->fetch();
        
    if($row["user"] != $user)
        return false;
    else
        return true;
}
?>