var timerImageChange;
var path = require('path');

window.compareTextIn = function(target){
	$('.text-preview__panel').hide();
	$('.text-preview__panel.text-preview__'+target).show();
	$('.text-preview__btns button').removeClass('active');
	$('.text-preview__btns button[data-text-in='+target+']').addClass('active');
}
window.compareImagesIn = function(target){
	clearTimeout(timerImageChange);
	$('.image-preview__panel').hide();
	$('.image-preview__panel.image-preview__'+target).show();
	$('.image-preview__btns button').removeClass('active');
	$('.image-preview__btns button[data-image-in='+target+']').addClass('active');
	if(target == 'piling-up'){
		compareImagesInPilingUp();
	}
}


window.compareImagesInPilingUp = function(){
	var $pile = $('.image-preview .image-preview__piling-up');
	var $before = $('.image-preview__piling-up .image-preview--before');
	var $after = $('.image-preview__piling-up .image-preview--after');
	if( $before.size() ){
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
				.append( $('<h2>before</h2>') )
				.append( $('<img>')
					.attr({
						"src": $('.image-preview__two-columns .image-preview--before img').attr('src')
					})
				)
			)
			.append( $('<div class="image-preview--after">')
				.append( $('<h2>after</h2>') )
				.append( $('<img>')
					.attr({
						"src": $('.image-preview__two-columns .image-preview--after img').attr('src')
					})
				)
			)
		;
		$('.image-preview__piling-up .image-preview--after').hide();
	}

	timerImageChange = setTimeout(compareImagesInPilingUp, 1000);
	return;
}
window.showAllList = function(){
	var $list = $('#difflist ul li');
	$list.show();
}
window.filterList = function(showSelector){
	var $list = $('#difflist ul').find(showSelector);
	$list.hide();
}


$(window).load(function(){
	compareTextIn('diff_1');
	compareImagesIn('two-columns');

	var hash = window.location.hash;
	if(hash){
		// console.log(hash);
		hash = hash.replace(/^\#+/, '');
		hash = path.resolve(hash);
		hash = hash.replace(/^\/+/, '');

		hash = './diff/'+hash+'.diff.html';
		// console.log(hash);
		window.diffpreview.location.href = hash;
	}

	$('.difflist__list').find('a').on('click', function(e){
		var $this = $(this);
		window.location.hash = $this.attr('data-path');
	})
});
