<?php
global $updateCore;

$result_check = $updateCore->have_update();

if($_GET["do"] == "update")
{
    global $coreLog;
    //print_arr($updateCore->createAllHashes());
    $update_status = $updateCore->startUpdate();
    /*foreach ($update_status as $key => $value) {
        echo $value;
    }*/

    var_dump($update_status);
    switch($update_status)
    {
        case "error_clean":
            header("Location: ?mode=update&exception=error_clean");
            exit();
        break;

        case "valid_version":
            header("Location: ?mode=update&exception=valid_version");
            exit();
        break;

        case "error_in_hash":
            header("Location: ?mode=update&exception=error_in_hash");
            exit();
        break;

        case "hash_error":
            header("Location: ?mode=update&exception=hash_error");
            exit();
        break;

        case "invalid_hash":
            header("Location: ?mode=update&exception=invalid_hash");
            exit();
        break;

        case "unzip_fail":
            header("Location: ?mode=update&exception=unzip_fail");
            exit();
        break;

        case "zclose_fail":
            header("Location: ?mode=update&exception=zclose_fail");
            exit();
        break;

        case "unlink_fail":
            header("Location: ?mode=update&exception=unlink_fail");
            exit();
        break;

        /*case "zclose_fail":
            header("Location: ?mode=update&exception=zclose_fail");
            exit();
        break;

        case "zclose_fail":
            header("Location: ?mode=update&exception=zclose_fail");
            exit();
        break;

        case "zclose_fail":
            header("Location: ?mode=update&exception=zclose_fail");
            exit();
        break;

        case "zclose_fail":
            header("Location: ?mode=update&exception=zclose_fail");
            exit();
        break;

        case "zclose_fail":
            header("Location: ?mode=update&exception=zclose_fail");
            exit();
        break;*/

        default:
            header("Location: ?mode=update&exception=error");
            exit();
        break;
    }
    /*
    $result = $updateCore->startUpdate();

    if(!$result)
    {
        $coreLog->write("{$_SESSION['support']['name']} получил ошибку при обновлении!");

        header("Location: ?mode=update&exception=true");
        exit();
    }
    else
    {
        $coreLog->write("Обновление произведено пользователем {$_SESSION['support']['name']}!");

        header("Location: ?mode=update");
        exit();
    }*/
}
?>
<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
        <div class="main-wrapper">
            <section id="main-container">
                <div class="update-info">
                    <?php if($_GET["exception"]) : ?>
                        <?php if($_GET["exception"] == "error_clean") : ?>
                            <div class="information-message error">
                                <p>Ошибка при подготовки папки обновлений!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "valid_version") : ?>
                            <div class="information-message error">
                                <p>У вас последняя версия!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "error_in_hash") : ?>
                            <div class="information-message error">
                                <p>Ошибка при загрузке обновлений!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "hash_error") : ?>
                            <div class="information-message error">
                                <p>Ошибка при проверке файла!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "invalid_hash") : ?>
                            <div class="information-message error">
                                <p>Файл обновлений повреждён!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "unzip_fail") : ?>
                            <div class="information-message error">
                                <p>Ошибка при распаковке архива!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "zclose_fail") : ?>
                            <div class="information-message error">
                                <p>Ошибка при закрытии архива!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "unlink_fail") : ?>
                            <div class="information-message error">
                                <p>Невозможно удалить архив!</p>
                            </div>
                        <?php endif;?>

                        <?php if($_GET["exception"] == "error") : ?>
                            <div class="information-message error">
                                <p>Критическая ошибка!</p>
                            </div>
                        <?php endif;?>
                    <?php endif;?>

                    <?php //include_admin_template("information-messages"); ?>
                    <?php if(gettype($result_check) == "string" && $result_check == "params_error") : ?>
                        <div class="information-message error">
                            <p>Неверные параметры для получения обновления!</p>
                        </div>
                    <?php endif; ?>
                    <?php if(gettype($result_check) == "string" && $result_check == "valid_version") : ?>
                        <div class="information-message error">
                            <p>У вас последняя версия!</p>
                        </div>
                    <?php endif;?>    

                    <?php if(gettype($result_check) == "string" && $result_check == "invalid_target") : ?>
                        <div class="information-message error">
                            <p>Серверу был отправлен некорректный запрос!</p>
                        </div>
                    <?php endif;?>

                    <?php if(gettype($result_check) == "string" && $result_check == "license_error") : ?>
                        <div class="information-message error">
                            <p>Лицензия отсутствует! Обновление невозможно!</p>
                        </div>
                    <?php endif;?>

                    <?php if(gettype($result_check) == "string" && $result_check == "technical_work") : ?>
                        <div class="information-message warning">
                            <p>На сервере проводятся технические работы, обновление невозможно! Простите за временные неудобства</p>
                        </div>
                    <?php endif;?>

                    <?php if(gettype($result_check) == "string" && $result_check == "server_error") : ?>
                        <div class="information-message error">
                            <p>Критическая ошибка! Сообщите разработчикам!</p>
                        </div>
                    <?php endif;?>

                    <?php if(gettype($result_check) == "array" && $result_check["status"] == "need_update") : ?>
                        <div class="update-info">
                            <div class="information-message warning">
                                <p>Доступна новая версия - <?php echo $result_check["version"]?>!</p>
                                <p><i>Перед обновлением сделайте резервную копию базы данных!</i></p>
                            </div>
                        </div>
                        <?php if($result_check["update_info"]) : ?>
                            <div class="update-changes">
                                    <p>Изменения:</p>
                                    <ul>
                                        <?php echo $result_check["update_info"]?>
                                    </ul>
                            </div>
                        <?php endif;?>
                        <div class="update-wrapper clearfix">
                            <a href="/admin/?mode=update&amp;do=update" class="btn btn-primary btn-block">Обновить</a>
                        </div>
                    <?php endif;?>  

                </div> 
            </section>
        </div>
<?php get_footer_template("", true)?>