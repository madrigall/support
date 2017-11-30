<?php
defined('SUPPORT_SYSTEM') or die('Access denied');

# Система хуков для PHP 5
# Повесить хук можно только на функции, в которых дописана строчка (желательно, в начале):
#    if (hs_has_hook(__FUNCTION__)) { return hs_call_hook(__FUNCTION__, func_get_args()); }

if ($HOOKSYS_VERSION == NULL)
{
	$HOOKSYS_VERSION = "1.0";

	$hs_hook_array = array();

	function hs_add_hook($func_name, $hook_func_name)
	{
		global $hs_hook_array;
		
		$hs_hook_array[$func_name] = $hook_func_name;
	}

	function hs_drop_hooks($func_name)
	{
		global $hs_hook_array;

		$hs_hook_array[$func_name] = NULL;
	}

	function hs_has_hook($func_name)
	{
		global $hs_hook_array;

		if ($hs_hook_array[$func_name] == NULL) 
			return FALSE;
		else
		   return TRUE;
	}

	function hs_call_hook($func_name, $func_args)
	{
		global $hs_hook_array;

		if (hs_has_hook($func_name))
		{
			return call_user_func_array($hs_hook_array[$func_name], $func_args);
		}
	}
}
?>