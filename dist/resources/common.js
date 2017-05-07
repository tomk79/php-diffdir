(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
var timerImageChange;
function compareImagesInTwoColumns(){
	clearTimeout(timerImageChange);
	$('.image-preview .image-preview--pilingup').hide();
	$('.image-preview .image-preview--twocolumns').show();
	return;
}
function compareImagesInPilingUp(){
	clearTimeout(timerImageChange);
	var $pile = $('.image-preview .image-preview--pilingup');
	if( $pile.is(':visible') ){
		var $before = $('.image-preview--pilingup .image-preview--before');
		var $after = $('.image-preview--pilingup .image-preview--after');
		if($before.is(':visible')){
			$before.hide();
			$after.show();
		}else{
			$before.show();
			$after.hide();
		}
	}else{
		$pile
			.html('')//一旦クリア
			.append( $('<div class="image-preview--before">')
				.append( $('<p>変更前</p>') )
				.append( $('<img>')
					.attr({
						"src": $('.image-preview--twocolumns .image-preview--before img').attr('src')
					})
				)
			)
			.append( $('<div class="image-preview--after">')
				.append( $('<p>変更後</p>') )
				.append( $('<img>')
					.attr({
						"src": $('.image-preview--twocolumns .image-preview--after img').attr('src')
					})
				)
			)
		;
		$('.image-preview--pilingup .image-preview--after').hide();

		$pile.show();
		$('.image-preview .image-preview--twocolumns').hide();
	}

	timerImageChange = setTimeout(compareImagesInPilingUp, 1000);
	return;
}
function showAllList(){
	var $list = $('#difflist ul li');
	$list.show();
}
function filterList(showSelector){
	var $list = $('#difflist ul').find(showSelector);
	$list.hide();
}

},{}]},{},[1])