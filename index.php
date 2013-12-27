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
	
	// ADD 'GET READY' STEP TO BEGINNING OF STEP ARRAY
	array_unshift($set['steps'], array('title' => 'Get Ready', 'time' => 10, 'color' => 'white', 'sound' => 'none' ));
	
	// RECONVERT TO JSON FOR JAVASCRIPT TO HANDLE
	$json = json_encode($set['steps']);
}
?>
<!DOCTYPE html>
<html lang="en-us" manifest="cache.manifesto">
<head>
<title>Repeaterrrr | <?php if($set['info']['title']) echo $set['info']['title']; else echo 'The simple, clean, and easy repeating timer.'; ?></title>
<meta charset="utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="viewport" content="user-scalable=no, width=500">
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
<script src="/cinch/?files=[jquery],[html5shiv],!/js/jquery.noclickdelay.js,/js/scripts.js&debug=true"></script>
<script type="text/javascript">
	// SAVE STEPS TO JAVASCRIPT VAR
	<?php if($_GET['set']) { ?>var steps = <?php echo $json; ?>;<?php } ?>
</script>
</head>

<body <?php if(!$_GET['set']) echo 'class="blue"'; ?>>
	<div class="container">
		<?php // IF NO SET IS GIVEN IN URL, PROVIDE SPLASH SCREEN
		if(!$_GET['set']) { ?>
		<div class="intro">
			<h1>Repeaterrrr</h1>
			<h5>The clean and easy repeating timer.</h5>
			
			<p>Repeaterrrr lets you create no-frills timers for any activity that requires keeping track of time in regular intervals.</p>
			<p>Plus, all your timer settings are stored in the URL, so it's easy to create, customize and share.</p>
			<br>
			<h6>
				Feel free to try out one of these example timers:<br>
				<a class="small" href="http://bit.ly/1de0Ik5">Pomodoro</a> | <a class="small " href="http://bit.ly/1eJKtP7">7-min Circuit Training</a> | <a class="small " href="http://bit.ly/1cuYzjP">10-20-30 Intervals</a>
			</h6>
			<h6>OR</h6>		
			<p><a class="button" href="/edit/">Make a timer</a></p>
		</div>
	</div>
	<footer>
		<a href="https://github.com/thomhines/repeaterrrr"><i class="icon-github-circled"></i></a>
		<a href="http://thomhines.com/">thom</a>
	</footer>
		
		<?php } else { ?>
		
		<!-- TIMER INTRO SCREEN -->
		<div class="ready">
			<h2><?php echo $set['info']['title']; ?></h2>
			<h5><?php echo $set['info']['description']; ?></h5>
			<h4><br>Duration: <b><?php echo $duration; ?></b></h4>
			<div class="start button" role="button">Start</div>
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
			<span class="pause button" role="button"><i class="icon-pause"></i> pause</span> <span class="skip button" role="button"><i class="icon-fast-fw"></i> skip step</span>
		</div>
	
		<!-- TIMER COMPLETION SCREEN -->	
		<div class="complete">
			<h2>All done!</h2>
			<h4>Well, you can check that off your list for today.<br>Or you could...</h4>
			<div class="start button" role="button">Do it again</div>
		</div>
	
	
	</div>
	
	
	<footer>
		<a class="title" href="/"><h1>Repeaterrrr</h1></a>
		<a href='/edit/?set=<?php echo urlencode($_GET['set']); ?>'><i class="icon-edit"></i></a>
		<a class="email_timer"><i class="icon-mail"></i></a>
		<span class="icon-volume-up mute"></span>
	</footer>
	
	<!-- INVISIBLE ELEMENTS -->
	<audio class="sounds"><source src="/audio/sounds.mp3" type="audio/mp3" /><source src="/audio/sounds.ogg" type="audio/ogg" /></audio>
	<div class="ajax"></div>
	
	
	<?php } // if($_GET['set']) ?>
	
	
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