<?php 
if($_GET["delete_supports"] == "true" && $_POST["del_sup_btn"] == "yes" && canSupportDo($_SESSION['support']['id'], "delete-users"))
{ 
    global $coreLog;
    
    $result = NULL;
    $j = 0;

    foreach ($_POST["support_to_delete"] as $value)
    {
        $support_to_delete = parseGetParameters((int) $value);

        if($support_to_delete)
        {
            $support_rank_to_delete = support_rank($support_to_delete);

            if($_SESSION['support']['rank'] < $support_rank_to_delete)
            {
                $coreLog->write("Пользователь с id {$_SESSION['support']['id']} не может удалить старшего пользователя с id $support_to_delete!");

                header("Location: ?mode=users&delete_support_error=1");
                exit();
            }
            else    
                if($_SESSION['support']['id'] != $support_to_delete)
                {
                    $result = deleteSupport($support_to_delete);
                }
                else
                {
                    $coreLog->write("Пользователь с id {$_SESSION['support']['id']} не может удалить сам себя!");

                    header("Location: ?mode=users&delete_support_error=2");
                    exit();
                }
        }

    $j++; 
       
    }

    if($result)
    {
        if($j > 1)
        {
            $coreLog->write("{$_SESSION['support']['name']} удалил пользователя с id $support_to_delete!");

            header("Location: ?mode=users&delete_support_status=success_m");
            exit();
        }
        else
        {
            $coreLog->write("{$_SESSION['support']['name']} удалил пользователей: " . var_export($support_to_delete, true));

            header("Location: ?mode=users&delete_support_status=success");
            exit();
        }
    }
    else
    {
        $coreLog->write("{$_SESSION['support']['name']} получил ошибку при удалении пользователя с id $support_to_delete!");

        header("Location: ?mode=users&delete_support_error=3");
        exit();
    }
}

if($_POST["del_sup_btn"] == "no")
{
    header("Location: ?mode=users");
    exit();
}     

?>
<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
    <div class="main-wrapper">
        <section id="main-container">
            <?php $support_id = $_SESSION['support']['id'];?>
            <?php if(canSupportDo($support_id, "delete-users")) : ?>
                <form method="POST" action="?mode=users&delete_supports=true">
                <div id="delete-support" class="dialog-overlay">
                    <div class="dialog-content animated bounce">
                        <p class="dialog-title">Удаление</p>
                        <hr>
                        <br>
                        <p>Вы действительно хотите удалить данные?</p>
                        <br>
                        <a href="?mode=users&amp;delete_suports=true">
                        <button value="yes" name="del_sup_btn" class="dialog-button">
                            <i class="fa fa-check"></i>
                        </button>
                        </a>
                        <a href="?mode=users">
                            <button value="no" name="del_sup_btn" class="dialog-button">
                                <i class="fa fa-times"></i>
                            </button>
                        </a>
                    </div>
                </div>
                <?php if($_POST["select_action"]) : ?>
                    <?php $i = 0;?>
                    <?php foreach($_POST["support_edit"] as $value) : ?>
                        <input type="hidden" name="support_to_delete[<?php echo $i?>]" value="<?php echo $value?>">
                    <?php $i++;?>
                    <?php endforeach;?>
                <?php endif;?>
            </form>


            <!-- Only For View -->
            <table id="users-table">
                <thead>
                    <tr>
                        <th class="check-box">
                            <label for="select-all-users"></label>
                            <input class="select-all-users head checkbox-u" type="checkbox">
                        </th>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Ранг</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 0?>
                <?php foreach(getUsers() as $users): ?>
                    <tr class="table_content">
                        <th class="check-box">
                            <label for="select-all"></label>
                            <input name="support_edit['<?php echo $i?>']" class="checkbox-users checkbox-u" type="checkbox" value="<?php echo $users['support_id']?>">
                        </th>
                        <td class="table_support_id"><?php echo $users["support_id"]?></td>
                        <td class="table_support_name"><?php echo $users["support_name"]?></td>
                        <td class="table_support_rank"><?php echo $users["support_rank"]?></td>
                        <td class="table_support_action"><a class="change" href="?support_edit=<?php echo $users['support_id']?>">Изменить</a>|<a class="delete" href="?mode=users&amp;delete_support=<?php echo $users['support_id']?>">Удалить</a></td>
                    </tr>
                    <?php $i++?>
                <?php endforeach;?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="check-box">
                            <label for="select-all-users"></label>
                            <input class="select-all-users foot checkbox-u" type="checkbox">
                        </th>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Ранг</th>
                        <th>Действие</th>
                    </tr>
                </tfoot>
            </table>

            <!-- Add User -->
            <h2 class="u-add-support">Добавление пользователя</h2>
            <table id="u-add-support">
                <tr>
                    <td><label class="label-text" for="new_support_id">ID</label></td>
                    <td><input placeholder="Введите ID" type="text" class="input margin-right-0" id="new_support_id" name="new_support_id" value=""></td>
                </tr>
                <tr>
                    <td><label class="label-text" for="new_support_name">Имя</label></td>
                    <td><input placeholder="Введите имя" type="text" class="input margin-right-0" id="new_support_name" name="new_support_name" value=""></td>
                </tr>
                <tr>
                    <td><label class="label-text" for="new_support_rank">Ранг</label></td>
                    <td><input placeholder="Введите ранг" type="text" class="input margin-right-0" id="new_support_rank" name="new_support_rank" value=""></td>
                </tr>
                <tr>
                    <td><label class="label-text" for="new_support_email">Email</label></td>
                    <td><input placeholder="Введите email" type="text" class="input margin-right-0" id="new_support_email" name="new_support_email" value=""></td>
                </tr>
                <tr>    
                    <td><label class="label-text" for="new_support_pass">Пароль</label></td>
                    <td><input placeholder="Введите пароль" type="password" class="input margin-right-0" id="new_support_pass" name="new_support_pass"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" id="u-add-support-btn" class="btn btn-wide btn-primary lrg" value="Сохранить">
                    </td>
                </tr>
            </table>
            <!-- End -->
            <?php else : ?>
                <div id="message-error-rank" class="modal-overlay error-info">
                    <div class="modal-content animated bounce">
                        <button class="modal-action" data-modal-close="true"><i id="modal-button" class="fa fa-times fa-lg"></i></button>
                        <p>У вас недостаточно прав для просмотра этой страницы!</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>
<?php get_footer_template("", true)?>  