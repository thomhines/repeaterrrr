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
	include_once('db.php');
	// LOAD JSON FROM SERVER
	$set_result = sql("SELECT * FROM `sets` WHERE `slug` = '".$get['set']."';");
	
	
	// COUNT HOW MANY TIMES TIMER IS USED
	if($set_result) sql("UPDATE `sets` SET `use_counter` = use_counter + 1 WHERE `slug` = '".$get['set']."';");
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
	
if(isset($set_result)) {
	// COUNT HOW MANY TIMES TIMER IS USED
	sql("UPDATE `sets` SET `use_counter` = use_counter + 1 WHERE `slug` = '".$get['set']."';");
	
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
<title>repeaterrrr | <?php if(isset($set['info']['title'])) echo $set['info']['title']; else echo 'The simple, clean, and easy repeating timer.'; ?></title>
<meta charset="utf-8" />
<meta name="description" content="The clean and easy repeating timer." />
<meta name="viewport" content="user-scalable=no, width=500">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="apple-touch-startup-image" href="img/favicons/startup.png" />
<link rel="apple-touch-icon" sizes="57x57" href="img/favicons/apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="114x114" href="img/favicons/apple-touch-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="72x72" href="img/favicons/apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="144x144" href="img/favicons/apple-touch-icon-144x144.png" />
<link rel="apple-touch-icon" sizes="60x60" href="img/favicons/apple-touch-icon-60x60.png" />
<link rel="apple-touch-icon" sizes="120x120" href="img/favicons/apple-touch-icon-120x120.png" />
<link rel="apple-touch-icon" sizes="76x76" href="img/favicons/apple-touch-icon-76x76.png" />
<link rel="apple-touch-icon" sizes="152x152" href="img/favicons/apple-touch-icon-152x152.png" />
<link rel="icon" type="image/png" href="img/favicons/favicon-196x196.png" sizes="196x196" />
<link rel="icon" type="image/png" href="img/favicons/favicon-160x160.png" sizes="160x160" />
<link rel="icon" type="image/png" href="img/favicons/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/png" href="img/favicons/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="img/favicons/favicon-16x16.png" sizes="16x16" />
<meta name="msapplication-TileColor" content="#b91d47" />
<meta name="msapplication-TileImage" content="img/favicons/mstile-144x144.png" />

<!-- <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,700' rel='stylesheet' type='text/css'> -->
<link rel="Stylesheet" href="/cinch/?files=/css/fonts.css,/css/style.scss&debug=true" type="text/css" media="all" />
<script src="/cinch/?files=[jquery],[html5shiv],!/js/jquery.noclickdelay.js,!/js/soundjs.min.js,/js/scripts.js&debug=false"></script>
<script type="text/javascript">
	// SAVE STEPS TO JAVASCRIPT VAR
	<?php if($set_result) { ?>var steps = <?php echo $json; ?>;<?php } ?>
</script>
</head>

<body <?php if(!isset($set_result)) echo 'class="red"'; ?>>
	<div class="container">
		<?php // IF NO SET IS GIVEN IN URL, PROVIDE SPLASH SCREEN
		if(!isset($set_result)) { ?>
		<div class="intro">
			<h1><img src="img/logo.svg" alt="repeaterrrr" width="200" onerror="this.onerror=null; this.src='img/logo.png'"></h1>
			<h5>The clean and easy repeating timerrrr.</h5>
			
			<p>Repeaterrrr lets you create no-frills timers for any activity that requires keeping track of time in regular intervals.</p>
			<p>Plus, all your timer settings are stored in the URL, so it's easy to create, customize and share.</p>
			<br>
			<h6>You can try out one of these example timers:</h6>
			<a class="small button" href="http://repeaterrrr.com/6LeTMUM">Pomodoro</a> <a class="small button" href="http://repeaterrrr.com/PKLaZA0">7-min Circuit Training</a> <a class="small button" href="http://repeaterrrr.com/FAC0IS3">10-20-30 Intervals</a> <a class="small button" href="http://repeaterrrr.com/uunoYIU">1 Minute Timer</a> <a class="small button" href="http://repeaterrrr.com/lGzsxUl">Tabata Interval Training</a> 

			<h6>OR</h6>		
			<a class="special button" href="/edit/">Make a timer</a>
		</div>
	</div>
	<footer>
		<a href="http://thomhines.com/">th</a>
		<a href="https://github.com/thomhines/repeaterrrr"><i class="icon-github-circled"></i></a>
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
			<h4>Well, you can check that off your list for today.<br>Or you could...</h4>
			<button class="start button" role="button">Do it again</button>
			<!-- SHOW SAVE TO HOMEPAGE INFO FOR IOS DEVICES -->
			<h6 class="ios">Like the timer? Add it to your home screen for easy access and to use it full screen!</h6>
		</div>
	
	
	</div>
	
	
	<footer>
		<a class="title" href="/"><h1><img src="img/logo.svg" alt="repeaterrrr" onerror="this.onerror=null; this.src='img/logo.png'"></h1></a>
		<a href='/edit/<?php echo $get['set']; ?>'><i class="icon-edit"></i></a>
		<a href='/copy/<?php echo $get['set']; ?>'><i class="icon-docs"></i></a>
		<a class="email_timer" target="_blank" href="mailto:?subject=<?php echo $set['info']['title']; ?> [repeaterrrr]&body=<?php echo $set['info']['title']; ?>%0d%0a<?php echo $set['info']['description']; ?>%0d%0a%0d%0ahttp://repeaterrrr.com/<?php echo $get['set']; ?>%0d%0a%0d%0a--%0d%0aRepeating timers by repeaterrrr%0d%0ahttp://repeaterrrr.com/"><i class="icon-mail"></i></a>
		<span class="icon-volume-up mute"></span>
	</footer>
	
	<div class="ajax"></div>
	
	
	<?php } // if($set_result) ?>
	
	
	<!-- GOOGLE ANALYTICS -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-46546607-1', 'thomhines.com');
	  ga('send', 'pageview');
	</script>

</body>



</html>