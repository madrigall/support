<?php
defined('SUPPORT_SYSTEM') or die('Access denied');
session_start();
    // подключение модели
    require_once MODEL;

    require_once PATH . "core/template-functions.php";
    require_once PATH . "core/authorization.php";
    require_once PATH . "core/recaptcha.php";

   
    /**
     * Errors
     */
    
    $error_code = null;

    $error_code = $_GET["e"]; 
    $error_code = abs(intval($error_code)); 

    if(!empty($error_code) && isset($_GET["e"])) 
        switch ($error_code)
        {
            case 400:
                include_error_template("400");
                exit();
            break;
            
            case 401:
                include_error_template("401");
                exit();
            break;

            case 403:
                include_error_template("403");
                exit();
            break;

            case 404:
                include_error_template("404");
                exit();
            break;

            case 500:
                include_error_template("500");
                exit();
            break;
        }

    /**
     * End
     */

    $recaptcha = new recaptcha(CAPTCHA_SECRET_KEY);
    
    include_template_functions("functions");

    $action = $_REQUEST["action"];
    $ticket = (int) $_REQUEST["ticket_id"];

    $user = $_SESSION["user"];
    
    if($_POST["login_btn"])
    {     
        $ip = getenv("HTTP_X_FORWARDED_FOR");

        if(empty($ip) || $ip =='unknown') 
            $ip = getenv("REMOTE_ADDR");


        $aLoginName = parseGetParameters($_POST["log"]);

        $isBanned = isBanned($aLoginName, $ip);

        if($isBanned)
        {
            header("Location: ?action=login&auth_fail=4");
            exit();
        }

        if(trim(CAPTCHA) == "TRUE")
        {
            $recaptcha_r = $recaptcha->check($_POST["g-recaptcha-response"]);

            if(!$recaptcha_r)
            {
                unset($recaptcha);
                $coreLog->write("$aLoginName хотел стат роботом!");

                header("Location: ?action=login&auth_fail=captcha");
                exit();
            }

            unset($recaptcha);   
        } 

        $aLoginPass = parseGetParameters($_POST["pwd"]);
        $aLoginRemember = parseGetParameters($_POST["rememberme"]);

        if(user_authorization($aLoginName, $aLoginPass, $aLoginRemember))
        {
            $coreLog->write("Авторизация пользователя $aLoginName.");
            
            header("Location: /");
            exit();
        }
    }

    if($_POST["login_btn_a"])
    {       
        $ip = getenv("HTTP_X_FORWARDED_FOR");

        if(empty($ip) || $ip =='unknown') 
            $ip = getenv("REMOTE_ADDR");
        
        $isBanned = isBanned($aLoginName, $ip);

        if($isBanned)
        {
            header("Location: ?action=login&auth_fail=4");
            exit();
        }

        if(trim(CAPTCHA) == "TRUE")
        {
            $recaptcha_r = $recaptcha->check($_POST["g-recaptcha-response"]);

            if(!$recaptcha_r)
            {
                unset($recaptcha);
                $coreLog->write("anonym хотел стат роботом!");

                header("Location: ?action=login&anonym=true&auth_fail=captcha");
                exit();
            }

            unset($recaptcha);    
        }

        if(user_anonym_authorization(true))
        {
            $coreLog->write("Анонимная авторизация.");
            
            header("Location: /");
            exit();
        }
    }

    switch($action)
    {
        case "create_ticket":
            require_template("create-ticket");
            exit();
      
        break;
        
        case "close_ticket":
            if(is_logged())
            {
                $ticket_id = (int) $_GET["ticket_id"];
                $result = closeTicket($ticket_id);

                if($result)
                {
                    $coreLog->write("Тикет #$ticket_id закрыт");

                    header("Location: ?ticket_id=". $ticket_id);
                    exit();
                }
            }
        break;

        case 'logout':
            if(is_logged())
            {
                $coreLog->write("Выход пользователя {$_SESSION['user']}.");

                unset($_SESSION["user"]);
                unset($_SESSION["id"]);
                unset($_SESSION["email"]);

                $user = NULL;

                session_destroy();
            }
        break;

        case 'login':
            require_template("login-form");
            exit();
        break;
        
    }

    if($ticket)
    {
        require_template("ticket-content");
        exit(); 
    }

    if($_GET['user_tickets'])
    {
        require_template("user-tickets");
        exit();
    }
    else
    {
        require_template("index");
    }
    

?>