<?php
define('SUPPORT_SYSTEM', TRUE);

session_start();
require_once "../../config.php";
require_once "../functions.php";

session_start();
            
/*if(!$_SESSION["support"]["id"]) 
{
    echo '{"error": "Ошибка!"}';
    exit();
}*/

if($_POST['submit_message'])
{
	//Id соббщения пользователя на которое отвечаем
	//$response = (int) $_POST["response"];
	$ticket_id = (int) $_POST["ticket_id"];

	if(issetTicket($ticket_id))
	{
		$array = getTicketById($ticket_id);

		$status_f = ticket_status_admin($array["ticket_id"], false, true);
		$status = ticket_status_admin($array["ticket_id"], true, true);

		$content = "<div class='col-md-4'>
			<a href='?ticket_id=" . $array['ticket_id'] . "'>
			<div class='new-ticket main-statement-block " . $status_f . "' >
				<div class='ribbon'><div class='bg-primary'>Новый</div></div>
				<h2 class='statement-id'>Заявка #" . $array['ticket_id']. "</h2>
				<hr class='statemet'>
				<div class='statement-information'>
					<div class='statement-category'>
						<i class='fa fa-pencil'></i>
						<span class='information-text'>" . $array['ticket_name'] . "</span>
					</div>
					<div class='statement-category'>
						<i class='fa fa-tag'></i>
						<span class='information-text'>" . $array['category'] . "</span>
					</div>
					<div class='statement-info'>
						<i class='fa fa-info-circle'></i>
						<span class='information-text'>" . $status . "</span>
					</div>
					<div class='statement-date'>
						<i class='fa fa-clock-o'></i>
						<span class='information-text'>" . showTime('d.m.Y', $array['time']) . "</span>
					</div>
					<div class='statement-user'>
						<i class='fa fa-user'></i>
						<span class='information-text'>" . $array['user'] . "</span>
					</div>

				</div>
				<hr class='statemet-2'>
				<div class='statement-user-first-message'>
            		<img class='radius image-left' alt='' src='http://www.gravatar.com/avatar/9a54bcaf1baece57756015a9320ec294?s=32&amp;d=identicon&amp;r=g'>                  
                	<p class='message-from-ticket'>" . first_message_from_ticket($array['ticket_id'], true) . "</p>
        		</div>
			</div>
			</a>
		</div>";
	$array_output = array('ticket' => "$content");
	echo json_encode($array_output);

	}
}
else
{
	echo '{"error": "Ошибка при получении данных!"}';
	exit();
}


?>