<?php get_header_template()?>
<?php global $user;?>
<section id="main-content">
    <!-- User Statements Start -->
    <div id="user-statements-wrapper">
        <?php if(!is_logged()) : ?> 
            <div class="please-login">
                <p>Пожалуйста, <a class="in-link" href="?action=login">авторизируйтесь</a> для дальнейшей работы!</p>
            </div>
        <?php else : ?>
            <div class="create_ticket">
                <a href="?action=create_ticket" id="create_ticket_button" class="btn-large btn btn-block btn-primary">Создать заявку</a>
            </div>
            <div class="row">	
                <?php if(have_user_tickets($user)) : ?>
                    <?php if($user == "anonymous") : ?>
                        <?php if(isset($_COOKIE["tickets"]) && !empty($_COOKIE["tickets"])) : ?>
                            <?php foreach($_COOKIE["tickets"] as $a_value) : ?>
                                <?php $content = getTicketById($a_value)?>
                                    <div class="col-md-4">
                                        <a href="?ticket_id=<?php echo $content['ticket_id']?>">
                                            <div id="user-statements">
                                                <h2 class="statement-name">Заявка #<?php echo $content['ticket_id']?></h2>
                                                <hr>
                                                <div class="statement-info">
                                                    <span class="display-block"><span class="width">Статус:</span><?php ticket_status($content['ticket_id'])?></span>
                                                    <span class="display-block"><span class="width">Создан:</span><span class="statement-was-create"><?php echo showTime('d.m.Y в H:i', $content['time'])?></span></span>
                                                    <span class="display-block"><span class="width">Агент:</span><span class="statement-support"><?php echo $content['support_id']?></span></span>
                                                    <span class="display-block"><span class="width">Категория:</span><span class="statement-category"><?php echo $content['category']?></span></span>
                                                    <span class="display-block"><span class="width">Название:</span><span class="statement-theme"><?php echo $content['ticket_name']?></span></span>
                                                </div>
                                                <hr>
                                                <div class="statement-user-first-message">
                                                    <img class="user_image" alt=" " src="<?php user_avatar('friendscraft@yandex.ru')?>">                  
                                                        <p><?php first_message_from_ticket($content['ticket_id'])?></p>
                                                </div>                            
                                            </div>
                                        </a>
                                    </div>
                            <?php endforeach;?>
                        <?php else : ?>
                            <div class="warning-no-top">
                                <p>Все заявки пользователя "anonymous"!</p>
                            </div>
                            <?php foreach(user_tickets($user) as $content) : //the_post();?>
                                <div class="col-md-4">
                                    <a href="?ticket_id=<?php echo $content['ticket_id']?>">
                                        <div id="user-statements">
                                            <h2 class="statement-name">Заявка #<?php echo $content['ticket_id']?></h2>
                                            <hr>
                                            <div class="statement-info">
                                                <span class="display-block"><span class="width">Статус:</span><?php ticket_status($content['ticket_id'])?></span>
                                                <span class="display-block"><span class="width">Создан:</span><span class="statement-was-create"><?php echo showTime('d.m.Y в H:i', $content['time'])?></span></span>
                                                <span class="display-block"><span class="width">Агент:</span><span class="statement-support"><?php echo $content['support_id']?></span></span>
                                                <span class="display-block"><span class="width">Категория:</span><span class="statement-category"><?php echo $content['category']?></span></span>
                                                <span class="display-block"><span class="width">Название:</span><span class="statement-theme"><?php echo $content['ticket_name']?></span></span>
                                            </div>
                                            <hr>
                                            <div class="statement-user-first-message">
                                                <img class="user_image" alt=" " src="<?php user_avatar('friendscraft@yandex.ru')?>">                  
                                                    <p><?php first_message_from_ticket($content['ticket_id'])?></p>
                                            </div>                            
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach;?>
                        <?php endif;?>
                    <?php else : ?>
                    <!--Not Anonym-->
                        <?php foreach(user_tickets($user) as $content) : //the_post();?>
                            <div class="col-md-4">
                                <a href="?ticket_id=<?php echo $content['ticket_id']?>">
                                    <div id="user-statements">
                                        <h2 class="statement-name">Заявка #<?php echo $content['ticket_id']?></h2>
                                        <hr>
                                        <div class="statement-info">
                                            <span class="display-block"><span class="width">Статус:</span><?php ticket_status($content['ticket_id'])?></span>
                                            <span class="display-block"><span class="width">Создан:</span><span class="statement-was-create"><?php echo showTime('d.m.Y в H:i', $content['time'])?></span></span>
                                            <span class="display-block"><span class="width">Агент:</span><span class="statement-support"><?php echo $content['support_id']?></span></span>
                                            <span class="display-block"><span class="width">Категория:</span><span class="statement-category"><?php echo $content['category']?></span></span>
                                            <span class="display-block"><span class="width">Название:</span><span class="statement-theme"><?php echo $content['ticket_name']?></span></span>
                                        </div>
                                        <hr>
                                        <div class="statement-user-first-message">
                                            <img class="user_image" alt=" " src="<?php user_avatar('friendscraft@yandex.ru')?>">                  
                                                <p><?php first_message_from_ticket($content['ticket_id'])?></p>
                                        </div>                            
                                    </div>
                                </a>
                            </div>
                        <?php endforeach;?>    
                    <?php endif;?>
                <?php else:?>
                    <!-- No Tickets -->
                    <div class="error-box">
                        <p>У вас нет открытых заявок!</p>
                    </div>
                <?php endif;?>
            </div>
        <?php endif;?>      
    </div>

    <!-- User Statements End -->
</section>
<?php get_footer_template()?>