<?php
include ("authorization.php");
include ("plugins-core.php");


/**
 * Преобразовываем дату из бд в нормальный вид
 */

function showTime($format, $time)
{
	$date = new DateTime($time); 
	return $date->format($format); 
}

function the_date($format)
{
    $date = new DateTime("NOW"); 
    return $date->format($format);
}


function print_arr($arr)
{
    echo "<pre>";
        print_r($arr);
    echo "</pre>";
}

/**
 * Выводим путь к ативной теме
 */

function template_url()
{
	if(defined("CUSTOM_FOLDER"))
		echo CUSTOM_FOLDER . DIRECTORY_SEPARATOR . ACTIVE_TEMPLATE;
	else 
		echo ACTIVE_TEMPLATE;
}

/**
 * Возвращаем путь к ативной теме
 */

function template_path()
{
	return PATH . ACTIVE_TEMPLATE;
}

/**
 * Выводим путь к ативной теме
 */

function admin_template_url()
{
	if(defined("CUSTOM_FOLDER"))
		echo CUSTOM_FOLDER . "admin/" . ADMIN_TEMPLATE;
	else 
		echo ADMIN_TEMPLATE;
}

/**
 * Возвращаем путь к ативной теме
 */

function admin_template_path()
{

	return PATH . "admin/" . ADMIN_TEMPLATE;
}

/**
 * Возвращаем запрашиваемую информацию о системе
 */

function support_info($arguments)
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());

	switch($arguments)
	{
		case "url":
			if(defined("CUSTOM_FOLDER"))
				echo "http://" . $_SERVER["HTTP_HOST"] . CUSTOM_FOLDER;
			else
				echo "http://" . $_SERVER["HTTP_HOST"];
		break;

		case "template":
			return PATH . ACTIVE_TEMPLATE;
		break;

		case "default_templates";
			return PATH . "/core/default_templates/";
		break;

		case "core_scripts_dir":
			$host = parse_url($_SERVER['HTTP_HOST']);
			if(defined("CUSTOM_FOLDER"))
				echo "{$host['host']}" . CUSTOM_FOLDER . "/core/js/";
			else
				echo "{$host['host']}/core/js/";
		break;

		case 'templates_dir':
			$host = parse_url($_SERVER['HTTP_HOST']);
			return "{$host['host']}/content/themes/";
		break;

		case 'templates_path':
			return "{$_SERVER['DOCUMENT_ROOT']}/content/themes/";
		break;

		case 'core_resources':
			$host = parse_url($_SERVER['HTTP_HOST']);
			return "{$host['host']}/core/";
		break;

		case 'core_resources_echo':
			$host = parse_url($_SERVER['HTTP_HOST']);
			if(defined("CUSTOM_FOLDER"))
				echo "{$host['host']}" . CUSTOM_FOLDER . "/core/";
			else
				echo "{$host['host']}/core/";
		break;
	}

}


/**
 * Вывод картинки темы в админку
 */

function themePicture($theme_name)
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());

	$picture = support_info("templates_path") . $theme_name . "/theme-image.png";

	if(file_exists($picture))
		$image = support_info("templates_dir") . $theme_name . "/theme-image.png";
	else
		$image = support_info("core_resources") . "images/no-picture.png";

	return $image;
}

function pluginPicture($plugin_name)
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());

	$picture = support_info("plugins_path") . $plugin_name . "/plugin-image.png";

	if(file_exists($picture))
		$image = support_info("plugins_path") . $plugin_name . "/plugin-image.png";
	else
		$image = support_info("core_resources") . "images/no-picture.png";

	return $image;
}

function user_avatar($email, $s = 32, $echo = true, $d = 'identicon', $r = 'g', $img = false, $atts = array() ) 
{

    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    if($echo)
    	echo $url;
    else
    	return $url;
}

function support_avatar($support_id, $s = 32, $echo = true, $d = 'identicon', $r = 'g', $img = false, $atts = array() )
{
	global $db;

    $sql = "SELECT support_email FROM support_agents WHERE support_id = :support_id LIMIT 1";
    $stmt = $db->prepare($sql);

    $stmt->bindValue(':support_id', $support_id);
    $stmt->execute();
    $row = $stmt->fetch();

    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $row["support_email"] ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    
    if($echo)
    	echo $url;
    else
    	return $url;
}

/**
 * Проверяем GET параметр
 */

function parseGetParameters($param)
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());

	$param = trim($param);
	$param = strip_tags($param);
	$param = htmlspecialchars($param);

	return $param;
}

function is_logged()
{
	if($_SESSION["user"])
		return true;
}

function parsePages()
{
    switch ($_GET["mode"])
    {
        case 'tickets':
            return "Заявки";
        break;
        
        case 'update':
            return "Обновление";
        break;
        
        case 'plugins':
            return "Плагины";
        break;

        case 'themes':
            return "Темы";
        break;

        case 'users':
            return "Пользователи";
        break;

        case 'settings':
            return "Настройки";
        break;
    }

    if($_GET["ticket_id"])
        return "Тикет #{$_GET['ticket_id']}";

    if($_GET["user_tickets"])
        return "Заявки {$_GET['user_tickets']}";

    if($_GET["action"] == "login")
        return "Авторизация";

}

function auth_user($flag = false)
{
	$support_id = getSupportFromTicket($_GET["ticket_id"]);

	$params = array(
	    'user_id' => (string) $_SESSION["user"],
	    'type' => 'user',
	    'ticket_id' => (int) $_GET["ticket_id"],
	    'key' => hash('sha256', SALT . $_SESSION["user"]),
	    'support' => $support_id["support_id"]
	);

	if($flag)
		$params = array(
		    'user_id' => (string) $_SESSION["user"],
		    'type' => 'user',
		    'key' => hash('sha256', SALT . $_SESSION["user"]),
		    'create' => true
		);

	return base64_encode(json_encode($params));

}


?>