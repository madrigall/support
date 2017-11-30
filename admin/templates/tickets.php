<?php get_header_template("", true)?>
	<?php get_sidebar_template("left", true)?>
        <div class="main-wrapper" id="wrapper">
            <section id="main-container">
            <?php if(empty($_GET["filter"])) : ?>
           		<?php $filter = "all" ?>
        	<?php else : ?>
        		<?php $filter = (string)$_GET["filter"]?>
        	<?php endif;?>
            <?php $page = (int) $_GET['page']?>   
            <?php $support_id = $_SESSION['support']['id'];?>
            <?php $count = countPosts($support_id, $filter)?>
            <?php $posts = $count[0]?>
        	<?php $total = (int) ((($posts - 1) / TICKETS_AMOUNT) + 1)?>
        	
        	<?php if(empty($page) || $page <= 0) $page = 1?>
        	<?php if($page > $total) $page = $total?>

        	<?php $start = $page * TICKETS_AMOUNT - TICKETS_AMOUNT?>

        	<?php if($start == "-1" || $start < "-1") : ?>
        		<?php $start = 0?>
        	<?php endif;?>	

            <?php if(canSupportDo($support_id, "tickets")) : ?>  
            	<?php $a = parse_url("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])?>

	            <?php if(have_support_tickets($_SESSION["support"]["id"])) : ?>       	
	            	<div class="filter-statement controls">
						<!--
	 					<ul>
							<li class="filter active" data-filter="all">Все категории</li>
							<li class="filter" data-filter=".category-1">Общие вопросы</li>
							<li class="filter" data-filter=".category-2">Проблемы с клиентом</li>
							<li class="filter" data-filter=".category-3">Технические вопросы</li>
							<li class="filter" data-filter=".category-4">Учетная запись</li>
						</ul>
						 -->
						<ul>
							<?php if($filter == "all") : ?>
								<a href="?mode=tickets&amp;filter=all"><li class="filter active">Все заявки</li></a>
							<?php else : ?>
								<a href="?mode=tickets&amp;filter=all"><li class="filter">Все заявки</li></a>
							<?php endif;?>

							<?php if($filter == "waiting") : ?>
								<a href="?mode=tickets&amp;filter=waiting"><li class="filter active">Нуждаются в ответе</li></a>
							<?php else : ?>
								<a href="?mode=tickets&amp;filter=waiting"><li class="filter">Нуждаются в ответе</li></a>
							<?php endif;?>

							<?php if($filter == "open") : ?>
								<a href="?mode=tickets&amp;filter=open"><li class="filter active">Открытые</li></a>
							<?php else : ?>
								<a href="?mode=tickets&amp;filter=open"><li class="filter">Открытые</li></a>
							<?php endif;?>

							<?php if($filter == "close") : ?>
								<a href="?mode=tickets&amp;filter=close"><li class="filter active">Закрытые</li></a>
							<?php else : ?>
								<a href="?mode=tickets&amp;filter=close"><li class="filter">Закрытые</li></a>
							<?php endif;?>						
						</ul>
					</div>

					<div class="statement-container">
						<div class="row">
							<?php $content = getTicketsToSupport($_SESSION["support"]["id"], $filter, $start)?>
							<?php if(!empty($content)) : ?>
								<?php foreach($content as $tickets) : ?>
									<div class="col-md-4">
										<a href="?ticket_id=<?php echo $tickets["ticket_id"]?>">
										<div class="main-statement-block <?php ticket_status_admin($tickets['ticket_id'])?>" >
											<h2 class="statement-id">Заявка #<?php echo $tickets["ticket_id"]?></h2>
											<hr class="statemet">
											<div class="statement-information">
												<div class="statement-category">
													<i class="fa fa-pencil"></i>
													<span class="information-text"><?php echo $tickets["ticket_name"]?></span>
												</div>
												<div class="statement-category">
													<i class="fa fa-tag"></i>
													<span class="information-text"><?php echo $tickets["category"]?></span>
												</div>
												<div class="statement-info">
													<i class="fa fa-info-circle"></i>
													<span class="information-text"><?php ticket_status_admin($tickets['ticket_id'], true)?></span>
												</div>
												<div class="statement-date">
													<i class="fa fa-clock-o"></i>
													<span class="information-text"><?php echo showTime('d.m.Y', $tickets['time'])?></span>
												</div>
												<div class="statement-user">
													<i class="fa fa-user"></i>
													<span class="information-text"><?php echo $tickets["user"]?></span>
												</div>

											</div>
											<hr class="statemet-2">
											<!-- Ticket Content-->
											<div class="statement-user-first-message">
			                            		<img class="radius image-left" alt=" " src="http://www.gravatar.com/avatar/9a54bcaf1baece57756015a9320ec294?s=32&amp;d=identicon&amp;r=g">                  
			                                	<p class="message-from-ticket">
			                                		<?php echo first_message_from_ticket($tickets["ticket_id"])?>
			                                	</p>
			                        		</div>
											<!-- End -->
										</div><!-- main-statement-block -->
										</a>
									</div><!-- col-md-4 -->
								<?php endforeach;?>
								<?php else : ?>
									<div class="information-message warning margin-15">
					                	<p>В данной сортировке заявок нет!</p>
					            	</div>
								<?php endif;?>


							<?php else:?>
					            <!-- No Tickets -->
					            <div class="information-message error">
					                <p>У вас нет открытых тикетов!</p>
					            </div>
				        	<?php endif;?>

						</div>
					</div><!-- container -->
				<nav class="pagination">
		            <ul>
		            	<?php if(($page - 1) > 0) : ?>
			            	<li class="previous"><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page - 1?>"><i class="fa fa-chevron-left"></i></a></li>
		             	<?php else : ?>
		             		<li class="previous disable"><i class="fa fa-chevron-left"></i></li>
		             	<?php endif;?>

		             	<?php if(($page - 5) > 1) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=1">1</a></li>
		             		<?php if(($page - 6) > 1) : ?>
		             			<li class="active"><a href="">...</a></li>
		             		<?php endif;?>
		             	<?php endif;?>

		             	<?php if(($page - 5) > 0) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page - 5?>"><?php echo $page - 5?></a></li>
		             	<?php endif;?>
		             	<?php if(($page - 4) > 0) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page - 4?>"><?php echo $page - 4?></a></li>
		             	<?php endif;?>
		             	<?php if(($page - 3) > 0) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page - 3?>"><?php echo $page - 3?></a></li>
		             	<?php endif;?>
		             	<?php if(($page - 2) > 0) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page - 2?>"><?php echo $page - 2?></a></li>
		             	<?php endif;?>
		             	<?php if(($page - 1) > 0) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page - 1?>"><?php echo $page - 1?></a></li>
		             	<?php endif;?>

		             	<li class="active"><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page?>"><?php echo $page?></a></li>
		             	<?php if(($page + 1) <= $total) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page + 1?>"><?php echo $page + 1?></a></li>
		             	<?php endif;?>
		             	<?php if(($page + 2) <= $total) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page + 2?>"><?php echo $page + 2?></a></li>
		             	<?php endif;?>
		             	<?php if(($page + 3) <= $total) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page + 3?>"><?php echo $page + 3?></a></li>
		             	<?php endif;?>
		             	<?php if(($page + 4) <= $total) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page + 4?>"><?php echo $page + 4?></a></li>
		             	<?php endif;?>
		             	<?php if(($page + 5) <= $total) : ?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page + 5?>"><?php echo $page + 5?></a></li>
		             	<?php endif;?>

		             	<?php if(($page + 5) < $total) : ?>
		             		<?php if(($page + 6) < $total) : ?>
		             			<li class="active"><a href="">...</a></li>
		             		<?php endif;?>
		             		<li><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $total?>"><?php echo $total?></a></li>
		             	<?php endif;?>

			            <?php if(($page + 1) <= $total) : ?>
			            	<li class="next"><a href="?mode=tickets&amp;filter=<?php echo $filter?>&amp;page=<?php echo $page + 1?>"><i class="fa fa-chevron-right"></i></a></li>
		             	<?php else : ?>
		             		<li class="next disable"><i class="fa fa-chevron-right"></i></li>
		             	<?php endif;?>
		            </ul>
				</nav>
				<?php else : ?>
					<div id="message-error-rank" class="modal-overlay error-info">
						<div class="modal-content animated bounce">
							<button class="modal-action" data-modal-close="true"><i id="modal-button" class="fa fa-times fa-lg"></i></button>
							<p>У вас недостаточно прав для просмотра этой страницы!</p>
							<br>
							<p><a href="?logout=true">Сменить пользователя</a></p>
						</div>
					</div>
				<?php endif; ?>
            </section>
        </div>
<?php get_footer_template("", true)?>