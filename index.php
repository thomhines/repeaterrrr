<?php
/*----------------------------------------------------------------------*

	REPEATERRRR
	http://repeaterrrr.com/
	https://github.com/thomhines/repeaterrrr
	
	The clean and easy repeating timer.

	Copyright 2014, Thom Hines
	MIT License
	
*----------------------------------------------------------------------*/

$set_result = null;
$show_edit_timer_button = false;

if (isset($_GET['set']) && $_GET['set']) {
	include_once('db.php');
	$set_result = sql("SELECT * FROM `sets` WHERE `slug` = '".$get['set']."' OR `edit_slug` = '".$get['set']."';");

	if ($set_result) {
		sql("UPDATE `sets` SET `use_counter` = `use_counter` + 1 WHERE `slug` = '".$set_result['slug']."';");

		$edit_slug_val = isset($set_result['edit_slug']) ? $set_result['edit_slug'] : '';
		$slug_val = isset($set_result['slug']) ? $set_result['slug'] : '';
		$show_edit_timer_button = ($edit_slug_val !== '' && $get['set'] === $edit_slug_val);
	}
}


// SAVE PAGE TO CACHE (THANKS CSS-TRICKS! http://css-tricks.com/snippets/php/intelligent-php-cache-control/)
/*
$lastModified=filemtime($_SERVER['SCRIPT_FILENAME']);
$etagFile = md5_file($_SERVER['SCRIPT_FILENAME']);
$ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
header("Etag: $etagFile");
header('Cache-Control: public');
if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified || $etagHeader == $etagFile) {
   header("HTTP/1.1 304 Not Modified");
   exit;
}
*/

if ($set_result) {
	// CONVERT JSON INTO PHP ARRAY
	$set = json_decode($set_result['json'], true);
	
	// MULTIPLY STEPS IF SET TO REPEAT
	$repeat_steps = array();
	if($set['info']['repeat']) {
		for($x = 0; $x < $set['info']['repeat']; $x++) {
			$repeat_steps = array_merge($repeat_steps, $set['steps']);
		}
		$set['steps'] = $repeat_steps;
	}
	
	// CALCULATE TOTAL DURATION OF SET
	$duration = 0;
	for($x = 0; $x < sizeof($set['steps']); $x++) {
		$duration += $set['steps'][$x]['time'];
	}
	if($duration <= 60) $duration = $duration . " sec";
	elseif($duration <= 3600) $duration = round($duration/60, 1) . " min";
	else $duration = round($duration/3600, 1) . " hr";
	
	// RECONVERT TO JSON FOR JAVASCRIPT TO HANDLE
	$json = json_encode($set['steps']);
}

?>
<!DOCTYPE html>
<html lang="en-us">
<head>
<title>repeaterrrr | <?php if (!empty($set_result) && !empty($set['info']['title'])) { echo htmlspecialchars($set['info']['title'], ENT_QUOTES, 'UTF-8'); } else { echo 'The simple, clean, and easy repeating timer.'; } ?></title>
<meta charset="utf-8" />
<meta name="description" content="The clean and easy repeating timer." />
<meta name="viewport" content="user-scalable=no, width=500">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="icon" href="img/favicons/favicon.ico" sizes="48x48">
<link rel="icon" type="image/png" sizes="32x32" href="img/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="img/favicons/favicon-16x16.png">
<link rel="apple-touch-icon" href="img/favicons/apple-touch-icon.png">
<link rel="manifest" href="img/favicons/site.webmanifest">
<meta name="theme-color" content="#ffffff">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="img/favicons/android-chrome-192x192.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/fonts.css" type="text/css" media="all" />
<link rel="Stylesheet" href="css/style.css" type="text/css" media="all" />
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="js/jquery.noclickdelay.js"></script>
<script src="js/soundjs.min.js"></script>
<script src="js/scripts.js"></script>

<script type="text/javascript">
	// SAVE STEPS TO JAVASCRIPT VAR
	<?php if ($set_result) { ?>var steps = <?php echo $json; ?>;<?php } ?>
</script>
</head>

<body <?php if (empty($set_result)) { echo 'class="home"'; } ?>>
	<div class="container">
		<?php // IF NO SET IS GIVEN IN URL, PROVIDE SPLASH SCREEN
		if (empty($set_result)) { ?>
		<div class="intro">
			<div class="logo">
				<img src="img/repeaterrrr_combo_dark.svg" alt="repeaterrrr" onerror="this.onerror=null; this.src='img/repeaterrrr_logo.svg'">
			</div>

			<div class="hero">
				<h2>Your timer,<br>on repeat.</h2>
				<p>Build a simple repeating timer for workouts, focus sessions, or anything that runs in intervals. Share it with a link.</p>
			</div>

			<a class="special button" href="/edit/">Make a timer</a>

			<div class="examples_label">or try an example</div>
			<div class="examples">
				<a class="small button" href="/oo4js8C">Pomodoro</a>
				<a class="small button" href="/EwP2j2w">10-20-30 Intervals</a>
				<a class="small button" href="/VvyLTYt">1 Minute Timer</a>
			</div>
		</div>
	</div>
	<footer>
		<span class="copyright">&copy; <?php echo date('Y'); ?> <a href="https://thomhines.com/">Thom Hines</a></span>
	</footer>
		
		<?php } else { ?>
		
		<!-- TIMER INTRO SCREEN -->
		<div class="ready">
			<h2><?php echo $set['info']['title']; ?></h2>
			<h5><?php echo $set['info']['description']; ?></h5>
			<h4><br>Duration: <b><?php echo $duration; ?></b></h4>
			<button class="start button" role="button">Start</button>
		</div>
	
	
		<!-- TIMER -->
		<div class="timer">
			<h2 class="current_activity"></h2>
			<div class="current_progress progress_bar">
				<span style="width: 0%;"></span>
			</div>
			<div class="total_progress progress_bar">
				<span style="width: 0%;"></span>
			</div>
			<h3 class="clock"><span class="seconds"></span>/<span class="total"></span> seconds</h3>
			<h5>Up Next:</h5>
			<h4 class="next_activity"></h4>
			<button class="pause button" role="button"><i class="icon-pause"></i> pause</button> <button class="skip button" role="button"><i class="icon-fast-fw"></i> skip step</button>
		</div>
	
		<!-- TIMER COMPLETION SCREEN -->	
		<div class="complete">
			<h2>All done!</h2>
			<h4>Well, you can check that off your list for today.</h4>
			<h5>Or you could...</h5>
			<button class="start button" role="button">Do it again</button>
			<!-- SHOW SAVE TO HOMEPAGE INFO FOR IOS DEVICES -->
			<h6 class="ios">Like the timer? Add it to your home screen for easy access and to use it full screen!</h6>
		</div>
	
	
	</div>
	
	
	<footer>
		<a class="title" href="/"><h1><img src="img/repeaterrrr_logo.svg" alt="repeaterrrr" onerror="this.onerror=null; this.src='img/repeaterrrr_logo.svg'"></h1></a>
		<div class="timer_footer_actions">
			<a class="timer_action timer_action_new" href="/edit/">
				New Timer
			</a>
			<?php if ($show_edit_timer_button) { ?>
			<a class="timer_action" href="/edit/<?php echo htmlspecialchars($set_result['edit_slug'], ENT_QUOTES, 'UTF-8'); ?>">
				<i class="icon-edit" aria-hidden="true"></i>
				<span class="timer_action_label">Edit</span>
			</a>
			<?php } ?>
			<a class="timer_action" href="/copy/<?php echo htmlspecialchars($set_result['slug'], ENT_QUOTES, 'UTF-8'); ?>">
				<i class="icon-docs" aria-hidden="true"></i>
				<span class="timer_action_label">Duplicate</span>
			</a>
			<?php
			$share_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'repeaterrrr.com';
			$share_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
			$share_timer_url = $share_scheme . '://' . $share_host . '/' . $set_result['slug'];
			?>
			<button type="button" class="share_timer timer_action" aria-haspopup="dialog" aria-expanded="false" aria-controls="share_timer_popup" aria-label="Share timer">
				<i class="icon-share" aria-hidden="true"></i>
				<span class="timer_action_label">Share</span>
			</button>
			<div class="timer_action timer_action_mute" role="button" tabindex="0" aria-label="Mute">
				<i class="mute icon-volume-up" aria-hidden="true"></i>
				<span class="timer_action_label">Mute</span>
			</div>
		</div>
	</footer>

	<div id="share_timer_popup" class="share_popup" role="dialog" aria-modal="true" aria-labelledby="share_timer_popup_title" aria-hidden="true">
		<div class="share_popup_backdrop" tabindex="-1"></div>
		<div class="share_popup_panel">
			<button type="button" class="share_popup_close" aria-label="Close"><i class="icon-cancel"></i></button>
			<h3 id="share_timer_popup_title">Share this timer</h3>
			<p class="share_popup_note">This link opens the view-only timer. Others can run it and make their own copy; they cannot edit your original.</p>
			<label class="share_popup_label" for="share_timer_url_field">Link</label>
			<div class="share_popup_url_wrap">
				<input type="text" id="share_timer_url_field" class="share_popup_url" readonly value="<?php echo htmlspecialchars($share_timer_url, ENT_QUOTES, 'UTF-8'); ?>">
				<button type="button" class="share_popup_copy">Copy</button>
			</div>
		</div>
	</div>
	
	<div class="ajax"></div>
	
	
	<?php } // if($set_result) ?>


</body>



</html>