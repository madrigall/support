<!DOCTYPE html>
<html lang="ru-RU">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"> 
        <title><?php support_title()?></title>

        <meta name="keywords" content="<?php support_keywords()?>">
        <meta name="Description" content="<?php support_description()?>">
        
    	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    	<script src="/core/js/parallax/parallax.min.js"></script>
        
        <link rel="stylesheet" type="text/css" href="/core/css/error.css">

        <!--[if lt IE 9]> 
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script> 
        <![endif]-->
    </head>
<body>
	<header class="head_main" style="width:100%;height:100%;overflow:hidden;" data-parallax="scroll" data-image-src="/core/images/lic.jpg" data-z-index="1">
		<div class="table">
			<div class="text-head">
				<p class="header-text error">401</p>
				<div class="buy">
					<?php if(defined("CUSTOM_FOLDER")) : ?>
						<a href="<?php echo CUSTOM_FOLDER?>/">Главная</a>
					<?php else : ?>
						<a href="/">Главная</a>
					<?php endif;?>
				</div>
			</div>
		</div>
	</header>
	
</body>
</html>