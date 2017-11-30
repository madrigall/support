<?php
define('SUPPORT_SYSTEM', TRUE);

session_start();

require_once "../config.php";
require_once "functions.php";
require_once "recaptcha.php";
require_once PATH . MODEL;

if(!$_SESSION["user"]) 
{
    echo '{"error": "Ошибка!"}';
    exit();
}


if($_POST["submit"] == "ticket")
{
    $category = (int) $_POST["category"];
    $ticket_name = (string) $_POST["name"];
    $user_message = (string) $_POST["user_message"];
    $user = (string) $_POST["user"];
    $user_email = $_SESSION["email"];
    /*
    
    $recaptcha_d = $_POST["recaptcha"];
    
    $recaptcha = new recaptcha(CAPTCHA_SECRET_KEY);
    
    $recaptcha_r = $recaptcha->check($recaptcha_d);

    if(!$recaptcha_r)
    {
        unset($recaptcha);
        
        echo '{"error" : "Подтвердите, что вы не робот!", "id" : "tikcet_name"}';
        exit();
    }

    unset($recaptcha);
    
    */
    
    if(empty($ticket_name))
    {
        echo '{"error" : "Имя пустым быть не может!", "id" : "tikcet_name"}';
        exit();
    }
    else
        if(empty($user_message))
        {
            echo '{"error" : "Сообщение пустым быть не может!", "id" : "user_message_ticket"}';
            exit();
        }
        else
            /*if(!mb_ereg('#^[А-Яа-яёЁa-zA-Z0-9_ \n\r]*$#i', $user_message))
            {
                echo '{"error" : "Разрешены только цифры, латинские и русские буквы в сообщении!", "id" : "user_message_ticket"}';
                exit();
            }*/
               
               /* if(!mb_ereg('#^[А-Яа-яёЁa-zA-Z0-9_ ]*$#i', $ticket_name))
                {
                    echo '{"error" : "Разрешены только цифры, латинские и русские буквы в названии!", "id" : "tikcet_name"}';
                    exit();
                }
                else
                {*/
                    if(empty($category))
                        $category = "Общие вопросы";

                    $dt = new DateTime("NOW");
                    $time = $dt->format("c");
                    $ticket_id = rand(1000000, 9999999);
                    $support_id = selectSupport();

                    $dublicate = findDublicateTicket($user, $ticket_name);

                    if(!$dublicate)
                    {
                        $result_create = createTicket($ticket_id, $ticket_name, $user, $support_id["support_id"], $category, $time);
                        $result = addUserMessage($ticket_id, $user, $user_email, strip_tags($user_message, array("<script>", "</script>")), $time); 

                        if($result_create && $result)
                        {
                            $array = array();
                            $result_array = array();
                            if(isset($_COOKIE["tickets"]))
                                $result_array = array_merge($array, $_COOKIE["tickets"]);

                            array_unshift($result_array, $ticket_id);

                            $j = count($result_array);
                            for($i = 0; $i < $j; $i++)
                                setcookie("tickets[$i]", $result_array[$i],  time() + 2*7*24*60*60*60, "/");
                            
                            $coreLog->write("$user создал заявку #$ticket_id!");

                            echo '{"info" : "Заявка создана!", "ticket_id": ' . $ticket_id . ', "to" : ' . $support_id["support_id"] . '}';
                            exit();
                        }   
                    }
                    else
                    {
                        echo '{"error" : "Дубликат заявки!"}';
                        exit();
                    }
                //}
}
else
{
    $coreLog->write("Access denied!");

    echo "Access denied!";
    exit();
}

?>