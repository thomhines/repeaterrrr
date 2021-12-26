<?php
	
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