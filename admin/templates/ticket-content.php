<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
    <div class="main-wrapper">
        <section id="main-container">
            <?php $support_id = $_SESSION['support']['id'];?>
                <?php if(canSupportDo($support_id, "tickets")) : ?>
                <?php if(issetTicket((int)$_GET["ticket_id"]) && thisSupport((int)$_GET["ticket_id"], $support_id)) : ?>
                    <?php if(have_messages((int)$_GET['ticket_id'])) : ?>
                        <!-- Ticket Open-->
                        <?php if(!ticket_open((int)$_GET['ticket_id'])) : ?>
                            <div class="information-message error">
                                <p>Заявка закрыта!</p>
                            </div>
                        <?php endif;?>
                        <!-- End -->
                        <div class="statement-main-messages-container clearfix">
                            <div class="row">   
                                <!-- Messages Wrapper -->
                                <div class="statement-messages-wrapper">
                                <?php foreach(user_messages((int)$_GET['ticket_id']) as $content) : ?>
                                    <!-- User Message -->
                                    <?php if(ticket_open((int)$_GET['ticket_id'])) : ?>  
                                    <div class="open message-wrapper user" id="user-message">
                                    <?php else : ?>
                                        <div class="message-wrapper user" id="user-message">
                                    <?php endif;?>
                                        <div class="user-image">
                                            <img alt="" src="<?php user_avatar($content['user_email'], 50)?>" class="user_image">
                                            <span class="user-<?php echo $content["user"]?>" data-value="<?php echo $content["user"]?>"></span>
                                        </div>
                                        <div class="message" id="<?php echo $content['message_id']?>">
                                            <div class="info-message clearfix">
                                                <span class="user-name"><?php echo $content["user"]?></span><span class="date-message"><?php echo showTime('d.m.Y в H:i', $content['message_time'])?></span>
                                            </div>
                                            <p class="message-text">
                                                <?php echo $content["user_message"]?>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- End User Message -->

                                    <!-- Support Message -->
                                    <?php foreach(support_messages($content["message_id"]) as $sup_content) : ?>

                                    <?php if(ticket_open((int)$_GET['ticket_id'])) : ?>  
                                    <div class="open message-wrapper support" id="support-message">
                                    <?php else : ?>
                                        <div class="message-wrapper support" id="support-message">
                                    <?php endif;?>
                                        <div class="user-image">
                                            <span class="support-<?php echo $sup_content["support_id"]?>" data-value="<?php echo $sup_content["support_id"]?>"></span><img alt="" src="<?php support_avatar($_SESSION['support']['id'], 50)?>" class="user_image" height="50" width="50">
                                        </div>
                                        <div class="message" id="<?php echo $sup_content['message_id']?>">
                                            <div class="info-message clearfix">
                                                <span class="support-name">support_<?php echo $sup_content["support_id"]?></span><span class="date-message"><?php echo showTime('d.m.Y в H:i', $sup_content['message_time'])?></span>
                                            </div>
                                            <p class="message-text">
                                                <?php echo $sup_content["support_message"]?>
                                            </p>
                                        </div>
                                    </div>
                                 <!-- End Support Message -->  
                                    <?php endforeach;?> 
                                <?php endforeach;?>
                                </div><!-- End -->
                            </div>
                            <div class="typing"></div>
                        </div>
                        <!-- End Main -->
                        <?php if(ticket_open((int)$_GET['ticket_id'])) : ?>
                        
                        <div class="wrapper-form">
                            <form id="message-form" class="clearfix">
                                <textarea name="form-message-text" id="form-message-text" cols="100%" rows="10" tabindex="4"></textarea>
                                <input type="hidden" id="ticket-id" name="ticket-id" value = "<?php echo (int)$_GET['ticket_id']?>">
                                <input type="hidden" id="support-id" name="support-id" value = "<?php echo $_SESSION["support"]["id"]?>">
                                <input name="support-message-add" class="btn-large btn btn-block btn-danger" type="submit" id="support-message-add" value="Отправить">
                                <!-- <div class="submit-button clearfix">
                                    <button id="support-message-add"><i class="fa fa-paper-plane fa-2x"></i></button>
                                </div>
                                <div class="clearfix"></div> -->
                            </form>
                        </div>
                    <?php endif;?>
                        <?php else : ?>
                        <!-- No Messages -->
                        <div class="information-message error">
                            <p>Нет сообщений!</p>
                        </div>
                        <!-- Form -->
                    <?php endif;?>
                <?php else:?>
                    <div class="information-message error">
                        <p>Данной заявки нет!</p>
                    </div>
                <?php endif;?>
                    <div id="delete-area" class="delete-area">
                        <div>
                            <div class="delete-area-item">
                                <i class="fa fa-trash-o fa-5x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="drop-overlay"></div>
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
    <div class="clearfix"></div> 
<?php get_footer_template("", true)?>      
           