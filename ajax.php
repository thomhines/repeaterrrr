<?php 
include_once('db.php');

// CHECK TO SEE IF TIMER ALREADY EXISTS
if($previous_set = sql("SELECT * FROM `sets` WHERE `json` = '".$post['json']."'")) {
	echo $previous_set['slug'];
	exit;
}

// IF NOT, ADD TIMER TO DATABASE
$slug = makeSlug();

if(sql("INSERT INTO `sets` (`slug`, `json`, `created`) VALUES ('$slug', '".$post['json']."', NOW())")) echo $slug;
else echo 'Error: There was an problem saving the timer to the database';



// CREATES A UNIQUE SLUG FOR EACH TIMER
function makeSlug() {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$slug = '';
	for ($i = 0; $i < 7; $i++) {
		$slug .= $chars[rand(0, strlen($chars)-1)];
	}

	// SEE IF SLUG IS ALREADY TAKEN, IF SO, RUN AGAIN
	if(sql("SELECT * FROM `sets` WHERE `slug` = '$slug'")) return makeSlug();
	else return $slug;
}

?>