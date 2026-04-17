<?php 
include_once('common.php');
include_once('db.php');


// CHECK TO SEE IF TIMER ALREADY EXISTS
if(@$post['json'] && $previous_set = sql("SELECT * FROM `sets` WHERE `json` = '".@$post['json']."'")) {
	echo $previous_set['slug'];
	exit;
}


if (@$post['edit_slug']) {
	if (sql("UPDATE `sets` SET `json` = '".$post['json']."' WHERE `edit_slug` = '".$post['edit_slug']."'")) {
		$row = sql("SELECT `slug` FROM `sets` WHERE `edit_slug` = '".$post['edit_slug']."'");
		if ($row) {
			echo $row['slug'];
		}
		else {
			echo 'Error: There was an problem saving the timer to the database';
		}
	}
	else {
		echo 'Error: There was an problem saving the timer to the database';
	}
	exit;
}


// IF NOT, ADD TIMER TO DATABASE
$slug = makeSlug();
$edit_slug = makeEditSlug();
if (@$post['json'] && sql("INSERT INTO `sets` (`slug`, `edit_slug`, `json`, `created`) VALUES ('$slug', '$edit_slug', '".$post['json']."', NOW())")) {
	// Line 1: public slug. Line 2: edit_slug (so first save can redirect to /edit/{edit_slug}).
	echo $slug . "\n" . $edit_slug;
}
else echo 'Error: There was an problem saving the timer to the database';



?>