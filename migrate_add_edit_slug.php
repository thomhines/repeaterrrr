<?php
/*----------------------------------------------------------------------*
 *
 * One-time migration: add `edit_slug` after `slug` on `sets`, then copy
 * `slug` into `edit_slug` for rows that need it.
 *
 * Run from CLI: php migrate_add_edit_slug.php
 * Or open once in a browser, then delete or protect this file.
 *
 *----------------------------------------------------------------------*/

require_once __DIR__ . '/db.php';

global $mysqli;

$table = '`sets`';
$column = 'edit_slug';

$check = $mysqli->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
if ($check === false) {
	die('Could not inspect table: ' . $mysqli->error . PHP_EOL);
}

$column_was_added = false;

if ($check->num_rows > 0) {
	echo "Column {$column} already exists; skipping ALTER." . PHP_EOL;
}
else {
	$sql = "ALTER TABLE {$table} ADD COLUMN `{$column}` TINYTEXT NULL AFTER `slug`";
	if (!$mysqli->query($sql)) {
		die('ALTER TABLE failed: ' . $mysqli->error . PHP_EOL);
	}
	echo "Added column {$column} after slug." . PHP_EOL;
	$column_was_added = true;
}

if ($column_was_added) {
	$update = "UPDATE {$table} SET `{$column}` = `slug`";
}
else {
	$update = "UPDATE {$table} SET `{$column}` = `slug` WHERE `{$column}` IS NULL OR `{$column}` = ''";
}

if (!$mysqli->query($update)) {
	die('UPDATE failed: ' . $mysqli->error . PHP_EOL);
}

echo 'Updated rows: ' . (int) $mysqli->affected_rows . PHP_EOL;
echo 'Done.' . PHP_EOL;
