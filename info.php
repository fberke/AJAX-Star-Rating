<?php

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	include(WB_PATH.'/framework/class.secure.php'); 
} else {
	$oneback = "../";
	$root = $oneback;
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= $oneback;
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) { 
		include($root.'/framework/class.secure.php'); 
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}
// end include class.secure.php

$module_directory = 'asr';
$module_name = 'AJAX Star Rating';
$module_function = 'snippet';
$module_version = '0.3.2';
$module_platform = '1.1.0';
$module_author = 'fberke';
$module_license = 'GNU GPL';
$module_license_terms = '-';
$module_description = '';
$module_home = 'https://github.com/fberke/AJAX-Star-Rating';
$module_guid = 'da32bff6-3ae0-4076-a45f-e13215470e76';

?>