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

if($_POST['delete-category'])
{
    $category = (string)$_POST["category"];
    
    if(empty($category))
    {
        echo '{"error": "Категория не может быть пустой!"}';
        exit();
    } 

    foreach(support_categories() as $categories)
        if($category == $categories["name"])
            $result_c = true;
            
    if($result_c)
    {
        $result = deleteCategory($category);
    
        if($result)
        {
            $coreLog->write("{$_SESSION['support']['id']} удалил категорию: $category!");

            echo '{"done": "Категория удалена!"}';
            exit();
        }
        else
        {
            $coreLog->write("{$_SESSION['support']['id']} не смог удалить категорию: $category!");

            echo '{"error": "Ошибка!"}';
            exit();
        }
    }
    else
    {
        $coreLog->write("{$_SESSION['support']['id']} не смог удалить категорию: $category, ее нет!");
        
        echo '{"error": "Такой категории нет!"}';
        exit();
    }
    
}
else
{
    $coreLog->write("Ошибка при удалении категории, нет POST параметра!");

    echo '{"error": "Ошибка!"}';
    exit();
}
?>