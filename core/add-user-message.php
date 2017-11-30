<?php
define('SUPPORT_SYSTEM', TRUE);
session_start();

require_once "../config.php";
require_once "functions.php";
require_once PATH . MODEL;

$message = (string) htmlspecialchars($_POST['message']);
$ticket_id = (int) $_POST['ticket_id'];
$user = (string) $_POST['user'];
$submit = (string) $_POST['submit'];
$user_email = $_SESSION["email"];

           
if(!$_SESSION["user"]) 
{
    echo '{"error": "Ошибка!"}';
    exit();
}

if($ticket_id && $user && $submit == "message")
{
	$dt = new DateTime("NOW");
	$time = $dt->format("c");

	$avatar = user_avatar($user_email, 50, false);
	
	$ticket_status = ticket_status($ticket_id, true);

	if(!ticket_open($ticket_id))
	{
		echo '{"error": "Заявка закрыта!"}';
		exit();
	}

	if(empty($message))
		exit('{"error" : "Вы не ввели сообщение!"}');
	else
	{
		$dublicate = findUserDublicateMessage($ticket_id, $user, $message);

		if($dublicate)
			echo '{"error" : "Вы это уже писали!"}';

		else
		{
			$result = addUserMessage($ticket_id, $user, $user_email, strip_tags($message, array("<script>", "</script>")), $time);

			if(!$result)
			{
				$coreLog->write("Ошибка при записи сообщения от $user!");

				echo '{"error" : "Ошибка при записи сообщения!"}';
			}
			else 
			{
				$message_to_support = "";
				$message_to_support .= "<div class='open message-wrapper user' id='user-message'>";
	            $message_to_support .= "<div class='user-image'>";
	            $message_to_support .= "<span class='user-" . $user . " user-online' data-value='" . $user . "'></span>";
	            $message_to_support .= "<img alt='' src='" . $avatar . "'class='user_image'>";
	            $message_to_support .= "</div>";
	            $message_to_support .= "<div class='message'>";
	            $message_to_support .= "<div class='info-message clearfix'>";
	            $message_to_support .= "<span class='user-name'>" . $user . "</span><span class='date-message'>". showTime('d.m.Y в H:i', $time) ."</span>";
	            $message_to_support .= "</div>";
	            $message_to_support .= "<p class='message-text'>";
	            $message_to_support .= str_replace('"',"'", $message);
	            $message_to_support .= "</p>";
	            $message_to_support .= "</div>";
	            $message_to_support .= "</div>";
				
				if($ticket_status["close"] != 0)
					if(updateTicketStatus($ticket_id, 0))
						$coreLog->write("Статус тикета #$ticket_id обновлен");

				$coreLog->write("$user добавил сообщение в тикет #$ticket_id!");

				echo '{"status" : "ok", "user" : "' . $user . '", "message" : "' . $message . '", "time" : "' . showTime('d.m.Y в H:i', $time) . '", "avatar" : "' . $avatar . '", "to_support" : "' . $message_to_support . '"}';
			}	
		}
	}
}
else
{
	$coreLog->write("Ошибка при добавлении сообщения в тикет #$ticket_id пользователем $user!");

	echo '{"error" : "Произошла ошибка, сообщите об этом администратору!"}';
}	


?>