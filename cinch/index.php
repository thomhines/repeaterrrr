<?php

/*----------------------------------------------------------------------*

	Cinch
	https://github.com/thomhines/cinch
	
	A simple, streamlined plugin to minimize and cache JS/CSS files

	Copyright 2013, Thom Hines
	MIT License
	
*----------------------------------------------------------------------*/

// GZIP CONTENT WHEN AVAILABLE
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); 
else ob_start(); 

if(!isset($_GET['files'])) exit(); // if no files have been selected, stop here


// PREPARE VARIABLES
$file_array = explode(',', $_GET['files']); 
$cachefile = 'cache/'.md5(implode(",", $_GET)); // build cache filename based on current parameters
$filepath = $_SERVER['SCRIPT_FILENAME']; // set path to site root folder
$filepath = str_replace('cinch/index.php', '', $filepath); // remove reference to cinch folder

$libraries= array( // array(library URL, default version number, css/js)
	'960gs' => array('https://raw.github.com/nathansmith/960-Grid-System/master/code/css/960.css', '1.0', 'css'),
	'angular' => array('https://ajax.googleapis.com/ajax/libs/angularjs/{version}/angular.min.js', '1.2.4', 'js'),
	'bootstrap-css' => array('http://netdna.bootstrapcdn.com/bootstrap/{version}/css/bootstrap.min.css', '3.0.3', 'css'),
	'bootstrap-theme-css' => array('http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css', '3.0.3', 'css'),
	'bootstrap-js' => array('http://netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js', '3.0.3', 'js'),
	'chrome-frame' => array('https://ajax.googleapis.com/ajax/libs/chrome-frame/{version}/CFInstall.min.js', '1.0.3', 'js'),
	'cssreset' => array('http://meyerweb.com/eric/tools/css/reset/reset.css', '2.0', 'css'),
	'cycle2' => array('http://malsup.github.io/min/jquery.cycle2.min.js', '20131022', 'js'),
	'dojo' => array('https://ajax.googleapis.com/ajax/libs/dojo/{version}/dojo/dojo.js', '1.9.1', 'js'),
	'ext-core' => array('https://ajax.googleapis.com/ajax/libs/ext-core/{version}/ext-core.js', '3.1.0', 'js'),
	'foldy960' => array('https://raw.github.com/davatron5000/Foldy960/master/style.css', '1.0', 'css'),
	'foundation-css' => array('libraries/foundation/{version}/foundation.min.css', '5.0.2', 'css'),
	'foundation-js' => array('libraries/foundation/{version}/foundation.min.js', '5.0.2', 'js'),
	'html5shiv' => array('http://html5shiv.googlecode.com/svn/trunk/html5.js', '3.7.0', 'js'),
	'isotope-css' => array('https://raw.github.com/desandro/isotope/master/css/style.css', '1.2.25', 'css'),
	'isotope-js' => array('https://raw.github.com/desandro/isotope/master/jquery.isotope.min.js', '1.2.25', 'js'),
	'jquery' => array('https://ajax.googleapis.com/ajax/libs/jquery/{version}/jquery.min.js', '1.10.2', 'js'),
	'jqueryui' => array('https://ajax.googleapis.com/ajax/libs/jqueryui/{version}/jquery-ui.min.js', '1.10.3', 'js'),
	'kube' => array('http://imperavi.com/css/kube.css', '2.0.0', 'css'),
	'masonry' => array('http://masonry.desandro.com/masonry.pkgd.min.js', '3.1.3', 'js'),
	'modernizr' => array('http://modernizr.com/downloads/modernizr-latest.js', '2.7.1', 'js'),
	'mootools' => array('https://ajax.googleapis.com/ajax/libs/mootools/{version}/mootools-yui-compressed.js', '1.4.5', 'js'),
	'normalize' => array('http://necolas.github.io/normalize.css/{version}/normalize.css', '2.1.3', 'css'),
	'prototype' => array('https://ajax.googleapis.com/ajax/libs/prototype/{version}/prototype.js', '1.7.1.0', 'js'),
	'pure' => array('http://yui.yahooapis.com/pure/{version}/pure-min.css', '0.3.0', 'css'),
	'scriptaculous' => array('https://ajax.googleapis.com/ajax/libs/scriptaculous/{version}/scriptaculous.js', '1.9.0', 'js'),
	'swfobject' => array('https://ajax.googleapis.com/ajax/libs/swfobject/{version}/swfobject.js', '2.2', 'js'),
	'webfont' => array('https://ajax.googleapis.com/ajax/libs/webfont/{version}/webfont.js', '1.5.0', 'js'),
	'yui-reset' => array('http://yui.yahooapis.com/{version}/build/cssreset/cssreset-min.css', '3.14.0', 'css'),
);	



if($_GET['min'] === "false") $_GET['min'] = false;


// USE CORRECT CONTENT TYPE 
if($_GET['t']=='auto' || !isset($_GET['t'])) { // auto detect content type
	if(substr($file_array[0], 0, 1) == '[') {
		preg_match("/\[([^\/]*)\/?(.*)?\]/", $file_array[0], $library_array);
		$library_info = $libraries[$library_array[1]];
	}
	
	if(substr($file_array[0], -3) == '.js' || substr($file_array[0], -7) == '.coffee' || $library_info[2] == 'js') $_GET['t'] = 'js';
	else $_GET['t'] = 'css';
} 

if($_GET['t']=='js') { // JS
	header("Content-type: application/x-javascript; charset: UTF-8"); 
	$cachefile .= '.js';
}
else { // otherwise, assume CSS
	header("Content-type: text/css; charset: UTF-8");
	$cachefile .= '.css';
}


if(file_exists($cachefile)) $timestamp = filemtime($cachefile);
else $timestamp = 0;
$new_changes = false;

// CLEAR CACHE IF CACHE IS OLDER THAN ONE MONTH
$one_month_ago = strtotime("-1 month");
if($timestamp != 0 && $timestamp < $one_month_ago) {
	clearCache();
	$new_changes = true;
} 

// CHECK TO SEE IF CACHE IS OLDER THAN ALL OF THE FILES
else {
	foreach($file_array as $file) {
		if(substr($file,0,1) == "!") $file = substr($file,1); // remove '!'
		if(is_file($filepath.$file)) if(filemtime($filepath.$file) > $timestamp) $new_changes = true;
	}
}


// IF NEW CHANGES HAVE BEEN DETECTED, REBUILD CACHE FILE
if($new_changes || $_GET['force'] || $_GET['clearcache']) {

	$content = '';
	foreach($file_array as $file) { // combine files
		
		// MINIFY FILES
		$compress_file = isset($_GET['min']) ? $_GET['min'] : true;
		if(substr($file,0,1) == "!") { // don't minify file if marked with an '!'
			$compress_file = false;
			if(substr($file,0,1) == "!") $file = substr($file,1); // remove '!' from the front of filename
		}
				
		// LOAD AND READ FILE
		if(substr($file, 0, 1) == '[') { // LOAD A FILE FROM EXTERNALLY HOSTED LIBRARY
			$compress_file = false;
			$temp_content = loadExternalLibrary($file);
			if(!$temp_content) $error .= "'".$file."' is not a valid library name.\n";
		}
	
		else { // LOAD A LOCAL FILE
			if(!is_file($filepath.$file)) $error .= "'".$file."' is not a valid file.\n";
			else {
				if(!$handle = fopen($filepath.$file, "r")) $error .= "There was an error trying to open '".$file."'.\n";
				else {
					$temp_content = "";
					if(filesize($filepath.$file) == 0) $error .= "'".$file."' is an empty file.\n";
					elseif(!$temp_content = fread($handle, filesize($filepath.$file))) $error .= "There was an error trying to open '".$file."'.\n";
					fclose($handle);
				}
			}
		}
				
		// NO ERRORS, LOAD FILE
		if($temp_content) {
			
			// IF SASS, PROCESS AND CONVERT TO CSS
			if(substr($file, -5) == '.scss' || substr($file, -5) == '.sass') $temp_content = convertSASS($temp_content, $file);
			
			// IF LESS, PROCESS AND CONVERT TO CSS
			if(substr($file, -5) == '.less') $temp_content = convertLESS($temp_content, $file);

			// IF COFFEESCRIPT, PROCESS AND CONVERT TO JS
			if(substr($file, -7) == '.coffee') $temp_content = convertCoffee($temp_content);

			// MINIFY/PACK CONTENT		
			if($compress_file && $_GET['t']=='js'){	// Javascript		
				if($compress_file === 'pack') {
					require_once('processors/packer-1.1/class.JavaScriptPacker.php');
					$packer = new JavaScriptPacker($temp_content, 'Normal', true, false);
					$temp_content = $packer->pack();
				} else {
					require_once('processors/jsShrink.php');
					$temp_content = jsShrink($temp_content);
				
				}
			} elseif($compress_file) { //CSS
				require_once('processors/css_optimizer/css_optimizer.php');
				$css_optimizer = new css_optimizer();
				$temp_content = $css_optimizer->process($temp_content);
			}

			// FIX LINKS TO EXTERNAL FILES IN CSS
			if($_GET['t'] == 'css') {
				$path = "../".dirname($file)."/"; // trailing slash just in case user didn't add a leading slash to their CSS file path
				$temp_content = preg_replace("/url\s?\(['\"]?((?!http|\/)[^'\"\)]*)['\"]?\)/", "url(".$path."$1)", $temp_content); // if path is absolute, leave it alone. otherwise, relink assets based on path from css file
			}
			
			if($_GET['debug'] && $temp_content) $temp_content = "/* $file */\n".$temp_content."\n\n";
			if($temp_content) $content .= $temp_content;
		}			
	}	
	
	// OUTPUT FILE
	if(!$handle = fopen($cachefile, 'w')) {
		 echo "ERROR: Cannot open cache file.\n";
		 exit;
	}
	if(fwrite($handle, $content) === FALSE) {
		echo "ERROR: Cannot write to cache file.\n";
		exit;
	}
	fclose($handle);

	$timestamp = filemtime($cachefile);
} 



// SET HEADERS TO CACHE FILES PROPERLY IN BROWSER
$gmt_mtime = gmdate('r', $timestamp);
header('ETag: "'.md5($timestamp.$cachefile).'"');
header('Last-Modified: '.$gmt_mtime);
header('Cache-Control: public');



// IF THERE IS A NEW CACHE FILE, SEND NEW CONTENT
if($content) {
	if($_GET['debug'] && isset($error)) echo "/*\n\nERROR:\n$error \n*/\n\n";
	echo $content;
}



// IF FILE IS ALREADY IN USER'S CACHE, SEND 304 NOT MODIFIED HEADER
elseif ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5($timestamp.$cachefile)) {
	header('HTTP/1.1 304 Not Modified');		
	exit();
}



// OTHERWISE, LOAD CACHED FILE AND SEND TO USER
else {
	if(!$handle = fopen($cachefile, "r")) {
		echo "/*\nERROR: There was an error trying to open '".$cachefile."'.\n*/\n\n";
		exit;
	}
	$content = fread($handle, filesize($cachefile));
	fclose($handle);
	
	if($_GET['debug'] && isset($error)) echo "/*\nERROR:\n$error \n*/\n\n";
	echo $content;	
}

ob_end_flush();



/*----------------------------------------------------------------------*
	FUNCTIONS
*----------------------------------------------------------------------*/

// PARSES SASS/SCSS
function convertSASS($src, $file) {
	
	$path = "../".dirname($file)."/";
	// CHECK TO SEE IF FILE USES OLD SCHOOL INDENTATION STYLE
	if(strpos($src, "{") === false) {
		// IF SO, CONVERT IT TO SCSS
		require_once('processors/sass.php');
		$src = sassToScss($src);
	} 
	
	require_once('processors/scss/scss.inc.php');
	$scss = new scssc();
	$scss->addImportPath($path);
	try {
		$css = $scss->compile($src);	
	} catch(Exception $e) {
		if($_GET['debug']) echo "/*\nERROR: $file\n" . $e->getMessage() . " \n*/\n\n";
	}
	return $css;
}

// PARSES LESS
function convertLESS($src, $file) {
	
	$path = "../".dirname($file)."/";
	require_once('processors/less/lessc.inc.php');
	$less = new lessc();
	$less->addImportDir($path);
	try {
		$css = $less->compile($src);	
	} catch(Exception $e) {
		if($_GET['debug']) echo "/*\nERROR: $file\n" . $e->getMessage() . " \n*/\n\n";
	}
	return $css;
}

// PARSES COFFEESCRIPT
function convertCoffee($src) {	
	require_once('processors/coffeescript/Init.php');
	CoffeeScript\Init::load();
	return CoffeeScript\Compiler::compile($src);
}


// RETRIEVE LIBRARY FILE FROM EXTERNALLY HOSTED LIBRARIES
function loadExternalLibrary($file) {
	global $libraries, $error;
	
	preg_match("/\[([^\/]*)\/?(.*)?\]/", $file, $file_array);
	$library_name = strtolower($file_array[1]);
	$version = $file_array[2] ? $file_array[2] : $library[1];
	
	$library = $libraries[$library_name];
	$library_url =  str_replace('{version}', $version, $library[0]);
	$local_library_url = "libraries/$library_name/{$library[1]}/".basename($library_url);
	
	// IF SERVER CAN'T REMOTELY ACCESS FILES, OR NO VERSION NUMBER IS SELECTED, USE LOCAL VERSION
	if(ini_get("allow_url_fopen") == 0 || !$file_array[2]) {
		if(ini_get("allow_url_fopen") == 0) $error .= "Your server does not allow for access to remote libraries. A local version of $library_name was used instead.\n";
		$library_url = $local_library_url; // reset url to local version
	}
	
	// GET FILE CONTENTS	
	$file_contents = @file_get_contents($library_url);
	
	// IF FILE CONTENTS FAILED TO LOAD, GET LOCAL VERSION
	if(!$file_contents) {
		$error .= "The external library $library_name could not be loaded. A local version was used instead.\n";
		$file_contents = @file_get_contents($local_library_url);
	}
	
	return $file_contents;
}

// DELETES ALL CACHE FILES
function clearCache() {
	$files = glob('cache/*.*s'); // select all .js and .css files
	foreach($files as $file){
		if(is_file($file)) unlink($file);
	}
}

?>