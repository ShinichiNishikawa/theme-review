jQuery(document).ready(function($) {

	$('.theme-review-description').hide(); //Hide children by default

	$('.theme-review-header').click(function(){
	  //Expand or collapse this panel

  $(this).next().slideToggle('fast');
	  $('.theme-review-header').slideToggle('fast');
	  $(this).slideToggle('fast');

	  $(this).toggleClass('theme-review-switch');
    
	});

	$( "input#theme-review-fix" ).change( function() {
		if ( $(this).is( ":checked" ) ) {
			$( "#wp-admin-bar-theme_review_bar .ab-sub-wrapper" ).css( "display", "block" );
			$( "html" ).css( "padding-right", "530px" );
		} else {
			$( "#wp-admin-bar-theme_review_bar .ab-sub-wrapper, html" ).removeAttr( "style" );
		}
	});

} );