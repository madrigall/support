<?php

if(isset($_POST["submit_settings_one"]) && canSupportDo($_SESSION["support"]["id"], "settings"))
{	
	global $coreLog;

	$settings = getSettings();

	foreach ($settings as $key)
    	$set_value["{$key["name"]}"] = $key["value"];	

	$log 				= (bool)$_POST["log"];
	$anonym 			= (bool)$_POST["anonym"];
	//$timezone 		= (string) $_POST["timezone"];
	$title 				=  $_POST["title"];
	$description 		= (string) $_POST["description"];
	$keywords 			=  $_POST["keywords"];
	$captcha_key 		= (string) $_POST["captcha_key"];
	$captcha_secret_key = (string) $_POST["captcha_secret_key"];
	$default_rank 		= (int) $_POST["default_rank"];
	$tickets_amount 	= (int) $_POST["tickets_amount"];
	$captcha 			= (bool)$_POST["CAPTCHA"];
	
	/*if(!preg_match("#^[a-zA-Z0-9_ ]*$#i", $title))
	{
		$coreLog->write("{$_SESSION['support'][id]} при смене title ввел недопустимые значения, а именно: $title");

		header("Location: /admin/?mode=settings&status=fail&fail_e=1");
        exit();
	}

	if(!preg_match("#^[a-zA-Z0-9\s_ ]*$#i", $description))
	{
		$coreLog->write("{$_SESSION['support'][id]} при смене описания ввел недопустимые значения, а именно: $description");

		header("Location: /admin/?mode=settings&status=fail&fail_e=2");
        exit();
	}

	if(!mb_ereg("[а-яА-Яa-zA-ZЁё0-9_,]", $keywords))
	{
		$coreLog->write("{$_SESSION['support'][id]} при смене описания ввел недопустимые значения, а именно: $description");

		header("Location: /admin/?mode=settings&status=fail&fail_e=3");
        exit();
	}
	*/

	if(!preg_match("#^[0-9]*$#i", $tickets_amount0 ) || $tickets_amount <= 0)
	{
		$coreLog->write("{$_SESSION['support'][id]} при смене количества тикетов на странице ввел недопустимые значения, а именно: $tickets_amount");

		header("Location: ?mode=settings&fail_e=1");
        exit();
	}

	if($default_rank > 10 || $default_rank <= 0)
	{
		$coreLog->write("{$_SESSION['support'][id]} при смене ранга по умолчанию ввел недопустимые значения, а именно: $default_rank");

		header("Location: ?mode=settings&fail_e=2");
        exit();
	}

	$result = false;

	if(!isset($set_value["ANONYM_USERS"]))
	{
		if(!$anonym || is_null($anonym))
    		$result = insertSettings("ANONYM_USERS", "FALSE");
    	else
    		$result = insertSettings("ANONYM_USERS", "TRUE");
	}
	else
	{
    	if($set_value["ANONYM_USERS"] == "TRUE" && !$anonym)
    		$result = updateSettings("ANONYM_USERS", "FALSE");
    	
    	if($set_value["ANONYM_USERS"] == "FALSE" && $anonym)
    		$result = updateSettings("ANONYM_USERS", "TRUE");
    	
    }

    if(!isset($set_value["LOGGING"]))
	{
		if(!$log || is_null($log))
    		$result = insertSettings("LOGGING", "FALSE");
    	else
    		$result = insertSettings("LOGGING", "TRUE");
	}
	else
	{
    	if($set_value["LOGGING"] == "TRUE" && !$log)
    		$result = updateSettings("LOGGING", "FALSE");
    	
    	if($set_value["LOGGING"] == "FALSE" && $log)
    		$result = updateSettings("LOGGING", "TRUE");
    }

    if(!isset($set_value["CAPTCHA"]))
	{
		if(!$captcha || is_null($captcha))
    		$result = insertSettings("CAPTCHA", "FALSE");
    	else
    		$result = insertSettings("CAPTCHA", "TRUE");
	}
	else
	{
    	if(trim($set_value["CAPTCHA"]) == "TRUE" && !$captcha)
    		$result = updateSettings("CAPTCHA", "FALSE");
    	
    	if(trim($set_value["CAPTCHA"]) == "FALSE" && $captcha)
    		$result = updateSettings("CAPTCHA", "TRUE");
    }
    /*if(!isset($set_value["TIMEZONE"]))
		$result = insertSettings("TIMEZONE", $timezone);
	else
		if($set_value["TIMEZONE"] != $timezone)
			$result = updateSettings("TIMEZONE", $timezone);*/
	if(isset($_POST["title"]) && !empty($_POST["title"]))
		if(!isset($set_value["TITLE"]))
			$result = insertSettings("TITLE", $title);
		else
			if($set_value["TITLE"] != $title)
				$result = updateSettings("TITLE", $title);

	if(isset($_POST["description"]) && !empty($_POST["description"]))
		if(!isset($set_value["DESCRIPTION"]))
			$result = insertSettings("DESCRIPTION", $description);
		else
	    	if($set_value["DESCRIPTION"] != $description)
	    		$result = updateSettings("DESCRIPTION", $description);

    if(isset($_POST["keywords"]) && !empty($_POST["keywords"]))
	    if(!isset($set_value["KEYWORDS"]))
			$result = insertSettings("KEYWORDS", $keywords);
		else
	    	if($set_value["KEYWORDS"] != $keywords)
	    		$result = updateSettings("KEYWORDS", $keywords);

    if(isset($_POST["captcha_key"]) && !empty($_POST["captcha_key"]))
	    if(!isset($set_value["CAPTCHA_KEY"]))
			$result = insertSettings("CAPTCHA_KEY", $captcha_key);
		else
	    	if($set_value["CAPTCHA_KEY"] != $captcha_key)
	    		$result = updateSettings("CAPTCHA_KEY", $captcha_key);

    if(isset($_POST["captcha_secret_key"]) && !empty($_POST["captcha_secret_key"]))
	    if(!isset($set_value["CAPTCHA_SECRET_KEY"]))
			$result = insertSettings("CAPTCHA_SECRET_KEY", $captcha_secret_key);
		else
	    	if($set_value["CAPTCHA_SECRET_KEY"] != $captcha_secret_key)
	    		$result = updateSettings("CAPTCHA_SECRET_KEY", $captcha_secret_key);
    
    if(isset($_POST["default_rank"]) && !empty($_POST["default_rank"]))
	    if(!isset($set_value["DEFAULT_RANK"]))
			$result = insertSettings("DEFAULT_RANK", $default_rank);
		else
	    	if($set_value["DEFAULT_RANK"] != $default_rank)
	    		$result = updateSettings("DEFAULT_RANK", $default_rank);

    if(isset($_POST["tickets_amount"]) && !empty($_POST["tickets_amount"]))
	    if(!isset($set_value["TICKETS_AMOUNT"]))
			$result = insertSettings("TICKETS_AMOUNT", $tickets_amount);
		else
	    	if($set_value["TICKETS_AMOUNT"] != $tickets_amount)
	    		$result = updateSettings("TICKETS_AMOUNT", $tickets_amount);

    if($result || !isset($rusult))
	{	    
		$coreLog->write("{$_SESSION['support'][id]} изменил настройки!");

		header("Location: ?mode=settings&status=success");
    	exit();
	}
	
	if(isset($rusult) && $result == false)
	{
		$coreLog->write("{$_SESSION['support'][id]} получил ошибку при сохранении настроек!");

		header("Location: ?mode=settings&status=fail");
    	exit();
	}

}

if($_POST["submit_add_answer"] && canSupportDo($_SESSION["support"]["id"], "settings"))
{
	global $coreLog;

	$question = $_POST["question"];
	$answer = $_POST["answer"];
	$category_id = $_POST["category_id"];

	if(empty($question) && empty($answer) && empty($category_id))
	{
		header("Location: ?mode=settings&status=fail_q");
    	exit();
	}

	$result = false;

	foreach ($category_id as $key => $value)
	{
		$result = addAnswer($value, $question, $answer);
	}	

	if($result)
	{	    	
		$coreLog->write("{$_SESSION['support'][id]} добавил ответ на частый вопрос в категорию с id " . var_export($category_id, true));

		header("Location: ?mode=settings&status=success");
    	exit();
	}
	else
	{
		$coreLog->write("{$_SESSION['support'][id]} получил ошибку при добавлении ответа на частый вопрос в категорию с id " . var_export($category_id, true));
		
		header("Location: ?mode=settings&status=fail");
    	exit();
	}
}

if($_POST["submit_settings_authorization"] && canSupportDo($_SESSION["support"]["id"], "settings"))
{
	global $coreLog;

	$settings = getSettings();

	foreach ($settings as $key)
    	$set_value["{$key["name"]}"] = $key["value"];	

	$db_name = (string) $_POST["DB_USERS"];
	$db_user = (string) $_POST["DB_USERS_USER"];
	$db_host = (string) $_POST["DB_USERS_HOST"];
	$db_pass = (string) $_POST["DB_USERS_PASS"];
	$method = (string) $_POST["method"];	
	
	$USERS_TABLE = (string) $_POST["USERS_TABLE"];
	$USERS_COLUMN_ID = (string) $_POST["USERS_COLUMN_ID"];
	$USERS_COLUMN_USER = (string) $_POST["USERS_COLUMN_USER"];
	$USERS_COLUMN_EMAIL = (string) $_POST["USERS_COLUMN_EMAIL"];
	$USERS_COLUMN_PASS = (string) $_POST["USERS_COLUMN_PASS"];

	
	if($_POST["USERS_TABLE_OTHER"])
		$USERS_TABLE_OTHER = (string) $_POST["USERS_TABLE"];

	if($_POST["USERS_COLUMN_SALT"])
		$USERS_COLUMN_SALT = (string) $_POST["USERS_TABLE"];

	if(isset($_POST["DB_USERS"]) && !empty($_POST["DB_USERS"]))
		if(!isset($set_value["DB_USERS"]))
			$result = insertSettings("DB_USERS", $db_name);
		else
			if($set_value["DB_USERS"] != $db_name)
				$result = updateSettings("DB_USERS", $db_name);

	if(isset($_POST["DB_USERS_USER"]) && !empty($_POST["DB_USERS_USER"]))
		if(!isset($set_value["DB_USERS_USER"]))
			$result = insertSettings("DB_USERS_USER", $db_user);
		else
			if($set_value["DB_USERS_USER"] != $db_user)
				$result = updateSettings("DB_USERS_USER", $db_user);

	if(isset($_POST["DB_USERS_HOST"]) && !empty($_POST["DB_USERS_HOST"]))
		if(!isset($set_value["DB_USERS_HOST"]))
			$result = insertSettings("DB_USERS_HOST", $db_host);
		else
			if($set_value["DB_USERS_HOST"] != $db_host)
				$result = updateSettings("DB_USERS_HOST", $db_host);

	if(isset($_POST["DB_USERS_PASS"]))
		if(!isset($set_value["DB_USERS_PASS"]) && $db_pass)
			$result = insertSettings("DB_USERS_PASS", $db_pass);
		else
			if($set_value["DB_USERS_PASS"] != $db_pass)
				$result = updateSettings("DB_USERS_PASS", $db_pass);

	if(isset($_POST["method"]) && !empty($_POST["method"]))
		if(!isset($set_value["HASH_CRYPT"]))
			$result = insertSettings("HASH_CRYPT", $method);
		else
			if($set_value["HASH_CRYPT"] != $method)
				$result = updateSettings("HASH_CRYPT", $method);

	if(isset($_POST["USERS_TABLE"]) && !empty($_POST["USERS_TABLE"]))
		if(!isset($set_value["USERS_TABLE"]))
			$result = insertSettings("USERS_TABLE", $USERS_TABLE);
		else
			if($set_value["USERS_TABLE"] != $USERS_TABLE)
				$result = updateSettings("USERS_TABLE", $USERS_TABLE);

	if(isset($_POST["USERS_COLUMN_ID"]) && !empty($_POST["USERS_COLUMN_ID"]))
		if(!isset($set_value["USERS_COLUMN_ID"]))
			$result = insertSettings("USERS_COLUMN_ID", $USERS_COLUMN_ID);
		else
			if($set_value["USERS_COLUMN_ID"] != $USERS_COLUMN_ID)
				$result = updateSettings("USERS_COLUMN_ID", $USERS_COLUMN_ID);

	if(isset($_POST["USERS_COLUMN_USER"]) && !empty($_POST["USERS_COLUMN_USER"]))
		if(!isset($set_value["USERS_COLUMN_USER"]))
			$result = insertSettings("USERS_COLUMN_USER", $USERS_COLUMN_USER);
		else
			if($set_value["USERS_COLUMN_USER"] != $USERS_COLUMN_USER)
				$result = updateSettings("USERS_COLUMN_USER", $USERS_COLUMN_USER);

	if(isset($_POST["USERS_COLUMN_EMAIL"]) && !empty($_POST["USERS_COLUMN_EMAIL"]))
		if(!isset($set_value["USERS_COLUMN_EMAIL"]))
			$result = insertSettings("USERS_COLUMN_EMAIL", $USERS_COLUMN_EMAIL);
		else
			if($set_value["USERS_COLUMN_EMAIL"] != $USERS_COLUMN_EMAIL)
				$result = updateSettings("USERS_COLUMN_EMAIL", $USERS_COLUMN_EMAIL);

	if(isset($_POST["USERS_COLUMN_PASS"]) && !empty($_POST["USERS_COLUMN_PASS"]))
		if(!isset($set_value["USERS_COLUMN_PASS"]))
			$result = insertSettings("USERS_COLUMN_PASS", $USERS_COLUMN_PASS);
		else
			if($set_value["USERS_COLUMN_PASS"] != $USERS_COLUMN_PASS)
				$result = updateSettings("USERS_COLUMN_PASS", $USERS_COLUMN_PASS);

	if(isset($_POST["USERS_TABLE_OTHER"]) && !empty($_POST["USERS_TABLE_OTHER"]))
		if(!isset($set_value["USERS_TABLE_OTHER"]))
			$result = insertSettings("USERS_TABLE_OTHER", $USERS_TABLE_OTHER);
		else
			if($set_value["USERS_TABLE_OTHER"] != $USERS_TABLE_OTHER)
				$result = updateSettings("USERS_TABLE_OTHER", $USERS_TABLE_OTHER);

	if(isset($_POST["USERS_TABLE_OTHER"]) && !empty($_POST["USERS_TABLE_OTHER"]))
		if(!isset($set_value["USERS_COLUMN_SALT"]))
			$result = insertSettings("USERS_COLUMN_SALT", $USERS_COLUMN_SALT);
		else
			if($set_value["USERS_COLUMN_SALT"] != $USERS_COLUMN_SALT)
				$result = updateSettings("USERS_COLUMN_SALT", $USERS_COLUMN_SALT);

	if($result || !isset($rusult))
	{	   
		$coreLog->write("{$_SESSION['support'][id]} изменил настройки авторизации!");

		header("Location: ?mode=settings&status=success");
    	exit();
	}
	
	if(isset($rusult) && $result == false)
	{
		$coreLog->write("{$_SESSION['support'][id]} получил ошибку при сохранении настроек авторизации!");

		header("Location: ?mode=settings&status=fail");
    	exit();
	}
}
?>
<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
        <div class="main-wrapper">
            <section id="main-container">
            	<?php global $coreLog?>
            	<?php $set_array = getSettings()?>
            	<?php foreach ($set_array as $key) $set_value["{$key["name"]}"] = $key["value"]?>
            	<?php $support_id = $_SESSION['support']['id']?>

            	<?php if(canSupportDo($support_id, "settings")) : ?>
	            	<?php if($_GET["status"] == "fail") : ?>
	            		<div class="information-message error">
	            			<p>Ошибка при записи данных!</p>
	            		</div>
	            	<?php endif;?>

	            	<?php if($_GET["fail_e"] == "1") : ?>
	            		<div class="information-message error">
	            			<p>Количество тикетов не может быть меньше 0!</p>
	            		</div>
	            	<?php endif;?>

	            	<?php if($_GET["fail_e"] == "2") : ?>
	            		<div class="information-message error">
	            			<p>Ранг по умолчанию не может быть больше 10 или меньше 0!</p>
	            		</div>
	            	<?php endif;?>

	            	<?php if($_GET["status"] == "success") : ?>
	            		<div class="information-message success">
	            			<p>Данные изменены!</p>
	            		</div>
	            	<?php endif;?>

	            	<ul id="settings-tabs">
	            		<li class="active"><a href="#general" data-toggle="tab" class="settings-tab">Общие</a></li>
	            		<li><a href="#categories" data-toggle="tab" class="settings-tab">Категории</a></li>
	            		<li><a href="#answers" data-toggle="tab" class="settings-tab">Ответы</a></li>
	            		<li><a href="#authorization" data-toggle="tab" class="settings-tab">Авторизация</a></li>
	            		<li><a href="#messages" data-toggle="tab" class="settings-tab">Группы</a></li>
	            	</ul>


					<div class="settings-content">
						<div class="tab-pane display-none active" id="general">
							<form action='?mode=settings' id='options_form' novalidate='novalidate' method='post'>
								<table id="setting-table">
									<tr>
										<td><label for="log">Включить логирование</label></td>
										<?php if($coreLog->get_logging()) : ?>
											<td class="padding-left-15"><input class="width-350" type='checkbox' name='log' checked="checked" ></td>
										<?php else : ?>
											<td class="padding-left-15"><input class="width-350" type='checkbox' name='log'></td>
										<?php endif;?>
									</tr>
									<tr>
										<td><label for="anonym">Разрешить анонимные тикеты</label></td>
										<?php if($set_value["ANONYM_USERS"] == "TRUE") : ?>
											<td class="padding-left-15"><input class="width-350" type='checkbox' name='anonym' checked="checked"></td>
										<?php else : ?>
											<td class="padding-left-15"><input class="width-350" type='checkbox' name='anonym'></td>
										<?php endif;?>
									</tr>
									<tr>
										<td><label for="CAPTCHA">Включить ReCaptcha</label></td>
										<?php if(trim($set_value["CAPTCHA"]) == "TRUE") : ?>
											<td class="padding-left-15"><input class="width-350" type='checkbox' name='CAPTCHA' checked="checked"></td>
										<?php else : ?>
											<td class="padding-left-15"><input class="width-350" type='checkbox' name='CAPTCHA'></td>
										<?php endif;?>
									</tr>

									<!-- <tr>
										<td><label>Часовой пояс</label></td>
										<td>

											<select id="timezone" name="timezone" class="margin-left-15 width-350">
												<option>Дорабатывается</option>
											</select>
										</td>
									</tr> -->
									<tr>
										<td><label for='title'>Название сайта</label></td>
										<td><input type='text' placeholder="Введите название сайта" class='input width-350' name='title' value='<?php echo $set_value["TITLE"]?>'></td>
									</tr>
									<tr>
										<td><label for='description'>Описание сайта</label></td>
										<td><input type='text' placeholder="Введите описание сайта" class='input width-350' name='description' value='<?php echo $set_value["DESCRIPTION"]?>'></td>
									</tr>
									<tr>
										<td><label for='keywords'>Ключевые слова</label></td>
										<td><input type='text' placeholder="Введите ключевые слова через зяпятую" class='input width-350' name='keywords' value='<?php echo $set_value["KEYWORDS"]?>'></td>
										<td><span class="description">Перечисляйте через запятую</span></td>
									</tr>
									<tr>
										<td><label for='captcha_key'>Ключ reCaptcha</label></td>
										<td><input type='text' placeholder="Введите ключ капчи" class='input width-350' name='captcha_key' value='<?php echo $set_value["CAPTCHA_KEY"]?>'></td>
										<td><span class="description">Его можно посмотреть в администраторской reCaptcha</span></td>
									</tr>
									<tr>
										<td><label for='captcha_secret_key'>Секретный ключ reCaptcha</label></td>
										<td><input type='text' placeholder="Введите секретный ключ капчи"class='input width-350' name='captcha_secret_key' value='<?php  echo $set_value["CAPTCHA_SECRET_KEY"]?>'></td>
										<td><span class="description">Его можно посмотреть в администраторской reCaptcha</span></td>
									</tr>
									<tr>
										<td><label for='default_rank'>Ранг нового пользователя</label></td>
										<td>
											<select class="margin-left-15 width-350" name="default_rank">
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option selected="selected" value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
											</select>
											<i class="select-arrow fa fa-arrow-down"></i>
										</td>
									</tr>
									<tr>
										<td><label for='tickets_amount'>Количество заявок</label></td>
										<td><input placeholder="Введите количество заявок на одной стринице" type='number' min='1' class='input width-350' name='tickets_amount' value='<?php echo $set_value["TICKETS_AMOUNT"]?>'></td>
										<td><span class="description">Сколько выводить заявок на странице!</span></td>
									</tr>
									<tr>
										<td>
											<br>
											<input type='submit' name='submit_settings_one' id='submit_settings' class='btn btn-wide btn-primary mrm' value='Сохранить'>
										</td>
										
									</tr>
								</table>
							</form>
						</div>

					<!-- Категории -->
					<div class="tab-pane display-none" id="categories">
						<!-- <span class="settings-title">Категории</span> -->
						<div class="categories">
							<?php if(support_categories()) : ?>
								<?php foreach(support_categories() as $categories) : ?>
										<span class="tag" id="<?php echo $categories["id"]?>"><span class="tag-content"><?php echo $categories["name"]?></span><a href="#" class="delete-category"><i class="fa fa-times my-times"></i></a></span>     
								<?php endforeach;?>
							<?php else : ?>
								<span class="tag">Категорий нет!</span>
							<?php endif;?>
						</div>
						<form>
							<input type='text' placeholder="Введите категорию" class='input width-350 margin-left-no' id="category-name" name='category-name'>
							<input type='submit' name='add-category' id='add-category' class='btn btn-wide btn-primary mrm' value='Добавить'>
						</form>
					</div>

					<!-- ЧаВо -->
					<div class="tab-pane display-none" id="answers">
							<!-- <span class="settings-title">Ответы на вопросы</span> -->
							<div id="sup-answ">
							<?php foreach(support_categories() as $categories) : ?>
								<?php if(has_questions($categories["id"])) : ?>
										<div class="support-answers" id="<?php echo $categories["id"]?>">
											<div class="support-cat-name"><?php echo $categories["name"]?></div>
											<?php foreach(support_answers($categories["id"]) as $questions) : ?>
												<div class="answers" id="<?php echo $questions["id"]?>">
													<div class="inline">Вопрос: <span class="answer-content"><?php echo $questions["question"]?></span><a href="#" class="delete-answer-b" id="<?php echo $questions["id"]?>"><i class="fa fa-times my-times"></i></a></div>
													<br>
													<div class="inline">Ответ: <span class="answer-content"><?php echo strip_tags($questions["support_answer"], array("<p>","</p>"))?></span></div>
												</div>
											<?php endforeach;?>
											</div>
										<?php else : ?>
										<div class="support-answers" id="<?php echo $categories["id"]?>">
											<div class="support-cat-name"><?php echo $categories["name"]?></div>
											<p class="description margin-top-bottom">Ответов нет.</p>
										</div>
									<?php endif;?>
							<?php endforeach;?>
							</div>
							<span class="settings-title">Написать ответ</span>
							<form action='?mode=settings' method='post' id='options_form'>
								<div id="answers-wrapper">
									<div id="answers">
										<?php if(support_categories()) : ?>
											<p class="description margin-top-bottom">1. Выберите категорию, в которой будет ответ.</p>
											<div class="last">
												<?php foreach(support_categories() as $categories) : ?>
													<?php $i++?>
														<span class="tag answers-tag" id="<?php echo $categories["id"]?>"><input class="add-answer" name="category_id[<?php echo $j?>]" value ="<?php echo $categories["id"]?>" type="checkbox"><span class="tag-content answers-content"><?php echo $categories["name"]?></span></span>     
												<?php endforeach;?>
											</div>
										<?php else : ?>
											<span class="tag answers-tag">Категорий нет!</span>
										<?php endif;?>
										<p class="description margin-top-bottom">2. Введите частозадаваемый вопрос.</p>
										<input type="text" name="question" class="input green width-350 margin-left-no" placeholder="Введите вопрос">
										<p class="description margin-top-bottom">3. Введите ответ.</p>
										<div id="support-answer">
											<textarea name="answer"></textarea>
										</div>
										<input type='submit' name='submit_add_answer' id='submit_settings' class='btn btn-wide btn-primary mrm block width-100' value='Добавить'>
									</div>
								</div>
							</form>
						
					</div>
					

					<!-- Авторизация -->
					<div class="tab-pane display-none" id="authorization">
						<!-- <span id="authorization-slide" class="settings-title settings-authorization">Настройки авторизация<span class="row-table "></span></span> -->
						<form action='?mode=settings' id='options_form' method='post'>
							<table>		
								<!-- <tr class="set-authorization">
									<td>
										<label class="text-align-left" for="type_bd">Тип базы данных</label>
									</td>
									<td>
										<select class="margin-left-15 width-350" name="type_bd">
											<option selected value="mysql">Mysql</option>
											<option value="sqlli">SqlLi</option>
										</select>
									</td>
									<td>
										<p class="description text-align-left">Тип базы данных с пользователями</p>
									</td>
								</tr> -->

								<tr class="set-authorization">
									<td>
										<label class="text-align-left" for="DB_USERS">Имя базы данных</label>
									</td>
									<td>
										<input type="text" name="DB_USERS" class="input width-350" value="<?php echo $set_value["DB_USERS"]?>">
									</td>
									<td>
										<p class="description text-align-left">Введите имя базы данных для авторизации пользователей через различные CMS</p>
									</td>
								</tr>
								<tr class="set-authorization">
									<td>
										<label class="text-align-left" for="DB_USERS_USER">Имя пользователя</label>
									</td>
									<td>
										<input type="text" name="DB_USERS_USER" class="input width-350" value="<?php echo $set_value["DB_USERS_USER"]?>">
									</td>
									<td>
										<p class="description text-align-left">Введите имя пользователя базы данных для авторизации пользователей через различные CMS</p>
									</td>
								</tr>
								<tr class="set-authorization">
									<td>
										<label class="text-align-left" for="DB_USERS_HOST">Адрес базы данных</label>
									</td>
									<td>
										<input type="text" name="DB_USERS_HOST" class="input width-350" value="<?php echo $set_value["DB_USERS_HOST"]?>">
									</td>
									<td>
										<p class="description text-align-left">Введите хост базы данных для авторизации пользователей через различные CMS</p>
									</td>
								</tr>
								<tr class="set-authorization">
									<td>
										<label class="text-align-left" for="DB_USERS_PASS">Пароль пользователя</label>
									</td>
									<td>
										<input type="text" name="DB_USERS_PASS" class="input width-350" >
									</td>
									<td>
										<p class="description text-align-left">Введите пароль базы данных для авторизации пользователей через различные CMS</p>
									</td>
								</tr>
								<tr class="set-authorization">
									<td>
										<label class="text-align-left" for="method">Метод авторизации</label>
									</td>
									<td>
										<select class="margin-left-15 width-350" name="method">
											<?php if(HASH_CRYPT == "wordpress") : ?>
												<option selected value="wordpress">WordPress</option>
											<?php else : ?>
												<option value="wordpress">WordPress</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "joomla") : ?>
												<option selected value="joomla">Joomla</option>
											<?php else : ?>
												<option value="joomla">Joomla</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "ipb") : ?>
												<option selected value="ipb">IPB</option>
											<?php else : ?>
												<option value="ipb">IPB</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "xenforo") : ?>
												<option selected value="xenforo">XenForo</option>
											<?php else : ?>
												<option value="xenforo">XenForo</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "vbulletin") : ?>
												<option selected value="vbulletin">vBulletin</option>
											<?php else : ?>
												<option value="vbulletin">vBulletin</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "dle") : ?>
												<option selected value="dle">DLE</option>
											<?php else : ?>
												<option value="dle">DLE</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "drupal") : ?>
												<option selected value="drupal">Drupal</option>
											<?php else : ?>
												<option value="drupal">Drupal</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "hash_md5") : ?>
												<option selected value="hash_md5">Md5</option>
											<?php else : ?>
												<option value="hash_md5">Md5</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "custom") : ?>
												<option selected value="custom">Свой</option>
											<?php else : ?>
												<option value="custom">Свой</option>
											<?php endif;?>

											<?php if(HASH_CRYPT == "none") : ?>
												<option selected value="none">Отсутствует</option>
											<?php else : ?>
												<option value="none">Отсутствует</option>
											<?php endif;?>

										</select>
									</td>
									<td>
										<p class="description text-align-left">С какой CMS или форумум вы будете интегрировать аторизацию</p>
									</td>
								</tr>

								<tr class="set-authorization">
								    <td>
								        <label class="text-align-left" for="USERS_TABLE">Таблица базы данных</label>
								    </td>
								    <td>
								        <input type="text" name="USERS_TABLE" class="input width-350" value="<?php echo $set_value["USERS_TABLE"]?>">
								    </td>
								    <td>
								        <p class="description text-align-left">Введите таблицу базы данных с даннымы пользователей</p>
								    </td>
								</tr>
								<tr class="set-authorization">
								    <td>
								        <label class="text-align-left" for="USERS_COLUMN_ID">Идентификатор</label>
								    </td>
								    <td>
								        <input type="text" name="USERS_COLUMN_ID" class="input width-350" value="<?php echo $set_value["USERS_COLUMN_ID"]?>">
								    </td>
								    <td>
								        <p class="description text-align-left">Введите уникальный идентификатор пользователя для авторизации</p>
								    </td>
								</tr>
								<tr class="set-authorization">
								    <td>
								        <label class="text-align-left" for="USERS_COLUMN_USER">Логин</label>
								    </td>
								    <td>
								        <input type="text" name="USERS_COLUMN_USER" class="input width-350" value="<?php echo $set_value["USERS_COLUMN_USER"]?>">
								    </td>
								    <td>
								        <p class="description text-align-left">Введите колонку логина пользователя</p>
								    </td>
								</tr>
								<tr class="set-authorization">
								    <td>
								        <label class="text-align-left" for="USERS_COLUMN_EMAIL">Email</label>
								    </td>
								    <td>
								        <input type="text" name="USERS_COLUMN_EMAIL" class="input width-350" value="<?php echo $set_value["USERS_COLUMN_EMAIL"]?>">
								    </td>
								    <td>
								        <p class="description text-align-left">Введите колонку email'a пользователя</p>
								    </td>
								</tr>

								<tr class="set-authorization">
								    <td>
								        <label class="text-align-left" for="USERS_COLUMN_PASS">Пароль</label>
								    </td>
								    <td>
								        <input type="text" name="USERS_COLUMN_PASS" class="input width-350" value="<?php echo $set_value["USERS_COLUMN_PASS"]?>">
								    </td>
								    <td>
								        <p class="description text-align-left">Введите колонку пароля пользователя</p>
								    </td>
								</tr>

								<tr class="set-authorization">
									<td></td>
									<td><span class="settings-title settings-authorization">Только для IpBoard</span></td>
								</tr>

								<tr class="set-authorization">
								    <td>
								        <label class="text-align-left" for="USERS_COLUMN_SALT">Соль</label>
								    </td>
								    <td>
								        <input type="text" name="USERS_COLUMN_SALT" class="input width-350" value="<?php echo $set_value["USERS_COLUMN_SALT"]?>">
								    </td>
								    <td>
								        <p class="description text-align-left">Введите колонку c солью</p>
								    </td>
								</tr>

								<tr class="set-authorization">
									<td></td>
									<td><span class="settings-title settings-authorization">Только для XenForo</span></td>
								</tr>

								<tr class="set-authorization">
								    <td>
								        <label class="text-align-left" for="USERS_TABLE_OTHER">XenForo authenticate</label>
								    </td>
								    <td>
								        <input type="text" name="USERS_TABLE_OTHER" class="input width-350" value="<?php echo $set_value["USERS_TABLE_OTHER"]?>">
								    </td>
								    <td>
								        <p class="description text-align-left">XenForo authenticate</p>
								    </td>
								</tr>

								<tr  class="set-authorization">
									<td>
										<br>
										<input type='submit' name='submit_settings_authorization' id='submit_settings' class='btn btn-wide btn-primary mrm' value='Сохранить'>
									</td>
									
								</tr>
							</table>
						</form>
					</div>
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