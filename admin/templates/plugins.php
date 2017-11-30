<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
        <div class="main-wrapper">
            <section id="main-container">
            <?php if($_GET["status"] == "success") : ?>
                <div class="information-message success">
                    <p>Плагин активирован!</p>
                </div>
            <?php endif;?>
            <?php if($_GET["status"] == "fail") : ?>
                <div class="information-message error">
                    <p>Ошибка при активации плагина!</p>
                </div>
            <?php endif;?>

            <?php if($_GET["status"] == "success_d") : ?>
                    <div class="information-message warning">
                        <p>Плагин деактивирован!</p>
                    </div>
                <?php endif;?>
                <?php if($_GET["status"] == "fail_d") : ?>
                    <div class="information-message error">
                        <p>Ошибка при дективации плагина!</p>
                    </div>
            <?php endif;?>

            <?php
                $support_id = $_SESSION['support']['id'];
                global $active_plugins;
            ?>
            <?php if(canSupportDo($support_id, "plugins")) : ?>
                <?php $res = scan_plugins_dir()?>
                <?php if(!empty($res)) : ?>
                    <table id="plugins_table">
                        <thead>
                            <tr>                    
                                <th>Описание</th>
                                <th>Плагин</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (scan_plugins_dir() as $name) : ?>                
                                    <?php if($active_plugins[$name]) : ?>                        
                                        <tr class="plugins_content_active">
                                            <td class="table_plugins_image"><img src="<?php echo pluginPicture($name)?>"></td>
                                            <td class="table_plugins_name"><?php echo $name ?></td>
                                            <td class="table_plugins_action"><a class="active_plugin" href="?plug_deactive=<?php echo $name ?>">Деактивировать</td>
                                        </tr>
                                    <?php continue;?>
                                    <?php endif;?>
                            <?php endforeach;?>      
                            <?php foreach (scan_plugins_dir() as $name) : ?>
                            <?php if(!$active_plugins[$name]) : ?>
                    			<tr class="plugins_content">
                    				<td class="table_plugins_image"><img src="<?php echo pluginPicture($name)?>"></td>
                    				<td class="table_plugins_name"><?php echo $name ?></td>
                    				<td class="table_plugins_action"><a class="active_plugin" href="?plug_active=<?php echo $name?>">Активировать</td>
                    			</tr>
                            <?php endif;?>    
                            <?php endforeach;?>
            			</tbody>
            			<tfoot>
            			<tr>
            				<th>Описание</th>
            				<th>Плагин</th>
            				<th>Действие</th>
            			</tr>
            			</tfoot>
            		</table>
                <?php else : ?>
                    <div class="information-message error">
                       <p>Плагины отсутствуют!</p>
                    </div>
                <?php endif;?>
            <?php else : ?>
                <div id="message-error-rank" class="modal-overlay error-info">
                    <div class="modal-content animated bounce">
                        <button class="modal-action" data-modal-close="true"><i id="modal-button" class="fa fa-times fa-lg"></i></button>
                            <p>У вас недостаточно прав для просмотра этой страницы!</p>
                    </div>
                </div>
            <?php endif;?>    
            <?php
			?>
            </section>
        </div>
<?php get_footer_template("", true)?>