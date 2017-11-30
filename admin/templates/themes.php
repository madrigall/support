<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
        <div class="main-wrapper">
            <section id="main-container">
            	<?php if($_GET["status"] == "success") : ?>
            		<div class="information-message success">
            			<p>Тема активирована!</p>
            		</div>
            	<?php endif;?>
            	<?php if($_GET["status"] == "fail") : ?>
            		<div class="information-message error">
            			<p>Ошибка при активации темы!</p>
            		</div>
            	<?php endif;?>

				<?php $support_id = $_SESSION['support']['id'];?>
				<?php if(canSupportDo($support_id, "themes")) : ?>
				<?php $res = scan_templates_dir()?>
				<div class='catalog-themes'>
					<?php if(empty($res)) : ?>
						<div class="information-message error">
			                <p>Темы отсутствуют!</p>
			            </div>
					<?php else : ?>
					<div class="row">	
						<?php foreach(scan_templates_dir() as $theme_name) : ?>
							<?php if(CONTENT . $theme_name . "/" == ACTIVE_TEMPLATE) : ?>
								<div class="col-md-4">
									<div class='theme clearfix'>
						                <h2 class='theme-name active-theme'>Тема <?php echo $theme_name ?></h2>	                
						                <div class='theme-picture'>
						                    <div class='theme-content'>
						                        <img class='no-hovered' src='<?php echo themePicture($theme_name)?>' alt='Тема <?php echo $theme_name ?>'>
						                    </div>
						                  	<div class="theme-information">
												<?php if(CONTENT . $theme_name . "/" == ACTIVE_TEMPLATE) : ?>
						                    		<p>Эта тема уже активирована!</p>
						                    	<?php else : ?>
						                    		<p><?php echo $theme_information?></p>
									                <a href='?active= <?php echo $theme_name?>' class='link-active'>Активировать</a>
						                    	<?php endif;?>
											</div>
						                </div>
					                </div>
						        </div>
						    	<?php endif;?>
							<?php endforeach; ?>

							<?php foreach(scan_templates_dir() as $theme_name) : ?>
								<?php if(CONTENT . $theme_name . "/" == ACTIVE_TEMPLATE) : ?>
									<?php continue;?>
								<?php endif;?>
								<div class="col-md-4">
									<div class='theme clearfix'>
						                <h2 class='theme-name'>Тема <?php echo $theme_name ?></h2>	                
						                <div class='theme-picture'>
						                    <div class='theme-content'>
						                        <img class='no-hovered' src='<?php echo themePicture($theme_name)?>' alt='Тема <?php echo $theme_name ?>'>
						                    </div>
						                  	<div class="theme-information">
												<?php if(CONTENT . $theme_name . "/" == ACTIVE_TEMPLATE) : ?>
						                    		<p>Эта тема уже активирована!</p>
						                    	<?php else : ?>
						                    		<p><?php echo $theme_information?></p>
									                <a href='?active= <?php echo $theme_name?>' class='link-active'>Активировать</a>
						                    	<?php endif;?>
											</div>
						                </div>
					                </div>
						        </div>
							<?php endforeach; ?>
						</div>
						<?php endif;?>
					</div>

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
