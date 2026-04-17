<?php

// CREATES A UNIQUE SLUG FOR EACH TIMER (PUBLIC SHARE / RUN URL)
function makeSlug() {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$slug = '';
	for ($i = 0; $i < 7; $i++) {
		$slug .= $chars[rand(0, strlen($chars) - 1)];
	}

	// SEE IF SLUG IS ALREADY TAKEN (AS slug OR edit_slug), IF SO, RUN AGAIN
	if (sql("SELECT * FROM `sets` WHERE `slug` = '$slug' OR `edit_slug` = '$slug'")) {
		return makeSlug();
	}

	return $slug;
}

// UNIQUE TOKEN FOR /edit/... ONLY (MUST NOT COLLIDE WITH ANY slug OR edit_slug)
function makeEditSlug() {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$edit_slug = '';
	for ($i = 0; $i < 7; $i++) {
		$edit_slug .= $chars[rand(0, strlen($chars) - 1)];
	}

	if (sql("SELECT * FROM `sets` WHERE `slug` = '$edit_slug' OR `edit_slug` = '$edit_slug'")) {
		return makeEditSlug();
	}

	return $edit_slug;
}

?>