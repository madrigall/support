<?php get_header_template()?>
<?php global $user;?>
<section id="main-content">
	<div id="ticket_create_wrapper">
		<?php if(is_logged()) : ?>
			<!-- Ticket Create Start -->
			<form method="post" action="" id="firstStep">
				<ol>
					<li>
						<h2 class="create-info">1. Определите категорию проблемы</h2>
						<?php foreach(support_categories() as $categories) : ?>
							<input id="category" name="category" type="radio" class="radio" checked><span class="radio"><?php echo $categories["name"]?></span>
						<?php endforeach;?>
					</li>

					<li>
						<h2 class="create-info">2. Напишите название</h2>
						<!--<label for="name">Название</label>-->
						<input id="ticket_name" type="text" class="input" name="ticket_name" placeholder="Введите заголовок заявки">
					</li>

					<li>
						<h2 class="create-info">3. Ваше сообщение</h2>
						<textarea placeholder="Введите вопрос" name="user_message" id="user_message_ticket" cols="100%" rows="10" tabindex="4"></textarea>
					</li>

					<br/>
					<input type="hidden" name="user" id="user-name-in-ticket" value ="<?php echo $user?>">
					<!--<div class="g-recaptcha" data-sitekey="<?php echo CAPTCHA_KEY?>"></div>-->
	                <br> 
					<input name="submit_ticket" class="btn-large btn btn-block btn-primary" type="submit" id="submit_ticket" value="Отправить">
				</ol>
			</form>
			
			<!-- Ticket Create End -->
		<?php else : ?>
			<div class="please-login">
			    <p>Пожалуйста, <a class="in-link" href="?action=login">авторизируйтесь</a> для дальнейшей работы!</p>
			</div>
		<?php endif; ?>
	</div>
</section>
<script>
    var key = "<?php echo auth_user(true)?>";
</script>
<?php get_footer_template()?>