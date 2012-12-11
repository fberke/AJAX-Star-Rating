<?php
//:Displays a rating bar
//:id = Unique Identifier (up to 25 characters)
//:u = number of rating units (up to 10)
//:s = static (no rating possible); true/false or 1/0
//:p = private (logged-in users only); true/false or 1/0
//:w = unitwidth (width of star); CSS-presets for 14, 20 and 24
//:Minimum call: [[asr?id=thisid]]
//:Full call:    [[asr?id=myid&u=5&s=1&p=1&w=20]]

global $wb;

if (!isset ($u)) $u = 5;
if (!isset ($s)) $s = 0;
if (!isset ($p)) $p = 0;
if (!isset ($w)) $w = 20;

// timeout in hrs to to prevent voting from same IP
//  0 disables timeout
// -1 means timeout will never end and IPs are stored forever
$timeout = 6;

$return_value = 'There seems to be a problem with AJAX Star Rating';

if (function_exists ('ratingBar') && isset ($id)) {
	$return_value = ratingBar ($id, $u, $s, $p, $timeout, $w);
}

return $return_value;

?>
