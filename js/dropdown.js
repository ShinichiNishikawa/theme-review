jQuery(document).ready(function($) {

	$('.theme-review-description').hide(); //Hide children by default

	$('.theme-review-header').click(function(){
	  //Expand or collapse this panel

  $(this).next().slideToggle('fast');
	  $('.theme-review-header').slideToggle('fast');
	  $(this).slideToggle('fast');

	  $(this).toggleClass('theme-review-switch');
    
	});
} );