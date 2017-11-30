<?php
define('SUPPORT_SYSTEM', TRUE);

session_start();
require_once "../../config.php";
require_once "../functions.php";

session_start();

$referer = $_SERVER['HTTP_REFERER'];

$check_ref = "http://" . $_SERVER['HTTP_HOST'] . "/admin/?ticket_id={$_POST["ticket_id"]}";
            
if(!$_SESSION["support"]["id"] && $referer != $check_ref) 
{
    echo '{"error": "Ошибка!"}';
    exit();
}

if($_POST['submit_message'])
{
	//Id соббщения пользователя на которое отвечаем
	//$response = (int) $_POST["response"];
	$ticket_id = (int) $_POST["ticket_id"];
	$support_id = (int) $_POST["support_id"];
	$ticket_status = ticket_status_admin($ticket_id, false, true);
	$message = (string) htmlspecialchars($_POST["message"]);

	$coreLog->write("Статус заявки #$ticket_status изменен!");

	$dt = new DateTime("NOW"); 
	$time = $dt->format("c");
	
	if(!ticket_open($ticket_id))
	{
		echo '{"error": "Заявка закрыта!"}';
		exit();
	}

	$response = getMaxIdMessageInTicket($ticket_id);

	if(empty($message))
	{
		echo '{"error": "Вы не ввели сообщение!"}';
		exit();
	}

	$dublicate = findSupportDublicateMessage($ticket_id, $support_id, $message, $response);

	if($dublicate)
	{
		echo '{"error": "Вы это уже писали!"}';
		exit();
	}
	else
	{
		$result = addSupportMessage($ticket_id, $support_id, $message, $response, $time);

		if(!$result)
		{
			$coreLog->write("Ошибка при записи сообщения от support_$support_id в тикет #$ticket_id!");

			echo '{"error": "Ошибка при записи сообщения!"}';
			exit();
		}
		else 
		{
			$message_to_user .= "<div id='support-message' class='box-message'>";
			$message_to_user .= "<img alt='' src='" . support_avatar($_SESSION['support']['id'], 50, false) . "' class='user_image'>";
			$message_to_user .= "<div class='info-chat'>";
			$message_to_user .= "<span class='support-name chat'>support_" . $support_id . "</span><span class='date_message'>" . showTime('d.m.Y в H:i', $time) . "</span>";
			$message_to_user .= "</div>";    
			$message_to_user .= "<p class='support-message'>";     
			$message_to_user .= str_replace('"',"'", $message);
			$message_to_user .= "</p>";
			$message_to_user .= "</div>";


			$content .= "<div class='open message-wrapper support ui-draggable ui-draggable-handle' id='support-message'>";
            $content .= "<div class='user-image'>";
            $content .= "<span class='support-" . $support_id . " support-online'></span>";
            $content .= "<img alt='' src=' " . support_avatar($_SESSION['support']['id'], 50, false) . "'class='user_image'>";
            $content .= "</div>";
            $content .= "<div class='message'>";
            $content .= "<div class='info-message clearfix'>";
            $content .= "<span class='support-name'>support_" . $support_id . "</span><span class='date-message'>". showTime('d.m.Y в H:i', $time) ."</span>";
            $content .= "</div>";
            $content .= "<p class='message-text'>";
            $content .= str_replace('"',"'", $message);
            $content .= "</p>";
            $content .= "</div>";
            $content .= "</div>";

            if($ticket_status["close"] != 2)
	            if(updateTicketStatus($ticket_id, "2"))
	            	$coreLog->write("Статут заявки #$ticket_id изменен!");

            $coreLog->write("Сообщение успешно добавлено пользователем support_$support_id в тикет #$ticket_id!");

			echo '{"info" : "Сообщение успешно добавлено!", "content" : "' . $content . '", "to_user" : "' . $message_to_user . '"}';
			exit();
		}
	}
}
else
{
	$coreLog->write("Ошибка при записи сообщения!");

	echo '{"error": "Ошибка при записи сообщения!"}';
	exit();
}


?>