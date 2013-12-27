/*
jQuery no-click-delay
by mmastrac
https://github.com/mmastrac/jquery-noclickdelay
Apache License
Version 2.0, January 2004
http://www.apache.org/licenses/
*/
(function($,window,document){"use strict";if(!window.navigator.userAgent.match(/(iPhone|iPad|iPod)/)){return;}
var CONFIG={TOUCH_MOVE_THRESHHOLD:10,PRESSED_CLASS:"pressed",GHOST_CLICK_TIMEOUT:500,GHOST_CLICK_THRESHOLD:10},clicks=[];function withinDistance(x1,y1,x2,y2,distance){return Math.abs(x1-x2)<distance&&Math.abs(y1-y2)<distance;}
document.addEventListener('click',function(e){for(var i=0;i<clicks.length;i++){if(withinDistance(clicks[i][0],clicks[i][1],e.clientX,e.clientY,CONFIG.GHOST_CLICK_THRESHOLD)){e.preventDefault();e.stopPropagation();return;}}},true);$(document).on('touchstart','.button',function(e){var elem=$(this);elem.css('webkitTapHighlightColor','rgba(0,0,0,0)');elem.addClass(CONFIG.PRESSED_CLASS);var touch=e.originalEvent.touches[0];var location=[touch.clientX,touch.clientY];this.__eventLocation=location;this.__onTouchMove=function(e){var touch=e.originalEvent.touches[0];if(withinDistance(touch.clientX,touch.clientY,location[0],location[1],CONFIG.TOUCH_MOVE_THRESHHOLD)){elem.addClass(CONFIG.PRESSED_CLASS);}else{elem.removeClass(CONFIG.PRESSED_CLASS);}};$(document.body).on('touchmove',this.__onTouchMove);});$(document).on('touchcancel','.button',function(){var elem=$(this);elem.removeClass(CONFIG.PRESSED_CLASS);$(document.body).off('touchmove',this.__onTouchMove);});$(document).on('touchend','.button',function(e){var elem=$(this);if(elem.hasClass(CONFIG.PRESSED_CLASS)){elem.removeClass(CONFIG.PRESSED_CLASS);var location=this.__eventLocation;if(location){var touch=e.originalEvent.changedTouches[0];if(!withinDistance(touch.clientX,touch.clientY,location[0],location[1],CONFIG.TOUCH_MOVE_THRESHHOLD)){return;}
setTimeout(function(){var evt=document.createEvent("MouseEvents");evt.initMouseEvent("click",true,true,window,0,0,0,0,0,false,false,false,false,0,null);elem.get(0).dispatchEvent(evt);},1);e.preventDefault();var clickLocation=[touch.clientX,touch.clientY];clicks.push(clickLocation);window.setTimeout(function(){var i=clicks.indexOf(clickLocation);if(i>=0){clicks.splice(i,1);}},CONFIG.GHOST_CLICK_TIMEOUT);}}
$(document.body).off('touchmove',this.__onTouchMove);});})(jQuery,window,document);