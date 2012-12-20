<?php

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	include(WB_PATH.'/framework/class.secure.php'); 
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
	include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
	$subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));	$dir = $_SERVER['DOCUMENT_ROOT'];
	$inc = false;
	foreach ($subs as $sub) {
		if (empty($sub)) continue; $dir .= '/'.$sub;
		if (file_exists($dir.'/framework/class.secure.php')) { 
			include($dir.'/framework/class.secure.php'); $inc = true;	break; 
		} 
	}
	if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include class.secure.php

require_once(WB_PATH.'/framework/functions.php');


// import the droplets
include_once dirname(__FILE__).'/../droplets/functions.inc.php';

wb_unpack_and_import( dirname(__FILE__).'/install/asr.php.zip', WB_PATH.'/temp/unzip/' );


// Create tables
$query = "CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."mod_asr_ratings (
		id VARCHAR(25) NOT NULL,
		total_votes MEDIUMINT NOT NULL DEFAULT '0',
		total_value INT NOT NULL DEFAULT '0',
		units TINYINT NOT NULL DEFAULT '5',
		unitwidth TINYINT NOT NULL DEFAULT '20',
		PRIMARY KEY (id)
		)";
$database->query ($query);

$query = "CREATE TABLE IF NOT EXISTS ".TABLE_PREFIX."mod_asr_blocked_ip (
		id INT NOT NULL AUTO_INCREMENT,
		rating_id VARCHAR(15),
		ip_addr INT DEFAULT '0',
		timestamp INT DEFAULT '0',
		PRIMARY KEY (id)
		)";
$database->query ($query);
?>
