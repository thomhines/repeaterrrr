<?php 
// echo "asdfasdf";
include_once('common.php');
include_once('db.php');


// CHECK TO SEE IF TIMER ALREADY EXISTS
if(@$post['json'] && $previous_set = sql("SELECT * FROM `sets` WHERE `json` = '".@$post['json']."'")) {
	echo $previous_set['slug'];
	exit;
}


if(@$post['slug']) {
	if(sql("UPDATE `sets` SET `json` = '".$post['json']."' WHERE `slug` = '".$post['slug']."'")) echo $post['slug'];
	else echo 'Error: There was an problem saving the timer to the database';
	exit;
}


// IF NOT, ADD TIMER TO DATABASE
$slug = makeSlug();
if(@$post['json'] && sql("INSERT INTO `sets` (`slug`, `json`, `created`) VALUES ('$slug', '".$post['json']."', NOW())")) echo $slug;
else echo 'Error: There was an problem saving the timer to the database';



?>