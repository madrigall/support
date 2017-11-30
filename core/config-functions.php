<?php

/**
 * Определяем константы из БД
 */
 
function define_constants()
{
    global $db;

    $sql = "SELECT * FROM support_config";
    $stmt = $db->prepare($sql);

    $stmt->execute();
    
    while($row = $stmt->fetch())
    {
        define("$row[name]", "$row[value]");
    }
}

/**
 * Подключаем активированные плагины
 */
 
function include_active_plugins()
{
    global $active_plugins;

	global $db;

	$sql = "SELECT plugin_name FROM support_plugins";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while($row = $stmt->fetch())
    {  
    	$plugin_name = $row['plugin_name'];

    	/**
    	 * Если в папке есть файлы
    	 */

    	if(gettype((PLUGINS . $plugin_name . "/") == "array"))
    	{
			include(support_info("plugins_path") . $plugin_name . "/" . "index.php");
            
            $active_plugins[$plugin_name] = true;
		}
    }
}
?>