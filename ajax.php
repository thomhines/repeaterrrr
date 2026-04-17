<?php
include_once('common.php');
include_once('db.php');

global $mysqli;

function ajax_fail($message) {
	echo $message;
	exit;
}

$raw_json = isset($_POST['json']) && is_string($_POST['json']) ? $_POST['json'] : '';
$raw_edit = isset($_POST['edit_slug']) && is_string($_POST['edit_slug']) ? trim($_POST['edit_slug']) : '';

if ($raw_json === '') {
	ajax_fail('Error: There was an problem saving the timer to the database');
}

json_decode($raw_json);
if (json_last_error() !== JSON_ERROR_NONE) {
	ajax_fail('Error: Invalid timer data');
}

if ($raw_edit !== '' && !preg_match('/^[A-Za-z0-9]{1,32}$/', $raw_edit)) {
	ajax_fail('Error: Invalid request');
}

$esc_json = $mysqli->real_escape_string($raw_json);
$esc_edit = $mysqli->real_escape_string($raw_edit);

$previous_set = sql("SELECT `slug`, `edit_slug` FROM `sets` WHERE `json` = '".$esc_json."' LIMIT 1");
if ($previous_set) {
	$out = $previous_set['slug'];
	if (!empty($previous_set['edit_slug'])) {
		$out .= "\n" . $previous_set['edit_slug'];
	}
	echo $out;
	exit;
}

if ($raw_edit !== '') {
	if (!sql("UPDATE `sets` SET `json` = '".$esc_json."' WHERE `edit_slug` = '".$esc_edit."'")) {
		ajax_fail('Error: There was an problem saving the timer to the database');
	}
	$row = sql("SELECT `slug`, `edit_slug` FROM `sets` WHERE `edit_slug` = '".$esc_edit."' LIMIT 1");
	if ($row) {
		echo $row['slug'] . "\n" . $row['edit_slug'];
	}
	else {
		ajax_fail('Error: There was an problem saving the timer to the database');
	}
	exit;
}

$slug = makeSlug();
$edit_slug = makeEditSlug();

$stmt = $mysqli->prepare('INSERT INTO `sets` (`slug`, `edit_slug`, `json`, `created`) VALUES (?, ?, ?, NOW())');
if (!$stmt) {
	ajax_fail('Error: There was an problem saving the timer to the database');
}
$stmt->bind_param('sss', $slug, $edit_slug, $raw_json);
if (!$stmt->execute()) {
	$stmt->close();
	ajax_fail('Error: There was an problem saving the timer to the database');
}
$stmt->close();

echo $slug . "\n" . $edit_slug;
