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

include_once ('functions.php');

global $wb;

if (LANGUAGE_LOADED) {
	if (file_exists (WB_PATH.'/modules/asr/languages/'.LANGUAGE.'.php')) {
		require_once (WB_PATH.'/modules/asr/languages/'.LANGUAGE.'.php');
	} else {
		require_once (WB_PATH.'/modules/asr/languages/EN.php');
	}
}


function drawStarRating (
	$voted,
	$static,
	$ajax,
	$vars) {

	global $ASR;
	
	// now draw the rating bar
	$ratingwidth = @number_format ($vars ['total_value'] / $vars ['total_votes'], 2) * $vars ['unitwidth'];
	$rating = @number_format ($vars ['total_value'] / $vars ['total_votes'], 1);
	$tense = ($vars ['total_votes'] == 1) ? $ASR['VOTE'] : $ASR['VOTES']; // plural form votes/vote
		
	$rater = array();
	if (!$ajax) $rater [] = "\n".'<div class="ratingblock'.$vars ['unitwidth'].'">';
	$rater [] = '<div id="unit_long_'.$vars ['id'].'">';
	$rater [] = '<ul id="unit_ul_'.$vars ['id'].'" class="unit-rating" style="width:'.$vars ['unitwidth'] * $vars ['units'].'px;">';
	$rater [] = '<li class="current-rating" style="width:'.$ratingwidth.'px;">'.$ASR['CURRENT_RATING'].' '.$rating.'/'.$vars ['units'].'</li>';
	
	if (!$static) {
		for ($ncount = 1; $ncount <= $vars ['units']; $ncount++) { // loop from 1 to the number of units
			(!$voted)
				//? $rater [] = '<li><a href="?j='.$ncount.'&amp;q='.$vars ['id'].'&amp;t='.$vars ['ip'].'&amp;c='.$vars ['units'].'&amp;w='.$vars ['unitwidth'].'" title="'.$ncount.' '.$ASR['RATING_LINK_TITLE'].' '.$vars ['units'].'" class="r'.$ncount.'-unit rater" rel="nofollow">'.$ncount.'</a></li>'
				? $rater [] = '<li><a href="?rating='.$ncount.'&amp;id='.$vars ['id'].'&amp;ip='.$vars ['ip'].'&amp;units='.$vars ['units'].'&amp;width='.$vars ['unitwidth'].'" title="'.$ncount.' '.$ASR['RATING_LINK_TITLE'].' '.$vars ['units'].'" class="r'.$ncount.'-unit rater" rel="nofollow">'.$ncount.'</a></li>'
				: $rater [] = '<li class="r'.$ncount.'-unit">'.$ncount.'</li>';
		}
	}
		
	$rater [] = '</ul>';
	$pclass = '';
	$staticNote = '';
	if ($static) {
		$pclass = ' class="static"';
		$staticNote = '<em>'.$ASR['STATIC_NOTE'].'</em>';
	}
	if ($voted) $pclass = ' class="voted"';
	$rater [] = '<p'.$pclass.'>'.$ASR['RATING'].' <strong> '.$rating.'</strong>/'.$vars ['units'].' ('.$vars ['total_votes'].' '.$tense.' '.$ASR['CAST'].') '.$staticNote.'</p>';
	$rater [] = '</div>';
	if (!$ajax) $rater [] = '</div>';
	$rater [] = "\n\n";

	return join ("\n", $rater);
}


if (isset ($_REQUEST['rating']) && isset ($_REQUEST['id']) && isset ($_REQUEST['ip']) && isset ($_REQUEST['units']) && isset ($_REQUEST['width'])) {
	//getting the values
	$vote_sent = preg_replace ("/[^0-9]/", "", $_REQUEST['rating']);
	$id_sent = preg_replace ("/[^0-9a-zA-Z\-_]/", "", $_REQUEST['id']);
	$ip_sent = preg_replace ("/[^0-9\-]/", "", $_REQUEST['ip']);
	$units = preg_replace ("/[^0-9]/", "", $_REQUEST['units']);
	$unitwidth = preg_replace ("/[^0-9]/", "", $_REQUEST['width']);
	$ajax = (isset ($_REQUEST ['a'])) ? preg_replace ("/[^0-9]/", "", $_REQUEST['a']) : 0;
	
	$ip = ip2long (getIP ());
	
	// get votes from DB
	$sql = "SELECT total_votes, total_value FROM ".TABLE_PREFIX."mod_asr_ratings WHERE id='$id_sent' ";
	$db = $database->query ($sql);
	
	$ratings = $db->fetchRow();
	
	$voted = userHasVoted ($id_sent, $ip_sent, 'mod_asr_blocked_ip');

	$total_value = $ratings ['total_value']; // total number of rating added together and stored
	$total_votes = $ratings ['total_votes']; // how many votes total
	
	//IP check when voting
	if (!$voted) { //if the user hasn't yet voted, then vote normally...
		if (($vote_sent >= 1 && $vote_sent <= $units) && ($ip == $ip_sent)) { // keep votes within range, make sure IP matches - no monkey business!
		
			$total_value = $total_value + $vote_sent; // add together the current vote value and the total vote value
			// checking to see if the first vote has been tallied
			// or increment the current number of votes
			$total_votes = ($total_value == 0) ? 0 : $total_votes + 1;
			
			$update = "UPDATE ".TABLE_PREFIX."mod_asr_ratings SET total_votes='".$total_votes."', total_value='".$total_value."' WHERE id='".$id_sent."' ";
			$database->query ($update);
			
			$insert = "INSERT INTO ".TABLE_PREFIX."mod_asr_blocked_ip (rating_id, ip_addr, timestamp) VALUES ('$id_sent', '$ip', '".time ()."')";
			$database->query ($insert);
			
			$voted = true;
		}
		
	}
	
	// name of the div id to be updated | the html that needs to be changed
	if ($ajax) {
		
		$vars = array (
			'id' => $id_sent,
			'ip' => $ip,
			'units' => $units,
			'unitwidth' => $unitwidth,
			'total_value' => $total_value,
			'total_votes' => $total_votes
			);
		
		$output = 'unit_long_'.$id_sent.'|'.drawStarRating ($voted, false, $ajax, $vars);
		echo $output;
		// this is important to prevent the whole page from being redrawn
		exit ();
	}	
} else {}


function ratingBar (
	$id,
	$units = 5,
	$static = false,
	$private = false,
	$timeout = 6,
	$unitwidth = 20
	) {
	
	global $admin;
	global $database;

	$ip = ip2long (getIP ());

	// get votes, values for the current rating bar
	$sql = "SELECT total_votes, total_value FROM ".TABLE_PREFIX."mod_asr_ratings WHERE id='$id' ";
	$db = $database->query ($sql);

	// create DB entry if id doesn't exist yet
	if (!isset ($db) || ($db->numRows() == 0)) {
		$sql = "INSERT INTO ".TABLE_PREFIX."mod_asr_ratings (`id`, `total_votes`, `total_value`) VALUES ('$id', '0', '0')";
		$db = $database->query ($sql);
		// get values again, otherwise $ratings is empty at first call
		$sql = "SELECT total_votes, total_value FROM ".TABLE_PREFIX."mod_asr_ratings WHERE id='$id' ";
		$db = $database->query ($sql);
	}
	
	$ratings = $db->fetchRow();

	
	// determine whether the user has voted, so we know how to draw the rating list
	unblockIPs ($timeout, 'mod_asr_blocked_ip');
	$voted = userHasVoted ($id, $ip, 'mod_asr_blocked_ip');
	
	
	$vars = array (
		'id' => $id,
		'ip' => $ip,
		'units' => $units,
		'unitwidth' => $unitwidth,
		'total_value' => $ratings ['total_value'],
		'total_votes' => $ratings ['total_votes']
		);
		
	if (($private && ($admin->is_authenticated())) || (!$private)) {
		return drawStarRating ($voted, $static, false, $vars);
	} // private mode
}
?>
