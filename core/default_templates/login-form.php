<?php get_header_template()?>
<section id="main-content" class="padding-20">
    <?php if($_GET["auth_fail"] == 1) : ?>
        <div class="error-box">
            <p>Вы ошиблись 3 раза. Ваша активность заблокирована на 15 минут!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["auth_fail"] == 2) : ?>
        <div class="error-box">
            <p>Нет такого метода авторизации!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["auth_fail"] == 3) : ?>
        <div class="error-box">
            <p>Неверный логин или пароль!</p>
        </div>
    <?php endif;?>
    
    <?php if($_GET["auth_fail"] == 4) : ?>
        <div class="error-box">
            <p>Вы забанены!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["auth_fail"] == "captcha") : ?>
        <div class="error-box">
            <p>Подтвердите, что вы не робот!</p>
        </div>
    <?php endif;?>

    <?php if(ANONYM_USERS && (string) $_GET["anonym"] != true) : ?>
        <div class="warning">
            <p>Доступный анонимный вход. Для продолжения нажмите <a href="?action=login&amp;anonym=true">сюда</a></p>
        </div>
    <?php endif;?>

    <?php if(ANONYM_USERS && (string) $_GET["anonym"] == true) : ?>
    <div class="warning">
        <p>Если вы нажмете войти, то будете авторизированы под анонимным пользоветелем и сможете создать анонимный тикет!</p>
    </div>
    <div id="login-form-back">
        <div id="login-form">
            <form name="loginform" action="" method="post">
                <?php if(trim(CAPTCHA) == "TRUE") : ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo CAPTCHA_KEY?>"></div>
                <?php endif;?>
                <br>                
                <input type="submit" name="login_btn_a" id="login_btn" class="btn btn-primary btn-block" value="Войти"/>
            </form>
        </div>
    </div>
    <?php endif;?>

    <?php if(HASH_CRYPT != "none" && (string) $_GET["anonym"] != true) : ?>
    <div id="login-form-back">
        <div id="login-form">
            <form name="loginform" action="" method="post">
                <input name="log" value="" size="20" type="text" class="field-login" placeholder="Введите логин"/>
                <input name="pwd" value="" size="20" type="password" class="field-login" placeholder="Введите пароль"/>
                <div class="remember-margin">
                    <label for="rememberme"><input name="rememberme" name="iCheck" type="checkbox" id="rememberme" value="1"/><span class="rememberme">Запомнить меня</span></label>                 
                </div>
                <?php if(trim(CAPTCHA) == "TRUE") : ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo CAPTCHA_KEY?>"></div>
                <?php endif;?>
                <br>                
                <input type="submit" name="login_btn" id="login_btn" class="btn btn-primary btn-block" value="Войти"/>
            </form>
        </div>
    </div>
    <?php endif;?>
</section>
<?php get_footer_template()?>
