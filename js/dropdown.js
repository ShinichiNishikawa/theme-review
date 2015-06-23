jQuery(document).ready(function($) {

	$('.description').hide(); //Hide children by default

	$('.theme-review-header').click(function(){
	  //Expand or collapse this panel
	  $(this).next().slideToggle('fast');
	  $('.theme-review-header').slideToggle('fast');
	  $(this).slideToggle('fast');

	  $(this).toggleClass('switch');
    
	});
} );