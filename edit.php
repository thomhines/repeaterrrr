<?php
/*----------------------------------------------------------------------*

	REPEATERRRR
	http://repeaterrrr.com/
	https://github.com/thomhines/repeaterrrr
	
	The clean and easy repeating timer.

	Copyright 2014, Thom Hines
	MIT License
	
*----------------------------------------------------------------------*/


include_once('common.php');
include_once('db.php');


error_reporting(E_ALL);

if(@$get['copy']) {
	// LOAD JSON FROM SERVER
	$set_result = sql("SELECT * FROM `sets` WHERE `slug` = '".$get['copy']."';");
	// CONVERT JSON INTO PHP ARRAY
	$set = json_decode($set_result['json'], true);
	$set['info']['title'] = $set['info']['title'] . " (copy)";
	$set = json_encode($set);
	$set = str_replace("'", "\'", $set);
	// print_r($set);
	
	$slug = makeSlug();
	$edit_slug = makeEditSlug();

	$result = sql("INSERT INTO `sets` (`slug`, `edit_slug`, `json`, `created`) VALUES ('$slug', '$edit_slug', '".$set."', NOW())", 1);
	if(!$result) echo 'Error: There was an problem saving the timer to the database';

	header('Location: https://repeaterrrr.com/edit/'.$edit_slug);
	
	die;
}

$set = array();
$edit_timer_not_found = false;

if (@$get['set']) {
	// LOAD JSON FROM SERVER (edit_slug only — not public slug)
	$set_result = sql("SELECT * FROM `sets` WHERE `edit_slug` = '".$get['set']."';");
	if ($set_result) {
		$set = json_decode($set_result['json'], true);
	}
	else {
		$edit_timer_not_found = true;
		$set['info'] = array('title' => '', 'description' => '');
		$set['steps'] = array(array('title' => '', 'time' => '', 'color' => '', 'sound' => ''));
	}
}
else {
	// FOR THE SAKE OF GIVING A BLANK STEP ROW ON NEW TIMERS
	$set['info'] = array('title' => '','description' => '');
	$set['steps'] = array(array('title' => '', 'time' => '', 'color' => '', 'sound' => ''));
}

?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title>repeaterrrr | Edit Timer</title>
<meta charset="utf-8" />
<meta name="description" content="The clean and easy repeating timer." />
<meta name="viewport" content="user-scalable=no, width=500">

<link rel="icon" href="/img/favicons/favicon.ico" sizes="48x48" />
<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png" />
<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png" />
<link rel="apple-touch-icon" href="/img/favicons/apple-touch-icon.png" />
<link rel="manifest" href="/img/favicons/site.webmanifest" />
<meta name="theme-color" content="#ffffff" />
<meta name="msapplication-TileColor" content="#ffffff" />
<meta name="msapplication-TileImage" content="/img/favicons/android-chrome-192x192.png" />

<link rel="stylesheet" href="/css/fonts.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/dragula.min.css" type="text/css" media="all" />
<link rel="Stylesheet" href="/css/style.css" type="text/css" media="all" />
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="/js/jquery.noclickdelay.js"></script>
<script src="/js/soundjs.min.js"></script>
<script src="/js/dragula.min.js"></script>
<script src="/js/scripts.js"></script>

</head>

<body class="editor clearfix">
	<a class="title" href="/"><h1><img src="/img/repeaterrrr_logo.svg" alt="repeaterrrr" onerror="this.onerror=null; this.src='/img/repeaterrrr_logo.svg'"></h1></a>

	<?php if ($edit_timer_not_found) { ?>
	<div class="edit_not_found_notice" role="status">That timer is not in the database. Check the link or start a new timer below.</div>
	<script>
		history.replaceState(null, '', '/edit/');
	</script>
	<?php } ?>

	<h4>Timer Info</h4>
	<form>
		<label for='title'>Title* (<span class="title_char_count">0</span>/40 chars.)</label>
		<input type="text" id="title" class="title" maxlength="40" placeholder="Timer Name" value="<?php echo htmlspecialchars($set['info']['title']); ?>">
		
		<label for='description'>Description (<span class="description_char_count">0</span>/140 chars.)</label>
		<textarea type="text" id="description" class="description" maxlength="140" placeholder="A little bit about your timer..."><?php echo htmlspecialchars($set['info']['description']); ?></textarea>
		
	
		<h4>Steps</h4>
	
		<div class="steps_labels">
			<label class="name_label">Name</label>
			<label class="time_label">Time</label>
			<label class="color_label">Color</label>
			<label class="tone_label">Tone</label>
		</div>
		
		<ul class="steps">
		<?php if($set['steps']) foreach($set['steps'] as $step) { ?>
		
	
		<li class="step <?php echo ($step['color']) ? $step['color'] : 'white'; ?>">
			<span class="drag_handle">&#xe805;</span>
			<i class="copy_step smaller button icon-docs" role="button"></i>
			<input type="text" class="name" value="<?php echo $step['title'] ?>" placeholder="Step Name">
			<input type="number" class="time" value="<?php echo $step['time'] ?>" min="1" step="1" placeholder="10"><label>sec</label>
			<select class="color">
				<option value="white" <?php if($step['color'] == 'white') echo 'selected="selected"'; ?>>White</option>
				<option value="red" <?php if($step['color'] == 'red') echo 'selected="selected"'; ?>>Red</option>
				<option value="yellow" <?php if($step['color'] == 'yellow') echo 'selected="selected"'; ?>>Yellow</option>
				<option value="green" <?php if($step['color'] == 'green') echo 'selected="selected"'; ?>>Green</option>
				<option value="blue" <?php if($step['color'] == 'blue') echo 'selected="selected"'; ?>>Blue</option>
			</select>
			<select class="tone">
				<option value="">None</option>
				<option value="single" <?php if($step['sound'] == 'single') echo 'selected="selected"'; ?>>Single</option>
				<option value="double" <?php if($step['sound'] == 'double') echo 'selected="selected"'; ?>>Double</option>
				<option value="triple" <?php if($step['sound'] == 'triple') echo 'selected="selected"'; ?>>Triple</option>
				<option value="short" <?php if($step['sound'] == 'short') echo 'selected="selected"'; ?>>Long</option>
			</select>
			<i class="delete_step smaller button icon-cancel" role="button"></i>
		</li>
		<?php } ?>
		</ul>
		<div><button class="medium button add_step" role="button">+ Add New Row</button></div>
	
		<!-- EMPTY ROW TEMPLATE FOR ADDING NEW STEP ROWS -->
		<li class="step row_template white">
			<span class="drag_handle">&#xe805;</span>
			<i class="copy_step smaller button icon-docs" role="button"></i>
			<input type="text" class="name">
			<input type="number" class="time" value="1" min="1" step="1"><label>sec</label>
			<select class="color">
				<option value="white">White</option>
				<option value="red">Red</option>
				<option value="yellow">Yellow</option>
				<option value="green">Green</option>
				<option value="blue">Blue</option>
			</select>
			<select class="tone">
				<option value="">None</option>
				<option value="single">Single</option>
				<option value="double">Double</option>
				<option value="triple">Triple</option>
				<option value="short">Long</option>
			</select>
			<i class="delete_step smaller button icon-cancel" role="button"></i>
		</li>
		
		<hr>
	
		<h5 class="repeat_container">Repeat all steps <input type="number" class="repeat" min="1" value="<?php if(isset($set['info']['repeat'])) echo $set['info']['repeat']; else echo '1'; ?>"> times</h5>
	
		<span class="error_message"></span>
		<button class="special button save disabled" role="button">Save Timer</button>
	</form>
	
	<div class="ajax"></div>
</body>

</html>