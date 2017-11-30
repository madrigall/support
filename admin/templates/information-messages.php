<!-- Dialog Delete Support -->
<?php if($_GET["delete_support"]) : ?>
    <div id="delete-support" class="dialog-overlay">
       <div class="dialog-content animated bounce">
           <p class="dialog-title">Удаление</p>
           <hr>
           <br>
           <p>Вы действительно хотите удалить пользователя?</p>
           <br>
           <a href="?mode=users&amp;support_delete=<?php echo $_GET["delete_support"]?>">
               <button class="dialog-button">
                   <i class="fa fa-check"></i>
               </button>
           </a>
           <a href="?mode=users">
               <button class="dialog-button">
                   <i class="fa fa-times"></i>
               </button>
           </a>
       </div>
    </div>
    <?php endif;?>
    <!-- End -->
    
    <!-- Delete Supprot -->
    <?php if($_GET["delete_support_status"] == "success") : ?>
        <div class="information-message success">
            <p>Пользователь успешно удален!</p>
        </div>
    <?php endif;?>
    <?php if($_GET["delete_support_status"] == "success_m") : ?>
        <div class="information-message success">
            <p>Пользователи успешно удалены!</p>
        </div>
    <?php endif;?>
    <!-- End -->

    <!-- Delete Errors -->
    <?php if($_GET["delete_support_error"]) : ?>
    <?php switch($_GET["delete_support_error"]) : 
        case "1" : ?>
            <div class="information-message error">
                <p>Вы не можете удалить пользователя, который старше вас!</p>
            </div>
        <?php break;?>
        <?php case "2" : ?>
            <div class="information-message error">
                <p>Вы не можете удалить сами себя!</p>
            </div>
        <?php break;?>
        <?php case "3" : ?>
            <div class="information-message error">
                <p>Ошибка!</p>
            </div>
        <?php break;?>
    <?php endswitch;?>
    <?php endif;?>
    <!-- End -->

    <!-- Add Support -->
    <?php if($_GET["add_status"] == "fail") : ?>
        <!-- Error While Create Support User -->
        <?php if($_GET["fail_id"]) : ?>
            <?php switch($_GET["fail_id"]) : 
                case "1" : ?>
                    <div class="information-message error">
                        <p>Пользователь с таким id уже существует!</p>
                    </div>
                <?php break;?>

                <?php case "2" : ?>
                    <div class="information-message error">
                        <p>Пользователь с таким именем уже существует!</p>
                    </div>
                <?php break;?>

                <?php case "3" : ?>
                    <div class="information-message error">
                        <p>Пользователь с таким email уже существует!</p>
                    </div>
                <?php break;?>

                <?php case "4" : ?>
                    <div class="information-message error">
                        <p>Некорректный email!</p>
                    </div>
                <?php break;?>

                <?php case "5" : ?>
                    <div class="information-message error">
                        <p>Пароль больше чем 64 символа!</p>
                    </div>
                <?php break;?>

                <?php case "6" : ?>
                    <div class="information-message error">
                        <p>Пароль меньше чем 6 символов!</p>
                    </div>
                <?php break;?>

                <?php case "7" : ?>
                    <div class="information-message error">
                        <p>Разрешены только цифры в id!</p>
                    </div>
                <?php break;?>

                <?php case "8" : ?>
                    <div class="information-message error">
                        <p>Разрешены только цифры и латинские буквы в имени пользователя!</p>
                    </div>
                <?php break;?>

                <?php case "9" : ?>
                    <div class="information-message error">
                        <p>Разрешены только цифры в ранге или ранг больше 10!</p>
                    </div>
                <?php break;?>

                <?php case "10" : ?>
                    <div class="information-message error">
                        <p>Email больше чем 64 символа!</p>
                    </div>
                <?php break;?>

                <?php case "11" : ?>
                    <div class="information-message error">
                        <p>Email меньше чем 6 символов!</p>
                    </div>
                <?php break;?>

                <?php case "12" : ?>
                    <div class="information-message error">
                        <p>Имя больше чем 64 символа!</p>
                    </div>
                <?php break;?>

                <?php case "13" : ?>
                    <div class="information-message error">
                        <p>Имя меньше чем 5 символов!</p>
                    </div>
                <?php break;?>

                <?php case "14" : ?>
                    <div class="information-message error">
                        <p>Id больше чем 64 символа!</p>
                    </div>
                <?php break;?>

                <?php case "15" : ?>
                    <div class="information-message error">
                        <p>Id меньше чем 6 символов!</p>
                    </div>
                <?php break;?>

                <?php case "16" : ?>
                    <div class="information-message error">
                        <p>Вы не можете добавить пользователя, который старше вас!</p>
                    </div>
                <?php break;?>

            <?php endswitch;?>
        <?php else : ?>

        <!-- End -->

        <div class="information-message error">
            <p>Ошибка при создании пользователя!</p>
        </div>
        <?php endif;?>
    <?php endif;?>

    <?php if($_GET["add_status"] == "success") : ?>
        <div class="information-message success">
            <p>Пользователь добавлен!</p>
        </div>
    <?php endif;?>
    <!-- End -->

    <!-- Support Edit -->
    <?php if($_GET["status"] == "fail") : ?>
        <div class="information-message error">
            <p>Ошибка при записи данных!</p>
        </div>
    <?php endif;?>
    
    <?php if($_GET["status"] == "success") : ?>
        <div class="information-message success">
            <p>Данные изменены!</p>
        </div>
    <?php endif;?>
    <!-- End -->

    <!-- Ban And Unban -->
    <?php if($_GET["unban_status"] == "success") : ?>
        <div class="information-message success">
            <p>Пользователь успешно разбанен!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["unban_status"] == "fail") : ?>
        <div class="information-message error">
            <p>Ошибка при разбане пользователя!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["ban_status"] == "success") : ?>
        <div class="information-message success">
            <p>Пользователь успешно забанен!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["unban_status"] == "fail") : ?>
        <div class="information-message error">
            <p>Ошибка при бане пользователя!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["unban_e"] == "1") : ?>
        <div class="information-message error">
            <p>Вы не можете разбанить сами себя!</p>
        </div>
    <?php endif;?>

    <?php if($_GET["ban_e"] == "1") : ?>
        <div class="information-message error">
            <p>Вы не можете забанить сами себя!</p>
        </div>
    <?php endif;?>
    
<!-- End -->