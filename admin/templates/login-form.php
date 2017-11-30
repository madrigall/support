<?php 
if(isset($_POST["submit_login"]))
{
    global $coreLog;

    $ip = getenv("HTTP_X_FORWARDED_FOR");

    if(empty($ip) || $ip =='unknown') 
        $ip = getenv("REMOTE_ADDR");

    $support_login = parseGetParameters((string) $_POST['log']);

    $isBanned = isSupportBanned(false, $support_login);

    if($isBanned)
    {
        $coreLog->write("Пользователь $support_login забанен и не может пройти авторизацию!");

        header("Location: ?auth_fail=4");
        exit();
    }

    if(trim(CAPTCHA) == "TRUE")
    {
        $recaptcha = new recaptcha(CAPTCHA_SECRET_KEY);

        $recaptcha_r = $recaptcha->check($_POST["g-recaptcha-response"]);

        if(!$recaptcha_r)
        {
            unset($recaptcha);
            $coreLog->write("$support_login не ввел капчу!");

            header("Location: ?status=fail_captcha");
            exit();
        }

        unset($recaptcha);
    }

    $support_pass = ((string) $_POST['pwd']);
    $remember = parseGetParameters((string) $_POST['rememberme']);

    $result = authorization($support_login, $support_pass, $remember);
    
    if($result)
    {
        $coreLog->write("Авторизация $support_login прошла успешно!");

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
    else
    {
        $coreLog->write("Пользователь $support_login не авторизирован!");

        header("Location: ?status=fail");
        exit();
    }

}
?>
<?php get_header_template("", true);?>
<div class="main-wrapper">
    <section class="margin-20">
        <?php if($_GET["status"] == "fail") : ?>
            <div class="information-message error">
                <p>Неверный логин или пароль!</p>
            </div>
        <?php endif;?>
        
        <?php if($_GET["status"] == "banned") : ?>
            <div class="information-message error">
                <p>Вы ошиблись 3 раза. Ваша активность заблокирована на 15 минут!</p>
            </div>
        <?php endif;?>


        <?php if($_GET["status"] == "fail_captcha") : ?>
            <div class="information-message error">
                <p>Подтвердите, что вы не робот!</p>
            </div>
        <?php endif;?>
        
        <?php if($_GET["auth_fail"] == "4") : ?>
            <div class="information-message error">
                <p>Вы забанены!</p>
            </div>
        <?php endif;?>

        <div id="login-form-back">
            <div id="login-form">
                <form name="loginform" action="" method="post">
                    <input name="log" value="" size="20" type="text" class="input" placeholder="Введите логин"/>
                    <input name="pwd" value="" size="20" type="password" class="input" placeholder="Введите пароль"/>
                    <div class="remember-margin">
                        <label for="rememberme"><input name="rememberme" name="iCheck" type="checkbox" id="rememberme" value="1"/><span class="rememberme">Запомнить меня</span></label>                 
                    </div>
                    <?php if(trim(CAPTCHA) == "TRUE") : ?>
                        <div class="g-recaptcha" data-sitekey="<?php echo CAPTCHA_KEY?>"></div>
                    <?php endif;?>
                    <br>
                    <input type="submit" name="submit_login" id="login_btn" class="btn btn-primary btn-block" value="Войти"/>
                </form>
            </div>
        </div>
    </section>
</div>
<?php get_footer_template("", true)?>
