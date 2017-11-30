        <footer>
            <p class='copyright'>© 2013-<?php echo the_date("Y")?> Support System Engine. All rights reserved.<span class="developer">Разработчик - <a href="mailto:madrigall-2014@yandex.ru">madrigall</a> | <a href="#">License</a> | Версия - <?php echo CURRENT_VERSION?></span></p>
        </footer>

        <script>
            var key = "<?php echo auth_support()?>";
        </script>    
        
        <script>
            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,400";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="//cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.4.1/jquery.jgrowl.min.css";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="http://fonts.googleapis.com/css?family=Play:400,700&amp;subset=latin,cyrillic";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="http://fonts.googleapis.com/css?family=Bree+Serif";document.getElementsByTagName("head")[0].appendChild(ms);
           
            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="<?php support_info('core_scripts_dir')?>validation/validationEngine.jquery.css";document.getElementsByTagName("head")[0].appendChild(ms);

            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="<?php support_info('core_scripts_dir')?>animate/animate.min.css";document.getElementsByTagName("head")[0].appendChild(ms);
            
            var ms=document.createElement("link");ms.rel="stylesheet";
            ms.href="<?php support_info('core_scripts_dir')?>ickeck/skins/square/_all.css";document.getElementsByTagName("head")[0].appendChild(ms);
            document.getElementsByTagName("head")[0].appendChild(ms);
        </script>
                
        <!-- Load Scripts -->
        <script>var scr = {"scripts":[
            {"src" : "https://www.google.com/recaptcha/api.js", "async" : false},
            {"src" : "https://cdn.socket.io/socket.io-1.3.7.js", "async" : false},
            {"src" : "https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js", "async" : false},
            {"src" : "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js", "async" : false},
            {"src" : "<?php support_info('core_scripts_dir')?>ickeck/icheck.min.js", "async" : false},
            {"src" : "<?php support_info('core_scripts_dir')?>waypoints/waypoints.min.js", "async" : false},
            {"src" : "<?php support_info('core_resources_echo')?>tinymce/js/tinymce/tinymce.min.js", "async" : false},
            {"src" : "<?php support_info('core_scripts_dir')?>modernizr/modernizr.custom.js", "async" : false},
            {"src" : "//cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.4.1/jquery.jgrowl.min.js", "async" : false},
            {"src" : "<?php support_info('core_scripts_dir')?>validation/jquery.validationEngine-ru.js", "async" : false},
            {"src" : "<?php support_info('core_scripts_dir')?>validation/jquery.validationEngine.js", "async" : false},
            {"src" : "<?php support_info('core_scripts_dir')?>tab/tab.js", "async" : false},
            {"src" : "<?php admin_template_url()?>js/main.min.js", "async" : false},
            ]};

            !function(t,n,r){"use strict";var c=function(t){if("[object Array]"!==Object.prototype.toString.call(t))return!1;for(var r=0;r<t.length;r++){var c=n.createElement("script"),e=t[r];c.src=e.src,c.async=e.async,n.body.appendChild(c)}return!0};t.addEventListener?t.addEventListener("load",function(){c(r.scripts);},!1):t.attachEvent?t.attachEvent("onload",function(){c(r.scripts)}):t.onload=function(){c(r.scripts)}}(window,document,scr);
        </script> 

        <!--<script src="<?php support_info("core_scripts_dir")?>animate/animate.js"></script>-->

    </body>
</html>