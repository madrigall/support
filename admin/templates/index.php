<?php if(defined("CUSTOM_FOLDER")) : ?>
    <?php header("Location: " . CUSTOM_FOLDER . "admin/?mode=tickets");?>
    <?php exit();?>
<?php else:
     header("Location: /admin/?mode=tickets")?>
     <?php exit();?>
<?php endif;?>

<?php get_header_template("", true)?>
<?php get_sidebar_template("left", true)?>
    <div class="main-wrapper">
        <section id="main-container">
            <h2 class="count-weeks">Показывать статистику за:</h2>
            <div class="weeks-div">
                <input id="weeks" type="radio" name="weeks" checked value="2"><p class="weeks">Две недели</p>
                <input id="weeks" type="radio" name="weeks" value="3"><p class="weeks">Три недели</p>
                <input id="weeks" type="radio" name="weeks" value="4"><p class="weeks">Четыри недели</p>
            </div>
            <canvas width="900px" height="600" id="tickets_statistic"></canvas>
        </section>
    </div> 
    <div class="clearfix"></div> 
<?php get_footer_template("", true)?>      
           