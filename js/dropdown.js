jQuery(document).ready(function($) {

	$('.description').hide(); //Hide children by default

	$('.parent').click(function(){
	  //Expand or collapse this panel
      $(this).next().slideToggle('fast');

	});
} );