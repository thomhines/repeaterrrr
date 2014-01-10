/*----------------------------------------------------------------------*

	REPEATERRRR
	http://repeaterrrr.com/
	https://github.com/thomhines/repeaterrrr
	
	The clean and easy repeating timer.

	Copyright 2014, Thom Hines
	MIT License
	
*----------------------------------------------------------------------*/


var timer_interval, current_step, current_seconds, prev_seconds, next_step, step_seconds = 0, prev_time, total_seconds = 0, total_seconds_so_far = 0, sound_start, sound_length;


$(function() {

	/*----------------------------------------------------------------------*
	
		TIMER PAGE SCRIPTS
	
	*----------------------------------------------------------------------*/
	if($('.timer').size()) {
	
		// OPTIMIZE FOR TOUCH SCREENS
		$('body').on('touchmove', function(e) { // DISABLE SCROLLING ON TOUCH DEVICES
			e.preventDefault();
		});
		$('.button').bind('touchstart',function(){ // ADD HOVER STATE TO BUTTONS
		    $(this).addClass('button_hover');
		});
		$('.button').bind('touchend',function(){
		    $(this).removeClass('button_hover');
		});
		
		// CALCULATE TOTAL TIME OF TIMER
		if($('.timer').size()) {
			for(var x = 0; x < steps.length; x++) {
				total_seconds += parseInt(steps[x]['time']);
			}
		}
		
		// TEXT FORMATTING
		fixHeaderFontSize(); // adjust font size of title
		$('.ready h5').html(markDown($('.ready h5').html())); // convert descript from markdown to html
		
		// IF RUN AS A FULL SCREEN APP IN MOBILE SAFARI, HIDE 'ADD TO HOME' MESSAGE ON TIMER PAGE
		if(window.navigator.standalone) {
			$('.ios').remove();
		}
	
	}	
		
	// ADD BUTTON ACTIONS
	//----------------------------------------------------------------------
	
		
	
	$('.start').click(function() {
		$('.sounds')[0].play(); // PRE-LOAD AUDIO FILE
		$('.sounds')[0].pause();
		init();
	});
	
	$('.skip').click(function() {
		nextStep();		
	});
	
	$('.pause').click(function() {
		// IF NOT PAUSED, PAUSE
		if($('.pause').text().trim() == 'pause') {
			clearInterval(timer_interval);
			$('.pause').html('<i class="icon-play"></i> resume');
		// VICE VERSA
		} else {
			timer_interval = setInterval(step, 100);
			$('.pause').html('<i class="icon-pause"></i> pause');
			prev_time = new Date()
		}
	});
	
	// CREATE SHORT LINK TO TIMER AND ADD TO MAILTO LINK
	$('.email_timer').click(function(e) {
		e.preventDefault();
		emailLink();
	});
	
	// ENABLE/DISABLE SOUNDS
	$('.mute').click(function() {
		if($(this).hasClass('muted')) {
			$(this).attr('class', 'mute').addClass('icon-volume-up');
		} else {
			$(this).attr('class', 'mute muted').addClass('icon-volume-off');
		}
	});
	
	




	// FUNCTIONS
	//----------------------------------------------------------------------

	// INITIALIZE TIMER, RESET VALUES
	function init() {
		current_step = -1;
		current_seconds = 0;
		step_seconds = 0;
		prev_time = new Date();
		total_seconds_so_far = 0;
		if(steps[1]) next_step = steps[1]['title'];
		else next_step = "DONE!";
		
		$('.current_activity').html(steps[0]['title']);
		$('.next_activity').html(next_step);
		fixHeaderFontSize();

		$('.progress_bar span').width(0);
		
		if($('.ready').is(':visible')) {
			$('.ready').fadeOut(300, function() {
				timer_interval = setInterval(step, 100);
			});
		} else {
			$('.complete').fadeOut(300, function() {
				timer_interval = setInterval(step, 100);
			});			
		}
		
	}
	
	// CONVERT BASIC MARKDOWN SYNTAX TO HTML
	function markDown(text) {
		text = text.replace(/\[([^\]]*)]\(([^)]*)\)/g, '<a href="$2" target="$1">$1</a>');
		text = text.replace(/\*\*(.*)\*\*/g, '<b>$1</b>');
		text = text.replace(/\*(.*)\*/g, '<i>$1</i>');
		return text;
	}
	
	// ADJUST CLOCK AND PROGRESS BARS EACH 1/10TH OF SECOND
	function step() {
		$('.timer').fadeIn(300);
		
		// ONCE CURRENT STEP HAS 0 SECONDS LEFT, GO TO NEXT STEP
		if(current_seconds <= 0) {
			nextStep();
		
		// UPDATE CLOCK AND PROGRESS BARS
		} else {
			var current_percent = 100-(current_seconds/step_seconds*100);
			if(current_percent > 100) current_percent = 100;
			updateClock();
		}
		
		// SMOOTH OUT STEP PROGRESS BAR MOTION
		// IF TIMER IS FOR LONGER PERIOD OF TIME, REMOVE ANIMATION TO REDUCE WEIRD WOBBLY MOVEMENT
		if(step_seconds >= 25) $('.current_progress span').css({width: current_percent+'%'});
		else $('.current_progress span').animate({width: current_percent+'%'}, 90);
		
		$('.total_progress span').css({width: (total_seconds_so_far/total_seconds*100)+'%'});

		// PLAY SHORT BEEP AT 1 AND 2 SECOND REMAINING MARK
		if(Math.ceil(current_seconds) != Math.ceil(prev_seconds) && (Math.ceil(current_seconds) == 2 || Math.ceil(current_seconds) == 1)) {
			playSound('tock');
		}
		prev_seconds = current_seconds;
		
		// COUNT DOWN .1 SEC
		var now = new Date();
		var elapsed = (now.getTime() - prev_time.getTime())/1000;
		prev_time = new Date();
		current_seconds -= elapsed;
		total_seconds_so_far += elapsed;
	}
	
	// UPDATES INFO TO NEXT STEP IN INTERVAL TIMER 
	function nextStep() {
			
		// CALCULATE TOTAL SECONDS BASED ON CURRENT AND PREVIOUS STEPS
		total_seconds_so_far = 0;
		for(x = 0; x < current_step+1; x++) {
			total_seconds_so_far += steps[x]['time'];
		}
		
		// MOVE STEP PROGRESS BAR BACK TO 0
		$('.current_progress span').stop().animate({width: '0'}, 30);

		// IF WE JUST COMPLETED THE LAST STEP, TURN OFF TIMER, CLEAR EVERYTHING AND SHOW 'COMPLETED' SCREEN
		if(current_step == steps.length-1) {
			playSound('long');
			clearInterval(timer_interval);
			//$(".container").attr('class', 'container');
			$('body').attr('class', '');
			$('body').addClass('white');
			$('.progress_bar span').stop().css({width: '100%'});
			$('.timer').fadeOut(1000, function() {
				$('.complete').fadeIn(300);
				fixHeaderFontSize();
			});
			$('title').html("repeaterrrr | " + $('.ready h2').text());
			
		// OTHERWISE, LOAD UP NEXT STEP
		} else {
			current_step++;
			current_seconds = steps[current_step]['time'];
			step_seconds = current_seconds;
			if(steps[current_step]['sound']) {
				playSound(steps[current_step]['sound']);	
			}
			$('.current_activity').html(steps[current_step]['title']);
			fixHeaderFontSize();
			//$(".container").attr('class', 'container');
			$('body').attr('class', '');
			$('body').addClass(steps[current_step]['color']);
			$('.total').html(step_seconds);
			if(!steps[current_step+1]) $('.next_activity').html('DONE!'); // if next to last step, set next step to 'done!'
			else $('.next_activity').html(steps[current_step+1]['title']);
		}		
	}
	
	// UPDATE COUNTDOWN TIMER
	var last_seconds;
	function updateClock() {
		remaining_seconds = Math.ceil(current_seconds);
		// IF TIME IS UNCHANGED, SKIP REST OF STEPS
		if(last_seconds == remaining_seconds) return null;
		
		// IF STEP IS MORE THAN 2 MIN, USE CLOCK STYLE TIMER
		if(step_seconds > 99) {
	 		var curr_min = Math.floor(current_seconds/60);
	 		var curr_sec = Math.ceil(current_seconds%60);

	 		if(curr_sec < 10) curr_sec = "0"+curr_sec;
	 		var step_min = Math.floor(step_seconds/60);
	 		var step_sec = Math.ceil(step_seconds%60);
	 		if(step_sec < 10) step_sec = "0"+step_sec;
	 		
		 	$('.clock').html('<span class="seconds">'+curr_min+':'+curr_sec+'</span>/<span class="total">'+step_min+':'+step_sec+'</span>');
		 	
		// OTHERWISE, JUST SHOW REMAINING SECONDS
		} else $('.clock').html('<span class="seconds">'+remaining_seconds+'</span>/<span class="total">'+step_seconds+'</span> seconds');
		
		
		$('title').html($('.seconds').text()+" | "+steps[current_step]['title']);
		
		last_seconds = remaining_seconds;
	}
	
	// ADJUST TITLE FONT SIZE BASED ON NUMBER OF CHARACTERS IN TITLE
	function fixHeaderFontSize() {
		$('h2:visible').each(function() {
			if($(this).text().length < 10) $(this).css('font-size', '3.1em'); 
			else if($(this).text().length < 15) $(this).css('font-size', '2.25em');
			else if($(this).text().length < 20) $(this).css('font-size', '1.75em');
			else if($(this).text().length < 25) $(this).css('font-size', '2.5em');
			else if($(this).text().length < 35) $(this).css('font-size', '1.1em');
			else if($(this).text().length < 45) $(this).css('font-size', '.9em');
		});
	}

	// PLAY SOUND
	function playSound(sound) {
		// IN ORDER TO WORK ON MOBILE DEVICES, A SINGLE AUDIO FILE IS USED FOR ALL SOUNDS
		// BELOW ARE THE START AND LENGTH VALUES FOR EACH SOUND USED WITHIN THE AUDIO
		// SPRITE. VALUES ARE IN SECONDS.
		
		var audio = $('.sounds').get(0);
		
		if(sound == 'none' || $('.mute').hasClass('muted')) { return null; }
		else if(sound == 'tock') { sound_start = .07; sound_length = .2; }
		else if(sound == 'single') { sound_start = .9; sound_length = .2; }
		else if(sound == 'double') { sound_start = 1.65; sound_length = .3; }
		else if(sound == 'triple') { sound_start = 2.53; sound_length = .5; }
		else if(sound == 'short') { sound_start = 3.63; sound_length = .6; }
		else if(sound == 'long') { sound_start = 5; sound_length = 1.2; }
		else { sound_start = 0; sound_length = 0; }
		
		console.log(audio.readyState); // HELPS MOBILE SAFARI NOT ERROR OUT (WTF???)
		
		if(audio.readyState == 4) {
			audio.currentTime = sound_start;
			audio.play();
		}
	}
	
	// STOP SOUND AFTER IT HAS PLAYED ITS ENTIRE LENGTH
	function stopSound() {
		if($('.sounds').get(0).currentTime >= sound_start + sound_length) {
			$('.sounds').get(0).pause();
		}
	}
	if($('.sounds').size()) {
		$('.sounds').get(0).addEventListener('timeupdate', stopSound, false);

	}









	/*----------------------------------------------------------------------*
	
		EDITOR PAGE SCRIPTS
	
	*----------------------------------------------------------------------*/
	
	if($('.editor').size()) {
	
		// UPDATE LINKS ON LOAD
		//updateLinks();
		verifyForm();
	
	
		// BUTTON ACTIONS
		//----------------------------------------------------------------------
		
		// UPDATE TIMER URL WHEN ANY INFO IS UPDATED
		$(document).on('change keyup paste', 'input:not(.timer_url), textarea, select', function() {
			//updateLinks();
			verifyForm();
		});
		
		// CHANGE COLOR OF ROW IF COLOR SELECTOR IS CHANGED
		$(document).on('change', 'select.color', function() {
			$(this).closest('tr').attr('class', $(this).val());
		});
		
		// INPUT BOX CHARACTER LIMITS
		$('.title_char_count').html($('.title').val().length);
		$('.title').keyup(function(e) {
			$('.title_char_count').html($('.title').val().length);
		});

		$('.description_char_count').html($('.description').val().length);
		$('.description').keyup(function() {
			$('.description_char_count').html($('.description').val().length);
		});
		
			
		// SELECT CONTENTS OF TIMER URL INPUT BOX WHEN FOCUSED ON
		$('.timer_url').on('focus, click', function() {
			this.select();
		});
		
		// MAKE TIMER STEPS SORTABLE VIA DRAG AND DROP
		if($('.steps').size()) {
			$('.steps').tableDnD({
			    dragHandle: ".drag_handle",
			    onDragClass: 'dragging',
			    onDrop: function(table, row) {
			       //updateLinks();
					verifyForm();
			    }
			});
		} 
		
		
		// ADD NEW STEP ROW
		$('.add_step').click(function() {
			$('.row_template').clone().insertBefore($(this).closest('tr')).removeClass('row_template');
			$('.steps').tableDnD({
			    dragHandle: ".drag_handle",
			    onDragClass: 'dragging',
			    onDrop: function(table, row) {
			       //updateLinks();
					verifyForm();
			    }
			});
		});
		
		// DELETE STEP ROW
		$(document).on('click', '.delete_step', function() {
			$(this).closest('tr').fadeOut(300, function() {
				$(this).remove();
				//updateLinks();
				verifyForm();
			});
		});
	
	
		// ON SAVE, CREATE SHORT LINK AND DIRECT USER TO NEW TIMER
		$(document).on('click', '.save', function(e) {
			e.preventDefault();
			var json_url = makeJson();
			$(this).addClass('working').html('Saving...');
			
			$.ajax({
				type: "POST",
				url: "/ajax.php", // shortens URL and stores it to database
				data: { json: json_url }
			})
			.done(function(response) {
				window.location = "/"+response;
			});
			
			
		});
			
	}
	
	
	
	
	// FUNCTIONS
	//----------------------------------------------------------------------
	

	// MAKE SURE MINIMUM REQUIREMENTS ARE MET, OTHERWISE DISABLE SAVE BUTTON
	function verifyForm() {
		var error_msg = "";
		
		var valid_row = false
		$('table tr').each(function() {
			if($(this).find('.name').val() && $(this).find('.time').val()) {
				valid_row = true;
			}
		});
		if(!valid_row) error_msg = 'This timer needs at least one working step!';
		if($('.title').val() == "") error_msg = 'This timer needs a title!';
		
		
		if(error_msg) {
			$('.error_message').html(error_msg);
			$('.save, .short_url, .copy_url, .email_timer').addClass('disabled');
		}
		else $('.save, .short_url, .copy_url, .email_timer').removeClass('disabled');
	}

	// CREATE JSON BASED ON FORM DATA
	function makeJson(title, description, steps) {
		var json = '{"info":{"title":"'+encodeUrlEntities($('.title').val())+'","description":"'+encodeUrlEntities($('.description').val())+'","repeat":'+encodeUrlEntities($('.repeat').val())+'},"steps":[';
		var valid_row = false
		$('table tr').each(function() {
			if($(this).find('.name').val() && $(this).find('.time').val()) {
				json += '{"title":"'+encodeUrlEntities($(this).find('.name').val())+'","time":'+$(this).find('.time').val()+',"color":"'+$(this).find('.color').val()+'","sound":"'+$(this).find('.tone').val()+'"},';
				valid_row = true;
			}
		});
		if(!valid_row) $('.error_message').html('This timer needs at least one working step!');
		json = json.substring(0, json.length - 1);
		json += "]}";
		return json;
	}
		
	// FIX ENCODING OF SPECIAL CHARACTERS WHEN USING IN URL
	function encodeUrlEntities(string) {
		if(!string) return '';
		var new_string = string.replace(/(["])/g, '\\$1');
		new_string = new_string.replace(/#/g, '%23');
		new_string = new_string.replace(/&/g, '%26');
		return new_string;
	}
});
