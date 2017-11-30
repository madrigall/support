        <?php global $user;?>
        <footer>
        	<div id="footer-block">
    	    	<ul id="footer-information">
    	    		<li class="inline-ul"><span class="ul-title">Частые вопросы : </span>
                        <ol class="block-ul">
                            <?php $i = 0?>
                            <?php if(has_questions()) : ?>
                                <?php foreach(support_answers() as $questions) : ?>
                                    <?php $i++?>
                                    <?php $quest = preg_split('/\s+/' , $questions["question"])?>
                                        <li><a href="?search=<?php echo $quest[0]?>"><?php echo $questions["question"]?></a></li>
                                    <?php if($i == 4) break?>
                                <?php endforeach;?>
                             <?php else : ?>
                                <li><a>Вопросов нет</a></li>
                            <?php endif;?>
                        </ol>
                    </li>
    	    		<li class="inline-ul"><span class="ul-title">Ваши тикеты : </span>
                        <ol class="block-ul">
                            <?php if(have_user_tickets($user)) : ?>
                                <?php foreach(getTicketsFooter($user) as $content) : ?>
                                    <li><a href="?ticket_id=<?php echo $content['ticket_id']?>">Тикет #<?php echo $content["ticket_id"]?></a></li>
                                <?php endforeach;?>
                            <?php else : ?>
                                <li><a>Тикетов нет</a></li>
                            <?php endif;?>
                        </ol>
                    </li>
    	    		<li class="inline-ul"><span class="ul-title">Ссылки : </span>
                        <ol class="block-ul">
                            <li><a href="admin/">Администраторская</a></li>
                            <li><a href="#">Документация</a></li>                        
                            <li><a href="#">Правила</a></li>
                            <li><a href="#">Лицензия</a></li>
                        </ol>
                    </li>
                    <li>
                        <ul class="image-footer-inline">
                            <li><a href="#"><img class="image-footer" src="<?php template_url()?>images/vk.png" alt="VK"></a></li>
                            <li><a href="#"><img class="image-footer" src="<?php template_url()?>images/twitter.png" alt="Twitter"></a></li>
                            <li><a href="#"><img class="image-footer" src="<?php template_url()?>images/facebook.png" alt="Facebook"></a></li>
                        </ul>
                        <a href="#"><img class="logo-footer" src="<?php template_url()?>images/logo.png" alt="Logo"></a>
                    </li>

    	    	</ul>
                <div class="clear"></div>
        	</div>
            <hr/>
            <p class="copyright">© 2014–<?php echo the_date("Y")?> Veysiot Support System. Все права защищены. <img class="html5-img" src="<?php template_url()?>images/html.png" alt="Html5"></p>   
        </footer>
        
        <script>
            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="http://fonts.googleapis.com/css?family=Play";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="http://fonts.googleapis.com/css?family=Bree+Serif";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="<?php support_info('core_scripts_dir')?>ickeck/skins/square/_all.css";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css";document.getElementsByTagName("head")[0].appendChild(ms);

            /*          
            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="<?php template_url()?>style.min.css";
            */
            document.getElementsByTagName("head")[0].appendChild(ms);
        </script>
                
        <!-- Load Scripts -->
        <script>var scr = {"scripts":[
            {"src" : "https://www.google.com/recaptcha/api.js", "async" : false},
            {"src" : "https://cdn.socket.io/socket.io-1.3.7.js", "async" : false},
            {"src" : "https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js", "async" : false},
            {"src" : "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js", "async" : false},
            {"src" : "<?php support_info('core_scripts_dir')?>ickeck/icheck.min.js", "async" : false},
            {"src" : "<?php template_url()?>js/main.js", "async" : false}
            ]};

            !function(t,n,r){"use strict";var c=function(t){if("[object Array]"!==Object.prototype.toString.call(t))return!1;for(var r=0;r<t.length;r++){var c=n.createElement("script"),e=t[r];c.src=e.src,c.async=e.async,n.body.appendChild(c)}return!0};t.addEventListener?t.addEventListener("load",function(){c(r.scripts);},!1):t.attachEvent?t.attachEvent("onload",function(){c(r.scripts)}):t.onload=function(){c(r.scripts)}}(window,document,scr);
        </script>
    </body>
</html>