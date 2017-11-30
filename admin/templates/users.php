<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
        <div class="main-wrapper">
            <section id="main-container">
            	<?php $support_id = $_SESSION['support']['id'];?>
            	<?php if(canSupportDo($support_id, "users")) : ?>
	            	<?php include_admin_template("information-messages"); ?>
	            	<!-- <h2 class="u-add-support">Пользователи</h2> -->
	            	<?php if(!defined("CUSTOM_FOLDER")) : ?>
	            		<form method="POST" action="/admin/?mode=users">
	            	<?php else : ?>
	            		<form method="POST" action="<?php echo CUSTOM_FOLDER?>admin/?mode=users">
	            	<?php endif;?>
	            	<div class="u-action-panel">
		            		<select name="select_action">
		            			<option value="1">Изменить</option>
		            			<option value="2">Удалить</option>
		            		</select><i class="select-arrow fa fa-arrow-down"></i>
		            		<input type="submit" id="doaction" class="btn btn-wide btn-primary mrm" value="Применить">
	            	</div>
					<table id="users-table">
			     		<thead>
			     			<tr class="table_content">
			     				<th class="check-box">
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
			    					<input name="support_edit['<?php echo $i?>']" class="checkbox-users checkbox-u" type="checkbox" value="<?php echo $users['support_id']?>">
			    				</th>
			    				<td class="table_support_id"><?php echo $users["support_id"]?></td>
			    				<?php if(!isSupportBanned($users['support_id'])) : ?>
			    					<td class="table_support_name"><?php echo $users["support_name"]?></td>
			    				<?php else : ?>
			    					<td class="table_support_name"><s><?php echo $users["support_name"]?></s></td>
			    				<?php endif;?>
			    				<td class="table_support_rank"><?php echo $users["support_rank"]?></td>
			    				<?php if(!isSupportBanned($users['support_id'])) : ?>
			    					<td class="table_support_action"><a class="change" href="?support_edit=<?php echo $users['support_id']?>" title="Изменить"><i class="fa fa-pencil fa-lg"></i></a><a class="delete change" href="?mode=users&amp;delete_support=<?php echo $users['support_id']?>" title="Удалить"><i class="fa fa-trash fa-lg"></i></a><a class="delete" href="?mode=users&amp;ban=<?php echo $users['support_id']?>" title="Забанить"><i class="fa fa-lock fa-lg"></i></a></td>
		 			    		<?php else : ?>
		 			    			<td class="table_support_action"><a class="change" href="?support_edit=<?php echo $users['support_id']?>" title="Изменить"><i class="fa fa-pencil fa-lg"></i></a><a class="delete change" href="?mode=users&amp;delete_support=<?php echo $users['support_id']?>" title="Удалить"><i class="fa fa-trash fa-lg"></i></a><a class="delete" href="?mode=users&amp;unban=<?php echo $users['support_id']?>" title="Разбанить"><i class="fa fa-unlock fa-lg"></i></a></td>
		 			    		<?php endif;?>
		 			    	</tr>
		 			    	<?php $i++?>
				     	<?php endforeach;?>
				     	</tbody>
			 			<tfoot>
			 				<tr class="table_content">
			 					<th class="check-box">
			 						<input class="select-all-users foot checkbox-u" type="checkbox">
			 					</th>
			 					<th>ID</th>
			 					<th>Имя</th>
			 					<th>Ранг</th>
			 					<th>Действие</th>
			 				</tr>
			 			</tfoot>
		 			</table>
		 		</form>

		 		<?php if(canSupportDo($support_id, "add-users")) : ?>
			 		<!-- Add User -->
			 		<h2 class="u-add-support">Добавление пользователя</h2>
			 		<?php $support_to_add_id = rand(10000, 99999);?>
			 		<form id="u-add-support-v" class="margin-10" method="POST" action="?mode=users&amp;users-action=add">
			 			<table id="u-add-support">
				 			<tr>
								<td><label class="label-text" for="new_support_id">ID</label></td>
								<td><input placeholder="Введите ID" type="text" data-validation-engine="validate[required, custom[integer]]" class="input margin-right-0" id="new_support_id" name="new_support_id" placeholder="Введите id пользователя" value="<?php echo $support_to_add_id?>"></td>
							</tr>
							<tr>
						  		<td><label class="label-text" for="new_support_name">Имя</label></td>
						 		<td><input placeholder="Введите имя" type="text" data-validation-engine="validate[required, custom[sup_name]]" class="input margin-right-0" id="new_support_name" name="new_support_name" placeholder="Введите имя пользователя" value="support_<?php echo $support_to_add_id?>"></td>
					  		</tr>
				  			<tr>
				  				<td><label class="label-text" for="new_support_rank">Ранг</label></td>
				  				<td><input placeholder="Введите ранг" type="text" data-validation-engine="validate[required,custom[integer]]" class="input margin-right-0" id="new_support_rank" name="new_support_rank" placeholder="Введите ранг пользователя" value="<?php echo DEFAULT_RANK?>"></td>
				  			</tr>
				  			<tr>
				  				<td><label class="label-text" for="new_support_email">Email</label></td>
				  				<td><input placeholder="Введите email" type="text" data-validation-engine="validate[required,custom[email]]" class="input margin-right-0" id="new_support_email" name="new_support_email" placeholder="Введите email пользователя" value=""></td>
				  			</tr>
				  			<tr>	
				  				<td><label class="label-text" for="new_support_pass">Пароль</label></td>
				  				<td><input placeholder="Введите пароль" type="password" data-validation-engine="validate[required]" class="input margin-right-0" id="new_support_pass" name="new_support_pass" placeholder="Введите пароль пользователя"></td>
				  			</tr>
				  			<tr>
				  				<td colspan="2">
				  					<input type="submit" id="u-add-support-btn" class="btn btn-wide btn-primary lrg" value="Сохранить">
			  					</td>
			  				</tr>
			  			</table>
			 		</form>
		 		<?php endif;?>
		 		
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