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

// Remove Droplet
$query = "DELETE FROM ".TABLE_PREFIX."mod_droplets WHERE name='asr'";
$database -> query ($query);
// Remove table with ratings
$query = "DROP TABLE IF EXISTS ".TABLE_PREFIX."mod_asr_ratings";
$database -> query ($query);
// Remove table with IP numbers
$query = "DROP TABLE IF EXISTS ".TABLE_PREFIX."mod_asr_blocked_ip";
$database -> query ($query);

if ($database->is_error()) {
	  $admin->print_error($database->get_error(), 'javascript:history_back();');
}

?>