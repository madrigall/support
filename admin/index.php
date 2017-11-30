<?php
define("SUPPORT_SYSTEM", true);

require_once "../config.php";
require_once "functions.php";
require_once PATH . MODEL;
require_once PATH . ADMIN_MODEL;
require_once PATH . "core/functions.php";
require_once PATH . "core/template-functions.php";
require_once PATH . "core/recaptcha.php";

session_start();

$logout = parseGetParameters($_GET['logout']);

if(isset($logout) && $logout == "true")
{
	$coreLog->write("Выход пользователя {$_SESSION['support']['name']}");

	logout($_SESSION['support']['id'], $_SESSION['support']['key']);
	$_SESSION = array();

	if(defined("CUSTOM_FOLDER"))
	{
		header("Location: " . CUSTOM_FOLDER . "admin/");
		exit();
	}
	else
	{
		header("Location: /admin/");
		exit();
	}
}

//Если не авторизованы
if(!is_support_login())
{
	//$coreLog->write("Форма авторизации!", false);
	include_admin_template("login-form"); 
}
else
{
	$updateCore = new updateCore(LICENSE_KEY, UPDATE_SERVER, CURRENT_VERSION);

	if(!isset($_SESSION["update"]) && empty($_SESSION["udpate"]))
	{
		$result_check = $updateCore->have_update();

		if($result_check  && ($result_check != "invalid_license" && $result_check != "error_server" && $result_check != "invalid_target" && $result_check != "valid_version"))
			$_SESSION["update"] = $result_check;
		else
			$_SESSION["update"] = false;
	}

	$support_id = $_SESSION["support"]["id"];

	/**
	 *	Активируем тему
	 */

	$themes_array = scan_templates_dir();
	$isIsset = false;
	$themeToActive = parseGetParameters( (string) $_GET['active']); 


	if($themeToActive)
	{
		if(canSupportDo($support_id, "active-themes"))
		{
			foreach ($themes_array as $key => $value)
			{
				if($themeToActive == $value)
					$isIsset = true;
			}

			if($isIsset)
			{
				$result = activeTheme($themeToActive);
				
				if($result)
				{
					$coreLog->write("{$_SESSION["support"]["name"]} активировал тему - $themeToActive!");
					
					header("Location: /admin/?mode=themes&status=success");
					exit();
				}
				else
				{
					$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при активации темы $themeToActive!");
					
					header("Location: /admin/?mode=themes&status=fail");
					exit();
				}
				exit;
			}
			else
			{
				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при активации темы $themeToActive - темы нет!");
				
				header("Location: /admin/?mode=themes&status=fail");
				exit();
			}

		}
	}

	/**
	 *	Активируем плагин
	 */

	$plugins_array = scan_plugins_dir();
	$isIsset_p = false;
	$pluginToActive = parseGetParameters( (string) $_GET['plug_active']); 

	if($pluginToActive)
	{
		if(canSupportDo($support_id, "active-plugins"))
		{
			foreach ($plugins_array as $key => $value)
			{
				if($pluginToActive == $value)
					$isIsset_p = true;
			}

			if($isIsset_p)
			{		
				$result = activePlugin($pluginToActive);
				
				if($result)
				{
					$coreLog->write("{$_SESSION["support"]["name"]} активировал плагин $pluginToActive!");
					
					header("Location: /admin/?mode=plugins&status=success");
					exit();
				}
				else
				{
					$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при активации плагина $pluginToActive!");
					
					header("Location: /admin/?mode=plugins&status=fail");
					exit();
				}
				exit;
			}
			else
			{
				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при активации плагина $pluginToActive - плагина нет!");

				header("Location: /admin/?mode=plugins&status=fail");
				exit();
			}
		}
	}

	/**
	 *	Деактивируем плагин
	 */

	$pluginToDeActive = parseGetParameters( (string) $_GET['plug_deactive']); 
	$isIsset_d = false;

	if($pluginToDeActive)
	{	
		if(canSupportDo($support_id, "de-active-plugins"))
		{
			foreach ($plugins_array as $key => $value)
			{
				if($pluginToDeActive == $value)
					$isIsset_d = true;
			}

			if($isIsset_d)
			{
				$result = deActivePlugin($pluginToDeActive);
				
				if($result)
				{
					$coreLog->write("{$_SESSION["support"]["name"]} деактивировал плагин $pluginToDeActive!");
					
					header("Location: /admin/?mode=plugins&status=success_d");
					exit();
				}
				else
				{
					$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при деактивации плагина $pluginToDeActive!");
					
					header("Location: /admin/?mode=plugins&status=fail_d");
					exit();
				}
				exit;
			}
			else
			{
				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при деактивации плагина $pluginToDeActive - плагина нет!");
				
				header("Location: /admin/?mode=plugins&status=fail_d");
				exit();
			}
		}
	}


	/**
	 * Добавление пользователя
	 */

	if($_GET["users-action"] == "add" && canSupportDo($_SESSION["support"]["id"], "add-users"))
	{
		$error_string = false;
		$result = false;
		$support_to_add_id = parseGetParameters((int)$_POST["new_support_id"]);
		$support_to_add_name = parseGetParameters((string)$_POST["new_support_name"]);
		$support_to_add_email = parseGetParameters((string) $_POST["new_support_email"]);
		$support_to_add_rank = parseGetParameters((int) $_POST["new_support_rank"]);
		$support_to_add_pass = (string) $_POST["new_support_pass"];
		$support_to_add_pass = str_replace(" ", "", $support_to_add_pass);
		
		if(empty($support_to_add_id))
		{
			$support_to_add_id = rand(10000, 99999);
		}
		
		if(findDublicateId($support_to_add_id))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - существующий id!");

			header("Location: ?mode=users&add_status=fail&fail_id=1");
			exit();
		}
		
		
		if(findDublicateName($support_to_add_name))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - существующее имя!");

			header("Location: ?mode=users&add_status=fail&fail_id=2");
			exit();
		}
		
		
		if(findDublicateEmail($support_to_add_email))
		{
			$error_string = true;
			
			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - существующий email!");

			header("Location: ?mode=users&add_status=fail&fail_id=3");
			exit();
		}
		
		$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';


		if(strlen($support_to_add_id) > 64)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - id больше чем 64 символа!");

			header("Location: ?mode=users&add_status=fail&fail_id=14");
			exit();
		}

		if(strlen($support_to_add_id) < 5)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - id меньше 5 символов!");

			header("Location: ?mode=users&add_status=fail&fail_id=15");
			exit();
		}

		if(strlen($support_to_add_name) > 64)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - имя больше чем 64 символа!");

			header("Location: ?mode=users&add_status=fail&fail_id=12");
			exit();
		}
		
		if(strlen($support_to_add_name) < 5)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - имя меньше 5 символов!");

			header("Location: ?mode=users&add_status=fail&fail_id=13");
			exit();
		}

		if (!preg_match("/[0-9A-Za-z_\.\-]+@[0-9a-z_\.\-]+\.[a-z]{2,4}/i", $support_to_add_email) || empty($support_to_add_email))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - некорректный email!");

			header("Location: ?mode=users&add_status=fail&fail_id=4");
			exit();
		}
		
		if(strlen($support_to_add_email) > 64)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - email больше чем 64 символа!");

			header("Location: ?mode=users&add_status=fail&fail_id=10");
			exit();
		}	

		if(strlen($support_to_add_email) < 10)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - email меньше чем 10 символов!");

			header("Location: ?mode=users&add_status=fail&fail_id=11");
			exit();
		}

		if(strlen($support_to_add_pass) > 64)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - пароль больше чем 64 символа!");

			header("Location: ?mode=users&add_status=fail&fail_id=5");
			exit();
		}
		
		if(strlen($support_to_add_pass) < 6)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - пароль меньше чем 6 символов!");

			header("Location: ?mode=users&add_status=fail&fail_id=6");
			exit();
		}	

		$support_to_add_pass = passHash($support_to_add_pass);
		
		//TODO: Javascript RegExp
		if(!preg_match("#^[0-9]*$#i", $support_to_add_id) || empty($support_to_add_id))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - некорректный id!");

			header("Location: ?mode=users&add_status=fail&fail_id=7");
			exit();
		}
		
		if(!preg_match('#^[a-zA-Z0-9_]*$#i', $support_to_add_name) || empty($support_to_add_name))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - некорректное имя!");

			header("Location: ?mode=users&add_status=fail&fail_id=8");
			exit();
		}

		if(!preg_match('#^[0-9]*$#i', $support_to_add_rank) || $support_to_add_rank > 10 || empty($support_to_add_rank))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank) - некорректный ранг!");

			header("Location: ?mode=users&add_status=fail&fail_id=9");
			exit();
		}

		if($support_to_add_rank > $_SESSION["support"]["rank"])
		{
			$error_string = true;

			$coreLog->write("Вы не можете добавить пользователя, который старше вас!");

			header("Location: ?mode=users&add_status=fail&fail_id=16");
			exit();
		}

		if(!$error_string)
			$result = addSupport($support_to_add_id, $support_to_add_name, $support_to_add_rank, $support_to_add_email, $support_to_add_pass);
		else
		{
			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при добавлении пользователя ($support_to_add_name id:$support_to_add_id email:$support_to_add_email rank: $support_to_add_rank). Данные не коррекстны!");

			header("Location: ?mode=users&add_status=fail");
			$_POST["add_support_fail"] = $error_string;
			exit();
		}

		if($result)
		{
			$coreLog->write("Добавлен пользователь с id $support_to_add_id");

			header("Location: ?mode=users&add_status=success");
			exit();
		}
		else
		{
			$coreLog->write("Пользователь не добавлен");

			header("Location: ?mode=users&add_status=fail");
			exit();
		}

	}
	
	/**
	 *	Удаление пользователя
	 */

	$select = (int) $_POST['select_action'];

	if($select == "2" || $_GET["delete_supports"])
	{
		//$coreLog->write("Подключен шаблон удаления пользователей!");

		include_admin_template("support-delete");
		exit();
	}

	$support_to_delete = parseGetParameters((int) $_GET['support_delete']);

	if($support_to_delete)
	{
		$support_rank_to_delete = support_rank($support_to_delete);

		if($_SESSION['support']['rank'] < $support_rank_to_delete)
		{
			$coreLog->write("({$_SESSION["support"]["name"]}:rank {$_SESSION["support"]["rank"]}) Невозможно удалить пользователя с рангом выше вашего ($support_rank_to_delete)!");

			header("Location: /admin/?mode=users&delete_support_error=1");
			exit;
		}
		else	
			if($_SESSION['support']['id'] != $support_to_delete)
			{
				$result = deleteSupport($support_to_delete);

				if($result)
				{
					$coreLog->write("{$_SESSION["support"]["name"]} удалил пользователь с id $support_to_delete!");

					header("Location: /admin/?mode=users&delete_support_status=success");
					exit;
				}
				else
				{
					$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при удалении пользователь с id $support_to_delete!");

					header("Location: /admin/?mode=users&delete_support_error=3");
					exit;
				}
			}
			else
			{	
				$coreLog->write("({$_SESSION["support"]["name"]} id:{$_SESSION["support"]["id"]}) Вы не можете удалить сами себя!($support_to_delete)");
				
				header("Location: /admin/?mode=users&delete_support_error=2");
				exit;
			}
	}

	$support_to_edit = parseGetParameters((int) $_POST['support_edit']);
	$support_edit_get = parseGetParameters((int) $_GET['support_edit']);
	$submit_edit_user = parseGetParameters((string) $_POST['submit_edit_user']);

	/**
	 * Редактирование пользователя
	 */

	if(($support_to_edit || $submit_edit_user || $support_edit_get) || (isset($submit_edit_user) && $select == "1"))
	{
		//$coreLog->write("Подключен шаблон редактирования пользователей!");

		include_admin_template("support-edit");
		exit();
	}


	/**
	 * Бан и разбан
	 */

	$toBan = (int) $_GET["ban"];
	$toUnBan = (int) $_GET["unban"];

	if($toBan)
	{
		if($toBan == $_SESSION["support"]["id"])
		{
			$coreLog->write("{$_SESSION["support"]["name"]} id:{$_SESSION["support"]["id"]} хотел забанить себя!(id:$toBan)");

			header("Location: ?mode=users&ban_e=1");
			exit;
		}
		else
		{
			$result_b = ban($toBan);

			if($result_b)
			{
				$coreLog->write("{$_SESSION["support"]["name"]} забанил пользователя с id $toBan!");

				header("Location: ?mode=users&ban_status=success");
				exit;
			}
			else
			{
				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при попытке забанить пользователя с id $toBan!");

				header("Location: ?mode=users&ban_status=fail");
				exit;
			}
		}
	}

	if($toUnBan)
	{
		if($toUnBan == $_SESSION["support"]["id"])
		{
			$coreLog->write("{$_SESSION["support"]["name"]} хотел разбанить себя!(id:$toUnBan). Несанкционированный вход этого пользователя!");

			header("Location: ?mode=users&unban_e=1");
			exit;
		}
		else
		{
			$result_ub = unban($toUnBan);

			if($result_ub)
			{
				$coreLog->write("{$_SESSION["support"]["name"]} разбанил пользователя с id $toUnBan!");

				header("Location: ?mode=users&unban_status=success");
				exit;
			}
			else
			{
				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при попытке разбанить пользователя с id $toUnBan!");

				header("Location: ?mode=users&unban_status=fail");
				exit;
			}
		}

	}

	/**
	 * Шаблоны
	 */

	$mode = parseGetParameters( (string) $_GET['mode']);

	$ticket_id = (int) $_GET["ticket_id"];

	if($ticket_id)
	{
		//$coreLog->write("Подключен шаблон сообщений в тикетах!");

		include_admin_template("ticket-content");
		exit();
	}

	if($mode)
		switch ($mode)
		{
			case "update":
				//$coreLog->write("Подключен шаблон обновления!");

				include_admin_template("update"); 
			break;

			case "tickets":
				//$coreLog->write("Подключен шаблон тикетов!");

				include_admin_template("tickets"); 
			break;
			
			case "plugins":
				//$coreLog->write("Подключен шаблон плагинов!");

				include_admin_template("plugins"); 
			break;

			case "themes":
				//$coreLog->write("Подключен шаблон тем!");

				include_admin_template("themes"); 
			break;

			case "users":
				//$coreLog->write("Подключен шаблон пользователей!");

				include_admin_template("users"); 
			break;

			case "settings":
				//$coreLog->write("Подключен шаблон настроек!");

				include_admin_template("settings"); 
			break;

			default:
				include_admin_template("index"); 
			break;
		}
	else
	{
		include_admin_template("index"); 
	}
}



?>