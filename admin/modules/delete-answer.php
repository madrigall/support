<?php
define('SUPPORT_SYSTEM', TRUE);

require_once "../../config.php";
require_once "../functions.php";

session_start();

$referer = $_SERVER['HTTP_REFERER'];

$check_ref = "http://" . $_SERVER['HTTP_HOST'] . "/admin/?mode=settings";
            
if(!$_SESSION["support"]["id"] && $referer != $check_ref) 
{
    echo '{"error": "Ошибка!"}';
    exit();
}

if($_POST['delete-answer'])
{
    $id = (string)$_POST["id"];
    
    if(empty($id))
    {
        echo '{"error": "Ответ не может быть пустым!"}';
        exit();
    } 
            
    $result = deleteAnswer($id);

    if($result)
    {
        $coreLog->write("{$_SESSION['support']['id']} удалил ответ с id: $id!");

        echo '{"done": "Категория удалена!"}';
        exit();
    }
    else
    {
        $coreLog->write("{$_SESSION['support']['id']} не смог удалить ответ с id: $id. Возможно его нет!");

        echo '{"error": "Ошибка!"}';
        exit();
    }    
}
else
{
    $coreLog->write("Ошибка при удалении ответа, нет POST параметра!");

    echo '{"error": "Ошибка!"}';
    exit();
}
?>