<?php
/*----------------------------------------------------------------------*

	REPEATERRRR
	http://repeaterrrr.com/
	https://github.com/thomhines/repeaterrrr
	
	The clean and easy repeating timer.

	Copyright 2014, Thom Hines
	MIT License
	
*----------------------------------------------------------------------*/


if($_GET['set']) {
	// CONVERT JSON INTO PHP ARRAY
	$set = json_decode($_GET['set'], true);
} else {
	// FOR THE SAKE OF GIVING A BLANK STEP ROW ON NEW TIMERS
	$set['steps'] = array('name' => '');
}
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title>repeaterrrr | Edit Timer</title>
<meta charset="utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="viewport" content="width=device-width">

<link rel="apple-touch-icon" sizes="57x57" href="../img/favicons/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="114x114" href="../img/favicons/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="72x72" href="../img/favicons/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="144x144" href="../img/favicons/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="60x60" href="../img/favicons/apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon" sizes="120x120" href="../img/favicons/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="76x76" href="../img/favicons/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="152x152" href="../img/favicons/apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="../img/favicons/favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="../img/favicons/favicon-160x160.png" sizes="160x160" />
<link rel="icon" type="image/png" href="../img/favicons/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="../img/favicons/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="../img/favicons/favicon-16x16.png" sizes="16x16" />
<meta name="msapplication-TileColor" content="#b91d47" />
<meta name="msapplication-TileImage" content="../img/favicons/mstile-144x144.png" />

<link rel="Stylesheet" href="/cinch/?files=/css/fonts.css,/css/style.scss" type="text/css" media="all" />
<script src="/cinch/?files=[jquery],[html5shiv],!/js/jquery.noclickdelay.js,!/js/ZeroClipboard.min.js,!/js/jquery.tablednd.js,/js/scripts.js&debug=true"></script>

</head>

<body class="editor">
	<a href="/"><h1><img src="/img/logo.svg" alt="repeaterrrr"></h1></a>


	<h4>Timer Info</h4>
	<label for='title'>Title* (<span class="title_char_count">0</span>/40 chars.)</label>
	<input type="text" id="title" class="title" maxlength="40" placeholder="Timer Name" value="<?php echo htmlspecialchars($set['info']['title']); ?>">
	
	<label for='description'>Description (<span class="description_char_count">0</span>/140 chars.)</label>
	<textarea type="text" id="description" class="description" maxlength="140" placeholder="A little bit about your timer..."><?php echo htmlspecialchars($set['info']['description']); ?></textarea>
	

	<h4>Steps</h4>
	<table class="steps">
	<tr class="nodrop nodrag"><th></th><th></th><th>Name</th><th>Time</th><th>Color</th><th>Tone</th></tr>
	<?php if($set['steps']) foreach($set['steps'] as $step) { ?>
	<tr class="<?php echo $step['color']; ?>">
		<td class="drag_handle">&#xe805;</td>
		<td><i class="delete_step smaller button icon-cancel" role="button"></span></td>
		<td><input type="text" class="name" placeholder="Step" value="<?php echo $step['title'] ?>"></td>
		<td><input type="number" class="time" placeholder="30" value="<?php echo $step['time'] ?>"><span>sec</span></td>
		<td>
			<select class="color">
				<option value="white" <?php if($step['color'] == 'white') echo 'selected="selected"'; ?>>White</option>
				<option value="red" <?php if($step['color'] == 'red') echo 'selected="selected"'; ?>>Red</option>
				<option value="yellow" <?php if($step['color'] == 'yellow') echo 'selected="selected"'; ?>>Yellow</option>
				<option value="green" <?php if($step['color'] == 'green') echo 'selected="selected"'; ?>>Green</option>
				<option value="blue" <?php if($step['color'] == 'blue') echo 'selected="selected"'; ?>>Blue</option>
			</select>
		</td>
		<td>
			<select class="tone">
				<option value="">None</option>
				<option value="single" <?php if($step['sound'] == 'single') echo 'selected="selected"'; ?>>Single</option>
				<option value="double" <?php if($step['sound'] == 'double') echo 'selected="selected"'; ?>>Double</option>
				<option value="triple" <?php if($step['sound'] == 'triple') echo 'selected="selected"'; ?>>Triple</option>
				<option value="short" <?php if($step['sound'] == 'short') echo 'selected="selected"'; ?>>Long</option>
			</select>
		</td>	
	</tr>
	<?php } ?>
	<tr class="nodrop nodrag"><td></td><td colspan="5" class="left"><span class="small button add_step" role="button">+ Add New Row</span></td></tr>

	<!-- EMPTY ROW TEMPLATE FOR ADDING NEW STEP ROWS -->
	<tr class="row_template">
		<td class="drag_handle">&#xe805;</td>
		<td><i class="delete_step smaller button icon-cancel" role="button"></span></td>
		<td><input type="text" class="name" placeholder="Name"></td>
		<td><input type="number" class="time" placeholder="30"><span>sec</span></td>
		<td>
			<select class="color">
				<option value="white">White</option>
				<option value="red">Red</option>
				<option value="yellow">Yellow</option>
				<option value="green">Green</option>
				<option value="blue">Blue</option>
			</select>
		</td>
		<td>
			<select class="tone">
				<option value="">None</option>
				<option value="single">Single</option>
				<option value="double">Double</option>
				<option value="triple">Triple</option>
				<option value="short">Long</option>
			</select>
		</td>	
	</tr>
	</table>
	

	<h5 class="repeat_container">Repeat all steps <input type="number" class="repeat" min="1" value="<?php if($set['info']['repeat']) echo $set['info']['repeat']; else echo '1'; ?>"> times</h5>
	
	<a class="special button save disabled" role="button" href='/?set=<?php echo urlencode($_GET['set']); ?>'>Use Timer</a>
	<span class="error"></span>


	<div class="share_container">
		<h5>Save/Share:</h5>
		<p>To save your timer, just bookmark the link below. All your settings are saved right there.</p>
		<input type="text" class="timer_url" value=''>
		<a class="small button short_url" role="button">Shorten URL</a>
		<a class="small button copy_url" role="button" data-clipboard-text=""><i class="icon-docs"></i></a>
		
		<a class="small button email_timer" role="button"><i class="icon-mail"></i></a>
	</div>
	
	
	<div class="ajax"></div>
</body>

</html>