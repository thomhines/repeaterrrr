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
	
	$result = sql("INSERT INTO `sets` (`slug`, `json`, `created`) VALUES ('$slug', '".$set."', NOW())", 1);
	if(!$result) echo 'Error: There was an problem saving the timer to the database';

	header('Location: https://repeaterrrr.com/edit/'.$slug);
	
	die;
}

$set = array();
if(@$get['set']) {
	// LOAD JSON FROM SERVER
	$set_result = sql("SELECT * FROM `sets` WHERE `slug` = '".$get['set']."';");
	// CONVERT JSON INTO PHP ARRAY
	$set = json_decode($set_result['json'], true);
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

<link rel="apple-touch-icon" sizes="57x57" href="/img/favicons/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="60x60" href="/img/favicons/apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon" sizes="120x120" href="/img/favicons/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="76x76" href="/img/favicons/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="152x152" href="/img/favicons/apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="/img/favicons/favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="/img/favicons/favicon-160x160.png" sizes="160x160" />
<link rel="icon" type="image/png" href="/img/favicons/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="/img/favicons/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="/img/favicons/favicon-16x16.png" sizes="16x16" />
<meta name="msapplication-TileColor" content="#b91d47" />
<meta name="msapplication-TileImage" content="/img/favicons/mstile-144x144.png" />

<link rel="Stylesheet" href="/cinch/?files=/css/fonts.css,/css/style.scss" type="text/css" media="all" />
<script src="/cinch/?files=[jquery],[html5shiv],!/js/jquery.noclickdelay.js,!/js/jquery-ui-1.10.3.custom.min.js,!/js/jquery.ui.touch-punch.min.js,/js/scripts.js&debug=true"></script>

</head>

<body class="editor clearfix">
	<a href="/"><h1><img src="/img/logo.svg" alt="repeaterrrr" onerror="this.onerror=null; this.src='img/logo.png'"></h1></a>


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
		
	
		<li class="step <?php echo $step['color']; ?>">
			<span class="drag_handle">&#xe805;</span>
			<i class="copy_step smaller button icon-docs" role="button"></i>
			<input type="text" class="name" value="<?php echo $step['title'] ?>"><span class="icon-cancel field_error name_error"></span>
			<input type="number" class="time" value="<?php echo $step['time'] ?>"><span class="icon-cancel field_error number_error"></span><label>sec</label>
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
		<li class="step row_template">
			<span class="drag_handle">&#xe805;</span>
			<i class="copy_step smaller button icon-docs" role="button"></i>
			<input type="text" class="name"><span class="icon-cancel field_error name_error"></span>
			<input type="number" class="time"><span class="icon-cancel field_error time_error"></span><label>sec</label>
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
		
	
		<h5 class="repeat_container">Repeat all steps <input type="number" class="repeat" min="1" value="<?php if(isset($set['info']['repeat'])) echo $set['info']['repeat']; else echo '1'; ?>"> times</h5>
	
		<button class="special button save disabled" role="button">Save</button>
		<span class="error_message"></span>
	</form>
	
	<div class="ajax"></div>
</body>

</html>