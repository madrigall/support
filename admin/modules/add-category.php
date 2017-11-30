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

if($_POST['add-category'])
{
    $category = (string)$_POST["category"];
    
    if(empty($category))
    {
        echo '{"error": "Категория не может быть пустой!"}';
        exit();
    }

    if(strlen($category) > 128)
    {
        echo '{"error": "Категория больше чем 128 символов!"}';
        exit();
    }
     
    foreach(support_categories() as $categories)
    {
        if($category == $categories["name"])
        {
            $coreLog->write("{$_SESSION['support']['name']} пытался создать категорию: $category, но она уже существует!");
             
            echo '{"error": "Такая категория уже существует!"}';
            exit();
        }
    }

    $result = addCategory($category);
    
    if($result)
    {
        $coreLog->write("{$_SESSION['support']['name']} добавил категорию: $category!");

        echo '{"done": "Категория добавлена!"}';
        exit();
    }
    else
    {
        $coreLog->write("{$_SESSION['support']['name']} не смог добавить категорию: $category!");

        echo '{"error": "Ошибка!"}';
        exit();
    }
}
else
{
    $coreLog->write("Ошибка при добавлении категории, нет POST параметра!");

    echo '{"error": "Ошибка!"}';
    exit();
}
?>