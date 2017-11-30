<?php
define('SUPPORT_SYSTEM', TRUE);


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

if($_POST['delete_message'])
{
    $written_by = (string)$_POST["written_by"];
    $message_id = (int)$_POST["message_id"];
    
    $result = deleteMessage($message_id, $written_by);
    
    if($result)
    {
        $coreLog->write("{$_SESSION["support"]["name"]} удалил сообщение с id $message_id!");

        echo '{"done": "Сообщение удалено!"}';
        exit();
    }
    else
    {
        $coreLog->write("{$_SESSION["support"]["name"]} не смог удалить сообщение с id $message_id!");

        echo '{"error": "Ошибка!"}';
        exit();
    }
}
else
{
    $coreLog->write("Ошибка при удалении сообщения пользователем {$_SESSION["support"]["name"]}!");

    echo '{"error": "Ошибка!"}';
    exit();
}
?>