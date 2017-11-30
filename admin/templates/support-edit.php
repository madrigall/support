<?php
if(isset($_POST["submit_edit_user"]) && canSupportDo($_SESSION["support"]["id"], "edit-users"))
{
	global $coreLog;
	
	$error_string = false;
	$support_id = $_POST['edit_sup_id'];
    $support_name = $_POST['edit_sup_name'];
    $support_rank = $_POST['edit_sup_rank'];
    $support_pass = $_POST['edit_sup_pass'];
    $support_email = $_POST['edit_sup_email'];
    $support_edit = $_POST['edit_sup_h'];

    

    for($j = 0; $j < count($support_id); $j++)
    {

    	if(findDublicateId($support_id[$j], true))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - существующий id!");

			header("Location: ?mode=users&add_status=fail&fail_id=1");
			exit();
		}
		
		if(findDublicateName($support_name[$j], true))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - существующее имя!");

			header("Location: ?mode=users&add_status=fail&fail_id=2");
			exit();
		}
		
		if(findDublicateEmail($support_email[$j], true))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - существующий email!");

			header("Location: ?mode=users&add_status=fail&fail_id=3");
			exit();
		}
		
		if(strlen($support_id[$j]) > 64)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - id больше чем 64 символа!");

			header("Location: ?mode=users&add_status=fail&fail_id=14");
			exit();
		}

		if(strlen($support_id[$j]) < 5)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - id меньше 5 символов!");

			header("Location: ?mode=users&add_status=fail&fail_id=15");
			exit();
		}

		if($support_name[$j])
		{
			if(strlen($support_name[$j]) > 64)
			{
				$error_string = true;

				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - имя больше чем 64 символа!");

				header("Location: ?mode=users&add_status=fail&fail_id=12");
				exit();
			}
			
			if(strlen($support_name[$j]) < 5)
			{
				$error_string = true;

				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - имя меньше 5 символов!");

				header("Location: ?mode=users&add_status=fail&fail_id=13");
				exit();
			}

			if(!preg_match('#^[a-zA-Z0-9_]*$#i', $support_name[$j]) || empty($support_name[$j]))
			{
				$error_string = true;

				$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - некорректное имя!");

				header("Location: ?mode=users&add_status=fail&fail_id=8");
				exit();
			}
		}

		if (!preg_match("/[0-9A-Za-z_\.\-]+@[0-9a-z_\.\-]+\.[a-z]{2,4}/i", $support_email[$j]) || empty($support_email[$j]))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - некорректный email!");

			header("Location: ?mode=users&add_status=fail&fail_id=4");
			exit();
		}
		
		if(strlen($support_email[$j]) > 64)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - email больше чем 64 символа!");

			header("Location: ?mode=users&add_status=fail&fail_id=10");
			exit();
		}	

		if(strlen($support_email[$j]) < 10)
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - email меньше чем 10 символов!");

			header("Location: ?mode=users&add_status=fail&fail_id=11");
			exit();
		}
		
		//TODO: Javascript RegExp
		if(!preg_match("#^[0-9]*$#i", $support_id[$j]) || empty($support_id[$j]))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - некорректный id!");

			header("Location: ?mode=users&add_status=fail&fail_id=7");
			exit();
		}

		if(!preg_match('#^[0-9]*$#i', $support_rank[$j]) || $support_rank[$j] > 10 || empty($support_rank[$j]))
		{
			$error_string = true;

			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - некорректный ранг!");

			header("Location: ?mode=users&add_status=fail&fail_id=9");
			exit();
		}
    }
    
    if(!$error_string)
	    for($j = 0; $j < count($support_id); $j++)
	    {
	    	$support_to_edit_get = parseGetParameters((int) $_GET['support_edit']);

	    	if(empty($support_to_edit_get))
	    		$support_to_edit = $support_edit[$j];
	    	else
	    		$support_to_edit = $support_to_edit_get;


			
	    	global $db;
	    	$sql = "";

	    	if($support_pass[$j])
	    	{
	    		$support_pass[$j] = parsePass($support_pass[$j]);
	    		if(strlen($support_pass[$j]) > 64)
	    		{
	    			$error_string = true;

	    			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - пароль больше чем 64 символа!");

	    			header("Location: ?mode=users&add_status=fail&fail_id=5");
	    			exit();
	    		}
	    		
	    		if(strlen($support_pass[$j]) < 6)
	    		{
	    			$error_string = true;

	    			$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при редактировании пользователя ($support_name id:$support_id email:$support_email rank: $support_rank) - пароль меньше чем 6 символов!");

	    			header("Location: ?mode=users&add_status=fail&fail_id=6");
	    			exit();
	    		}	

	    		if($support_name[$j])
					$sql = "UPDATE support_agents SET support_id =:support_id, support_name = :support_name, support_email = :support_email, support_pass = :support_pass, support_rank = :support_rank WHERE support_id = :support_to_edit";
	    		else
	    			$sql = "UPDATE support_agents SET support_id =:support_id, support_pass = :support_pass, support_email = :support_email, support_rank = :support_rank WHERE support_id = :support_to_edit";
	    	}
	    	else
	    		if($support_name[$j])
					$sql = "UPDATE support_agents SET support_id =:support_id, support_name = :support_name, support_email = :support_email, support_rank = :support_rank WHERE support_id = :support_to_edit";
				else
					$sql = "UPDATE support_agents SET support_id =:support_id, support_email = :support_email, support_rank = :support_rank WHERE support_id = :support_to_edit";




		    $stmt = $db->prepare($sql);

		    $stmt->bindValue(':support_id', $support_id[$j]);

		    if($support_name[$j])
		    	$stmt->bindValue(':support_name', $support_name[$j]);

		    $stmt->bindValue(':support_rank', $support_rank[$j]);
		    $stmt->bindValue(':support_email', $support_email[$j]);
		    
		    if($support_pass[$j])
		    {
		    	$support_pass_h = passHash($support_pass[$j]);
		    	$stmt->bindValue(':support_pass', $support_pass_h);
			}

			$stmt->bindValue(':support_to_edit', $support_to_edit);
		    $stmt->execute();
			
		    if($stmt)
		    {
		    	$coreLog->write("{$_SESSION["support"]["name"]} изменил данные {$support_name[$j]} (редактирование)!");

		    	$success = true;
	        }
	        else
	        {
	        	$coreLog->write("{$_SESSION["support"]["name"]} получил ошибку при изменении данных $support_name[$j]!");

	        	$success = false;
	        }
		}//for

	    if($success)
	    {
        	header("Location: ?mode=users&status=success");
        	exit();
        }
        else
        {
    		header("Location: ?mode=users&status=fail");
    		exit();
        }
}

?>
<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
        <div class="main-wrapper">
            <section id="main-container">
            	<?php $support_id = $_SESSION['support']['id'];?>
            	<?php if(canSupportDo($support_id, "edit-users")) : ?>
	            	<?php if(!empty($error_string)) : ?>
	            		<div class="information-message error">
	            			<p><?php echo $error_string?></p>
	            		</div>
	            	<?php endif;?>
	            	<?php if(!defined("CUSTOM_FOLDER")) : ?>
	            		<form action="/admin/?mode=users" id="support_edit_form" method="POST">
	            	<?php else : ?>
	            		<form action="<?php echo CUSTOM_FOLDER?>admin/?mode=users" id="support_edit_form" method="POST">
	            	<?php endif;?>
	            	
	            	<table>
	            	<?php $i = 0?>
	            	<?php if($_POST["support_edit"]) : ?>
			           	<?php foreach($_POST["support_edit"] as $support) : ?>
				           	<?php $support_to_edit = parseGetParameters((int) $support);?>
				           	<?php $support_info = get_support($support_to_edit)?>
				           	<tr>
				           		<td></td>
				           		<td></td>
				           		<td>
				           			<br>
				           			<h2 class="support-to-edit"><?php echo $support_info['support_name']?></h2>
				           			<br>
				           		</td>
				           	</tr>
				                <tr>
									<td><label class="label-text" for="edit_sup_id">ID</label></td>
									<td><input placeholder="Введите ID" type="text" class="input" data-validation-engine="validate[required, custom[integer]]" id="edit_sup_id" name="edit_sup_id[<?php echo $i?>]" value="<?php echo $support_info['support_id']?>"><input type="hidden" class="input" id="edit_sup_h" name="edit_sup_h[<?php echo $i?>]" value="<?php echo $support_info['support_id']?>"></td>
								</tr>
							<?php if($support_to_edit == $_SESSION['support']['id'] || $support_info['support_name'] == $_SESSION['support']['name']) : ?>
								<tr>
									<td><label class="label-text" for="edit_sup_name">Имя</label></td>
									<td><input placeholder="Введите имя" type="text" class="input" id="edit_sup_name" name="edit_sup_name[<?php echo $i?>]" disabled="disabled" value="<?php echo $support_info['support_name']?>"></td>
									<td><span class="description">Имя пользователя изменить нельзя!</span></td>
								</tr>
							<?php else : ?>
								<tr>
							  		<td><label class="label-text" for="edit_sup_name">Имя</label></td>
							 		<td><input placeholder="Введите имя" type="text" class="input" data-validation-engine="validate[required, custom[sup_name]]" id="edit_sup_name" name="edit_sup_name[<?php echo $i?>]" value="<?php echo $support_info['support_name']?>"></td>
						  		</tr>
						  	<?php endif;?>
					  			<tr>
					  				<td><label class="label-text" for="edit_sup_rank">Ранг</label></td>
					  				<td><input placeholder="Введите ранг" type="text" class="input" data-validation-engine="validate[required,custom[integer]]" id="edit_sup_rank" name="edit_sup_rank[<?php echo $i?>]" value="<?php echo $support_info['support_rank']?>"></td>
					  			</tr>
					  			<tr>
					  				<td><label class="label-text" for="new_support_email">Email</label></td>
					  				<td><input placeholder="Введите email" type="text" data-validation-engine="validate[required,custom[email]]" class="input margin-right-0" id="edit_sup_email" name="edit_sup_email[<?php echo $i?>]" value="<?php echo $support_info['support_email']?>"></td>
					  			</tr>
					  			<tr>	
					  				<td><label class="label-text" for="edit_sup_pass">Пароль</label></td>
					  				<td><input placeholder="Введите пароль" type="password" class="input" id="edit_sup_pass" name="edit_sup_pass[<?php echo $i?>]"></td>
					  				<td><span class="description">Если пароль менять не нужно, оставьте поле пустым!</span></td>
					  			</tr>
					  	<?php $i++?>
			           	<?php endforeach;?>
			        <?php else : ?>
			        <?php if($_GET["support_edit"]) : ?>
			           	<?php $support_to_edit = parseGetParameters((int) $_GET["support_edit"]);?>
			           	<?php $support_info = get_support($support_to_edit)?>
			           	<tr>
			           		<td></td>
			           		<td></td>
			           		<td>
			           			<br>
			           			<h2 class="support-to-edit"><?php echo $support_info['support_name']?></h2>
			           			<br>
			           		</td>
			           	</tr>
			                <tr>
								<td><label class="label-text" for="edit_sup_id">ID</label></td>
								<td><input placeholder="Введите ID" type="text" class="input" id="edit_sup_id" data-validation-engine="validate[required, custom[integer]]" name="edit_sup_id[0]" value="<?php echo $support_info['support_id']?>"><input type="hidden" class="input" id="edit_sup_h" name="edit_sup_h[<?php echo $i?>]" value="<?php echo $support_info['support_id']?>"></td>
							</tr>
						<?php if($support_to_edit == $_SESSION['support']['id']) : ?>
							<tr>
								<td><label class="label-text" for="edit_sup_name">Имя</label></td>
								<td><input placeholder="Введите имя" type="text" class="input" id="edit_sup_name" name="edit_sup_name[0]" disabled="disabled" value="<?php echo $support_info['support_name']?>"></td>
								<td><span class="description">Имя пользователя изменить нельзя!</span></td>
							</tr>
						<?php else : ?>
							<tr>
						  		<td><label class="label-text" for="edit_sup_name">Имя</label></td>
						 		<td><input placeholder="Введите имя" type="text" class="input" data-validation-engine="validate[required, custom[sup_name]]" id="edit_sup_name" name="edit_sup_name[0]" value="<?php echo $support_info['support_name']?>"></td>
					  		</tr>
					  	<?php endif;?>
				  			<tr>
				  				<td><label class="label-text" for="edit_sup_rank">Ранг</label></td>
				  				<td><input placeholder="Введите ранг" type="text" class="input" data-validation-engine="validate[required,custom[integer]]" id="edit_sup_rank" name="edit_sup_rank[0]" value="<?php echo $support_info['support_rank']?>"></td>
				  			</tr>
				  			<tr>
				  				<td><label class="label-text" for="new_support_email">Email</label></td>
				  				<td><input placeholder="Введите email" type="text" data-validation-engine="validate[required,custom[email]]" class="input margin-right-0" id="edit_sup_email" name="edit_sup_email[0]" value="<?php echo $support_info['support_email']?>"></td>
				  			</tr>
				  			<tr>	
				  				<td><label class="label-text" for="edit_sup_pass">Пароль</label></td>
				  				<td><input placeholder="Введите пароль" type="password" class="input" id="edit_sup_pass" name="edit_sup_pass[0]"></td>
				  				<td><span class="description">Если пароль менять не нужно, оставьте поле пустым!</span></td>
				  			</tr>
		           	<?php endif;?>
		           	<?php endif;?>
		           	<?php if(empty($error_string)) : ?>
		           		<tr>
		           			<td>
		           				<br>
		           				<input type="submit" name="submit_edit_user" id="submit_edit_user" class="btn btn-wide btn-primary mrm" value="Сохранить">
		           			</td>
		           		</tr>
		           	<?php endif;?>
		           	</table>
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
<?php get_footer_template("", true) ?>