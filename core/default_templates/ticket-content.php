<?php get_header_template()?>
<?php global $user;?>
<section id="main-content">
    <div id="chat-wrapper">
    <?php if(!is_logged()) : ?> 
        <div class="please-login">
            <p>Пожалуйста, <a class="in-link" href="?action=login">авторизируйтесь</a> для дальнейшей работы!</p>
        </div>
    <?php else : ?>
        <?php if(issetTicket((int)$_GET["ticket_id"]) && thisUser((int)$_GET["ticket_id"] ,$user)) : ?>
            <?php if(have_messages((int)$_GET['ticket_id'])) : ?>
            <!-- Button for close ticket -->
            <?php if(ticket_open((int)$_GET['ticket_id'])) : ?>
            <div id="dialog">
                <div id="dialog-window">
                    <a class="close-ticket">&times;</a>
                    <p>Вы действительно хотите закрыть заявку?</p>
                    </br>
                    <a href="?action=close_ticket&amp;ticket_id=<?php echo (int)$_GET['ticket_id']?>" id="submit_ticket_close_but" class="yes btn btn-block btn-primary">Да</a>
                    <a href="" id="submit_ticket_close_but" class="no btn btn-block btn-danger">Нет</a>
                </div>
            </div>
            <div id="submit_ticket_close">
                <a href="#" id="submit_ticket_close_link" class="btn btn-block btn-danger">Закрыть заявку</a>
            </div>
            <?php else : ?>
                <div class="error-box no-margin">
                    <p>Тикет закрыт!</p>
                </div>
                <div class="clear"></div>
            <?php endif;?>
            <!-- End -->
            <div id="messages-wrapper">
            <?php foreach(user_messages((int)$_GET['ticket_id']) as $content) : ?>
                <!-- Messages Wrapper Start-->  
                    <!-- User Message -->
                    <div id="user-message" class="box-message">
                        <img alt="" src="<?php user_avatar($_SESSION["email"], 50)?>" class="user_image" >
                        <div class="info-chat">
                            <span class="user-name chat"><?php echo $content["user"]?></span><span class="date_message"><?php echo showTime('d.m.Y в H:i', $content['message_time'])?></span>
                        </div> 
                        <p class="message">
                            <?php echo $content["user_message"]?>
                        </p>                 
                    </div>
                    <!-- User Message End -->
                    <!-- Support Message -->
                    <?php foreach(support_messages($content["message_id"]) as $sup_content) : ?>
                    <div id="support-message" class="box-message message-id-<?php echo $sup_content["message_id"]?>">
                        <img alt="" src="<?php support_avatar($sup_content['support_id'], 50)?>" class="user_image" height="50" width="50">
                        <div class="info-chat">
                            <span class="support-name chat">support_<?php echo $sup_content["support_id"]?></span><span class="date_message"><?php echo showTime('d.m.Y в H:i', $sup_content['message_time'])?></span>
                        </div> 
                        <p class="support-message">
                            <?php echo $sup_content["support_message"]?>
                        </p>
                    </div>
                    <!-- Support Message End -->
                <?php endforeach;?>
                <!-- Messages Wrapper End-->    
            <?php endforeach;?>
            </div>
            <?php if(!is_logged()) : ?> 
                <div class="please-login">
                    <p>Пожалуйста, <a class="in-link" href="/?action=login">авторизируйтесь</a> для дальнейшей работы!</p>
                </div>
            <?php else : ?>
                <?php if(ticket_open((int)$_GET['ticket_id'])) : ?>
                    <div id="form-text">
                        <form id="commentform" method="POST">
                            <span class="typing"></span>
                            <p class="in-system">Вы вошли как <span class="user-name form"><?php echo $user?></span>. <a class="logout-form" href="?action=logout">Выйти</a></p>
                            <textarea placeholder="Введите сообщение" name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea>
                            <input type="hidden" name="ticket_id" value = "<?php (int)$_GET['ticket_id']?>">
                            <input type="hidden" name="user" id="user-name-in-ticket" value ="<?php echo $user?>">
                            <input name="submit_message" class="btn-large btn btn-block btn-default" type="submit" id="submit_message" value="Отправить">
                        </form>
                    </div>
                <?php endif;?>
            <?php endif;?>
            <?php else:?>
            <!-- No Messages -->
                <div class="error-box no-margin">
                    <p>У вас нет сообщений!</p>
                </div>
                <div id="form-text">
                        <form id="commentform" method="POST">
                            <span class="typing"></span>
                            <p class="in-system">Вы вошли как <span class="user-name form"><?php echo $user?></span>. <a class="logout-form" href="?action=logout">Выйти</a></p>
                            <textarea placeholder="Введите сообщение" name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea>
                            <input type="hidden" name="ticket_id" value = "<?php (int)$_GET['ticket_id']?>">
                            <input type="hidden" name="user" id="user-name-in-ticket" value = "<?php echo $user?>">
                            <input name="submit_message" class="btn-large btn btn-block btn-default" type="submit" id="submit_message" value="Отправить">
                        </form>
                    </div>
            <?php endif;?>
        <?php else : ?>
            <div class="error-box no-margin">
                <p>Данной заявки нет!</p>
            </div>
        <?php endif;?>
    <?php endif;?>
    </div>
</section>
<script>
    var key = "<?php echo auth_user()?>";
</script>
<?php get_footer_template()?>