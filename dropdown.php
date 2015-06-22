<?php
add_action( 'admin_bar_menu', 'theme_review_dropdown', 999 );
function theme_review_dropdown() {
  global $wp_admin_bar, $wpdb;
  $content="";

	if ( !is_super_admin() || !is_admin_bar_showing() )
        return;

	$args = array(
		'id'    => 'theme_review_bar',
		'parent' => 'top-secondary',
		'title' => __('Theme Review Dropdown test','theme-review'),
		'href'  => false,
	);

// id, name, description
$requirements = array(
  	array( "accessibility", __( 'Accessibility', 'theme-review' ), __('This theme has an accessibility-ready tag and it needs an<br> 
  		additional review by an accessibility expert.<br>
  		It is important that you do not close the ticket<br> 
		when you are finished with the basic review.','theme-review') ),
  	array( "code", __( 'Code', 'theme-review' ), __('code-description.','theme-review') ),
  	array( "core", __( 'Core Functionality and Features', 'theme-review' ), __('core-description.','theme-review') ),
  	array( "presentation", __( 'Presentation vs Functionality', 'theme-review' ),  __('presentation-description.','theme-review') ),
);

foreach ($requirements as $req) {
         
    if ( isset( $_POST[ $req[0] ] ) ) {
        //add sanity check...
        //validate before update
        if  ( $_POST[ $req[0] ] =='fail' || $_POST[ $req[0] ] =='pass') { 
            update_option( $req[0] , $_POST[ $req[0] ]);
        }
    }

    $value = esc_attr( get_option($req[0], '0') );

    $content= $content . '<li class="parent">' . $req[1] . '</li><li class="description">' . $req[2] 
     . '<br><br><span class="mark-completed">' . __('Mark as completed:' ,'theme-review') . '</span><br>
        <label><input type="radio" name="' . esc_attr( $req[0] ) . '" value="pass" ' . checked( $value, 'pass', false ) . '> ' . __('Pass','theme-review') .'</label><br>
        <label><input type="radio" name="' . esc_attr( $req[0] ). '" value="fail" ' . checked( $value, 'fail', false ) . '> ' . __('Fail','theme-review'). '</label><br>'
    . '</li>';
} //end for each
    
    $dropdown_section = '<div style="width:400px; min-height:500px; background:#32373c; padding:12px; margin:-6px -20px; ">
    	<form method="POST">
      	<ul>'
        	. $content .
      	'</ul>
        <br>
        <input type="hidden" name="saved" value="sent">
      	<input type="submit" value="Save Progress" class="button button-primary button-large">
    	</form>
    </div>';

	$wp_admin_bar->add_node( $args );
	$wp_admin_bar->add_node( array( 'id' => 'theme_review_bar_slide', 'parent' => 'theme_review_bar', 'title' => $dropdown_section, 'href' => FALSE ) );
}