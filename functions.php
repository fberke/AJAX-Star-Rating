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



if (!function_exists ('getIP')) {
function getIP () {
	$ip = 0; // just in case...
	// Test if it is a shared client
	if (!empty ($_SERVER ['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER ['HTTP_CLIENT_IP'];
	// Is it a proxy address
	} elseif (!empty ($_SERVER ['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
	// Or get 'real' address
	} else {
		$ip = $_SERVER ['REMOTE_ADDR'];
	}

	return $ip;
}
}


if (!function_exists ('userHasVoted')) {
function userHasVoted ($id, $ip, $table_name) {
	global $database;

	$sql = "SELECT * FROM ".TABLE_PREFIX.$table_name." WHERE ip_addr='".$ip."' AND rating_id = '".$id."' ";
	$db = $database->query ($sql);

	return (isset ($db)) ? ($db->numRows() > 0) : false;
}
}


if (!function_exists ('unblockIPs')) {
function unblockIPs ($timeout, $table_name) {
	global $database;
	
	if ($timeout != -1) {
		$gap = time () - ($timeout * 3600);
		$sql = "DELETE FROM ".TABLE_PREFIX.$table_name." WHERE timestamp <= '".$gap."' ";
		$database->query ($sql);
	}
}
}

?>
