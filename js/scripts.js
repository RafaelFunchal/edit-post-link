$(document).ready(function(){
	$('.edit-post-link').mouseover(function() {
		$(this).animate({
			width: '50px',
			height: '20px',
			textIndent: '0'
		},500);
	});
	$('.edit-post-link').mouseout(function() {
		$(this).animate({
			width: '15px',
			height: '15px',
			textIndent: '-999px'
		},500);
	});
	$('.edit-post-link').attr('target', '_blank');
});