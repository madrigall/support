<!-- Navigation -->
<nav class="main-navigation clearfix">
    <ul class="cbp-vimenu tt-wrapper none">
        <li>
            <a href="/" class="icon-logo"><span>На сайт</span></a>
        </li>
       <!--  <li>
            <a href="/admin/"><i class="fa fa-home fa-lg "></i><span>Главная</span></a>
        </li> -->
        <li>
            <?php if($_SESSION["update"] != CURRENT_VERSION && (!empty($_SESSION["update"]) && $_SESSION["update"])) : ?>
                <span class="notification notification-green"><i class="fa fa-bell-o fa-lg"></i></span>
            <?php endif;?>
            <a href="?mode=update"><i class="fa fa-refresh fa-lg "></i><span>Обновление</span></a>
        </li>
        <li>
            <?php if(issetNewTicketsToAnswer($_SESSION["support"]["id"])) : ?>
                <span class="notification notification-green"><i class="fa fa-bell-o fa-lg"></i></span>
            <?php endif;?>
            <a href="?mode=tickets"><i class="fa fa-ticket fa-lg"></i><span>Заявки</span></a>
        </li>
        <li>
            <a href="?mode=plugins"><i class="fa fa-plug fa-lg"></i><span>Плагины</span></a>
        </li>
        <li>
            <a href="?mode=themes"><i class="fa fa-desktop fa-lg"></i><span>Темы</span></a>
        </li>
        <li>
            <a href="?mode=users"><i class="fa fa-users fa-lg"></i><span>Пользователи</span></a>
        </li>
        <li>
            <a href="?mode=settings"><i class="fa fa-cog fa-lg "></i><span>Настройки</span></a>
        </li>
        <?php admin_menu()?>
    </ul>
</nav>
<!-- End Navigation --> 