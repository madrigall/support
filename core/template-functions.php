<?php
/**
 * Not M_DIRECTORY important
 */

/**
 * Подключаем шаблон header
 */
function get_header_template($name = null, $adminTemplate = false)
{
	if(empty($name))
		$name = "header.php";
	else
		$name = "header-{$name}.php";

	if(!$adminTemplate)
        if(file_exists(template_path()."{$name}"))
            require_once template_path()."{$name}";
        else
        {
            $d_template_path = support_info("default_templates");
            $d_template = $d_template_path . "no-template.php";

            require_once $d_template;
        }
    else
        if(file_exists(admin_template_path()."{$name}"))
            require_once admin_template_path()."{$name}";
        else
        {
            $d_template = admin_template_path . "no-template.php";

            require_once $d_template;
        }
}

/**
 * Подключаем шаблон footer
 */

function get_footer_template($name = null, $adminTemplate = false)
{
	if(empty($name))
		$name = "footer.php";
	else
		$name = "footer-{$name}.php";

	if(!$adminTemplate)
        if(file_exists(template_path()."{$name}"))
            require_once template_path()."{$name}";
        else
        {
            $d_template_path = support_info("default_templates");
            $d_template = $d_template_path . "no-template.php";

            require_once $d_template;
        }
    else
        if(file_exists(admin_template_path()."{$name}"))
            require_once admin_template_path()."{$name}";
        else
        {
            $d_template = admin_template_path . "no-template.php";

            require_once $d_template;
        }
}

/**
 * Подключаем шаблон sidebar
 */

function get_sidebar_template($name = null, $adminTemplate = false)
{
	if(empty($name))
		$name = "sidebar.php";
	else
		$name = "sidebar-{$name}.php";

	if(!$adminTemplate)
        if(file_exists(template_path()."{$name}"))
            require_once template_path()."{$name}";
        else
        {
            $d_template_path = support_info("default_templates");
            $d_template = $d_template_path . "no-template.php";

            require_once $d_template;
        }
    else
        if(file_exists(admin_template_path()."{$name}"))
            require_once admin_template_path()."{$name}";
        else
        {
            $d_template = admin_template_path . "no-template.php";

            require_once $d_template;
        }
}


/**
 * Подключаем шаблоны администратора
 */
 
function include_admin_template($name)
{
	$template_path = PATH . "admin/templates/";
	$template = $template_path . $name . ".php";

	if(file_exists($template))
		include admin_template_path() . $name . ".php";
	else
		include admin_template_path() . "error.php";
}

/**
 * Подключаем функции шаблона
 */
 
function include_template_functions($name)
{
	$functions = PATH . ACTIVE_TEMPLATE .  $name . ".php";

	if(file_exists($functions))
		include template_path() . $name . ".php";
}

function include_custom_parts($name)
{
	$functions = PATH . ACTIVE_TEMPLATE .  $name . ".php";

	if(file_exists($functions))
		include template_path() . $name . ".php";
}

/**
 * Вешаем обработчик на свои страницы
 */
 
function add_handler_pages($param, $value, $name, $method = "GET")
{	

    if(empty($param))
        echo "Not transferred first parameter!<br>";
        
    if(empty($value))
        echo "Not transferred second parameter!<br>";
    
    if(empty($name))
        echo "Not transferred third parameter!<br>";
        
	$template = PATH . ACTIVE_TEMPLATE .  $name . ".php";
    
	if($method == "GET")
		if(isset($_GET["{$param}"]) && $_GET["{$param}"] == $value)
        {
            if(file_exists($template))
                include template_path() . $name . ".php";
            else
                echo "File is not found!";
        }
        else
            if($method == "POST")
                if(isset($_POST["{$param}"]) && $_POST["{$param}"] == $value)
                {
                    if(file_exists($template))
                        include template_path() . $name . ".php";
                    else
                        echo "File is not found!";
                }      

}

/**
 * Important
 */

/**
 * Ф-ция подключает обязательные шаблоны, если его нет берет default_templates
 */

function require_template($name = null)
{
	$template_path = support_info("template");
	$template = $template_path . $name . ".php";

	if(file_exists($template))
		require_once $template;
	else
	{
		$d_template_path = support_info("default_templates");
		$d_template = $d_template_path . $name . ".php";

		require_once $d_template;
	}
}

function include_error_template($error)
{
    $template_path = support_info("template");
    $template = $template_path . $error . ".php";

    if(file_exists($template))
        require_once $template;
    else
    {
        $d_template_path = support_info("default_templates");
        $d_template = $d_template_path . $error . ".php";

        require_once $d_template;
    }
}
?>