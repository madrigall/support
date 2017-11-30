<?php

require_once PATH . ADMIN_MODEL;
require_once PATH . MODEL;

/**
 * Из 2 массивов делаем 1(1 массив передается)
 */

function parse_arguments($arguments, $default = "")
{
	$result = array_replace($default, $arguments);
	return $result;
}


/**
 * Получаем все темы в папке
 */

function scan_templates_dir()
{
	$result = scandir("../content/themes/");
	unset($result[0],$result[1]);

	foreach($result as $key => $value)
	{
		if($result[$key] == "index.php")
			unset($result[$key]);
	}
		
	return $result;
}

/**
 * Получаем все плагины в папке
 */

function scan_plugins_dir()
{
	$result = scandir("../content/plugins/");
	unset($result[0],$result[1]);

	foreach($result as $key => $value)
	{
		if($result[$key] == "index.php")
			unset($result[$key]);
	}
		
	return $result;
}


/**
 * Выводим в header информацию о текущем пользователе
 */

function headerSupportInformation()
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());

	$info = "
		<div id='info_in_head'>
			<span class='support_info'>{$_SESSION['support']['name']}<span class='sup_id'>({$_SESSION['support']['id']})</span></span>|<a class='logout' href='?logout=true'>Выйти</a>
		</div>
			";
	echo $info;
}


/**
 * Проверяем, может ли пользователь это делать
 */

function canSupportDo($support_id, $whatDo)
{
	global $db;

    $sql = "SELECT support_rank FROM support_agents WHERE support_id = :support_id";
    $stmt = $db->prepare($sql);

	$stmt->bindValue(':support_id', $support_id);
    $stmt->execute();

    //Получаем данные из бд
    while($row = $stmt->fetch())
	{
		$sup_rank = $row['support_rank'];
	}

	switch ($whatDo)
	{
		case 'themes':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'plugins':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'settings':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'plugins':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'tickets':
			if($sup_rank >=6)
				return true;
			if($sup_rank <= 4)
				return false;
		break;

		case 'update':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'users':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'edit-users':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'add-users':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'delete-users':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;
		
		case 'active-themes':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'active-plugins':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;

		case 'de-active-plugins':
			if($sup_rank == 10)
				return true;
			if($sup_rank <= 5)
				return false;
		break;


		default:
			return false;
		break;
	}
}


/**
 * Хеш пароля
 */

function passHash($pass)
{
	//$salt = '$2a$10$'.substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(),mt_rand()))), 0, 22) . '$';
	//return crypt($pass . PASS_SALT, PASS_SALT);
	return crypt($pass, PASS_SALT);
}

/**
 * Хук меню в админке
 */

function admin_menu()
{
	if (hs_has_hook(__FUNCTION__))
		return hs_call_hook(__FUNCTION__, func_get_args());	
}

/**
 * Убираем с теги с пароля
 */

function parsePass($pass)
{
	$pass = strip_tags($pass);
	return $pass;
}

/**
 * Запрос через curl
 */
function postRequest($url, $data)
{
	$curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //CURLOPT_HTTPHEADER, array("Content-Type: application/json")
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
}


/**
 * Проверка на наличие обновления
 */
function have_update($echo = false)
{
	$version = postRequest("http://akatamut.com/v_index.php", "version=true");

	if($echo)
		return $version;
	
	if($version >= CURRENT_VERSION)
		return false;
	else
		return true;
}

/**
 * Подключение к серверу сообщений
 */

function auth_support()
{
	//$tickets = getAllOpenTicketsToSupport($_SESSION["support"]["id"]);
	
	$params = array(
	    //'key' => $_COOKIE["key"],
	    'user_id' => $_SESSION["support"]["id"],
	    'type' => 'support',
	    'key' => hash('sha256', SALT . $_SESSION["support"]["id"])
	);

	return base64_encode(json_encode($params));

}
?>