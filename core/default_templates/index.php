<?php get_header_template()?>
        <section id="main-content">
            <div class="wrapper-answers">
                <?php if(!is_logged()) : ?> 
                    <div class="please-login">
                        <p>Пожалуйста, <a class="in-link" href="?action=login">авторизируйтесь</a> для дальнейшей работы!</p>
                    </div>
                <?php endif; ?>

                <div class="create_ticket clearfix">
                    <a href="?action=create_ticket" id="create_ticket_button" class="btn-large btn btn-block btn-primary">Создать заявку</a>
                </div>

                <form id="search-form">
                    <input type="text" class="input find-input find-input-text" name="search" placeholder="Введите вопрос">
                    <input type="submit" value="Найти" class="find-input btn btn-block btn-large btn-danger">
                </form>

                <?php if(isset($_GET["search"])) : ?>
                    <?php if(search(parseGetParameters((string)$_GET["search"]))) : ?>
                    <div class="category-content margin-bottom-50">
                        <h2 class="search-answers">Данные по вашему запросу</h2>
                        <?php foreach(search($_GET["search"]) as $value) : ?>
                            <div class="color-blue">Вопрос: <span class="answer-content"><?php echo $value["question"]?></span></div>
                            <div class="color-blue padding-top-10">Ответ: <span class="answer-content"><?php echo strip_tags($value["support_answer"], array("<p>","</p>"))?></span></div>
                            <br>
                        <?php endforeach;?>
                    </div>
                    <?php else : ?>
                    <div class="category-content margin-bottom-50">
                        <h2 class="search-answers">По вашему запросу ничего не найдено!</h2>
                    </div>
                    <?php endif;?>  
                <?php endif;?>

                <!-- Answers Block -->
                <?php foreach(support_categories() as $categories) : ?>
                	<div class="wrapper-answers-grid">
                        <div class="answers">
                            <div class="category-head">
                                <h1 class="answer-category"><?php echo $categories["name"]?><span class="press">(Нажмите)</span></h1>
                            </div>
                            <?php if(has_questions($categories["id"])) : ?>
                            <div class="category-content clearfix">
                            	<ul class="category-answers">
                                    <?php foreach(support_answers($categories["id"]) as $questions) : ?>
                                		<li class="color-blue">Вопрос: <span class="answer-content"><?php echo $questions["question"]?></span></li>
                                        <li class="color-blue padding-top-10">Ответ: <span class="answer-content"><?php echo strip_tags($questions["support_answer"], array("<p>","</p>"))?></span></li>
                                        <li><br></li>
                                    <?php endforeach;?>
                            	</ul>
                            </div>
                            <?php else : ?>
                                <div class="category-content">
                                   <span class="answer-content">Ответов в данной категории нет!</span>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                <?php endforeach;?>
                <!-- Answers Block End -->
            </div>            
        </section>
<?php get_footer_template()?>

