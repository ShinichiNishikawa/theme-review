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
		'title' => __('Theme Review','theme-review'),
		'href'  => false,
	);


  $trt_theme = wp_get_theme();
  $trt_author_uri =$trt_theme->get( 'AuthorURI' );
  $trt_theme_uri =$trt_theme->get( 'ThemeURI' );

  $trt_author_uri ='<a href="' . esc_url($trt_author_uri) . '">' . __('Check Author URI','theme-review') . '</a>';
  $trt_theme_uri ='<a href="' . esc_url($trt_theme_uri) . '">' . __('Check Theme URI','theme-review') . '</a>';

  $trt_textdomain =$trt_theme->get( 'TextDomain' );
  if( !$trt_textdomain){
      $trt_textdomain=__('<b style="color:red">REQUIRED</b>: Text domain is missing or blank in style.css','theme-review');
  }


    /* __( 'Accessibility', 'theme-review' ), __('This theme has an accessibility-ready tag and it needs an<br> 
      additional review by an accessibility expert.<br>
      It is important that you do not close the ticket<br> 
    when you are finished with the basic review.','theme-review') ),
    */


// id, name, description
$requirements = array(
    array( "theme_review_code", __( 'Code', 'theme-review' ), __('Check for any php or javascript errors, warnings or notices on different<br> pages of the theme.','theme-review')
                    . '<br>' . __('Make sure that you also test any custom functionality and custom widgets.','theme-review')
                    . '<br>' . __('Check for broken images and test any header, logo, and background settings.','theme-review')
                    . '<br><br>' . __('Themes are required to have a valid DOCTYPE declaration and include<br> language_attributes.','theme-review') 
                    . '<br> <code> &lt;!DOCTYPE html&gt;&lt;html &lt;?php language_attributes(); ?&gt;></code>'
                    . '<br>' . __('<i>Other HTML and css errors are not reasons for not approving a theme,<br> but the theme should not be broken.</i>','theme-review')
                  ),

    array( "theme_review_prefix", __( 'Prefix', 'theme-review' ), __('The theme should use a unique prefix for everything the Theme defines<br> in the public namespace.','theme-review')
                    . '<br>' . __('It is not uncommon for authors to forget to prefix their functions or Classes.<br> The recommended prefix is the theme-slug.','theme-review')      
                    . '<br>'. __('Tip: use a text editor that can search entire directories to create<br> a list of all functions.','theme-review')
                    . '<br><br><span class="dashicons dashicons-visibility"></span> <a href="http://themereview.co/prefix-all-the-things/">Prefix all the things</a>'
                    ),
    array( "theme_review_sanitize", __( 'Sanitize', 'theme-review' ), __('Sanitize <b>everything</b>.','theme-review')
                    . __('If the theme uses the customizer, then each setting should<br> have a <code>sanitize_callback</code>.
                     Make sure that the callback function is appropriate<br> and working.','theme-review')
                    . '<br>' . __('If the theme uses meta boxes, the options needs to be sanitized before<br> they are added or updated.','theme-review')
                    ),
    array( "theme_review_hooks", __( 'Hooks', 'theme-review' ), __('Removing or modifying non-presentational hooks is not allowed,<br> this includes disabling the admin tool bar.','theme-review')    
                        . '<br>' . __('Examples:','theme-review')
                        . '<br><code>remove_action( "wp_head", "wp_generator" )</code> '
                        . "<br><code>add_filter( show_admin_bar', '__return_false' );</code> "      
                        . "<br><code>remove_filter( 'the_content','wpautop' );</code> "                       
                        . '<br>'. __('Themes should use the right hooks for their functions.','theme-review')
                        . '<br>' . __('load_theme_textdomain, add_theme_support, and register_nav_menu(s)<br> should be in a function, added with <br>
                          <code>add_action( "after_setup_theme", "function_name" );</code>','theme-review')

                     ),
  	
    array( "theme_review_core", __( 'Core Functionality and Features', 'theme-review' ), __('The theme should use WordPress functionality and features first, if available.<br>
                    This means that custom functions should not mimic or attempt to<br> replace core functions.','theme-review')
                   . '<br>' . __('(Function parameters and filters should be used instead.)','theme-review')
                   . '<br>' . __('Functionality that authors often missuse includes custom pagination,<br> hard coded search forms, options to disable comments, and <br>custom image resizing.','theme-review')
                   . '<br><br>' .  __('The theme must include comments_template().','theme-review')
                   . '<br>' .  __('Avoid hard coding to modify conten.','theme-review')

                    ),

    array( "theme_review_tags", __( 'Theme tags', 'theme-review' ), __('The theme tags and description must match what the theme actually does in respect to functionality and design.','theme-review')
      ),

  	array( "theme_review_presentation", __( 'Presentation vs Functionality & Plugins', 'theme-review' ),  __('Themes must not generate non-trivial user content or configure non-theme<br> options or functionality.','theme-review')
        . '<br>' .  __('Examples include: custom post types, shortcodes, analytics, ads, SEO. <br>It also includes making changes to login forms, admin interface, editors etc.')
        .'<br>' . __('A theme can recommend plugins, but not include them in the theme.','theme-review')
     ),
    array( "theme_review_documentation", __( 'Documentation', 'theme-review' ),  __('Is there enough documentation for you to set-up the theme?','theme-review') ),
    array( "theme_review_favicons", __( 'Favicons', 'theme-review' ),  __('If implemented, favicons must be disabled by default, and there must be<br> an option where the user can change the icon.','theme-review') ),
    array( "theme_review_language", __( 'Language', 'theme-review' ),  __('All theme text strings are to be translatable.','theme-review') . '<br>' .  __('Text domain: ','theme-review') . '<br>' . $trt_textdomain
                   . '<br>' . __( 'WordPress must be spelled with a capital W and P. <br>A theme can be in any language, but only one.','theme-review') . ''
                   . '<br><b style="color:blue">' . __( 'RECOMMENDED:','theme-review') . '</b>'
                   . '<br><span class="ab-icon dashicons dashicons-media-code"></span>' . __('Make sure that there is a language file in the theme folder and that it<br> refers to the correct theme.','theme-review')
                   . '<br><span class="ab-icon dashicons dashicons-visibility"></span> <a href="https://developer.wordpress.org/themes/functionality/internationalization/">' 
                   . __('Theme Handbook: Internationalization.','theme-review') . '</a>'
                    ),

    array( "theme_review_licensing", __( 'Licensing and Naming', 'theme-review' ) , __('The theme needs to be 100% GPL compatible.','theme-review') . ' ' . __('The theme should: <br> 
                     -Use the license and license uri in the header of style.css','theme-review') 
                   . '<br>' .__('-Declare licenses of any resources included such as<br> fonts or images, including images used in the screenshot.','theme-review') 
                   . '<br>' . __('-Have a copyright notice: (Copyright Year Name).','theme-review')
                   . '<br>' . __('The theme name must not contain trademarks, "WordPress" or "Theme".','theme-review')
                   . '<br><br><span class="ab-icon dashicons dashicons-media-code"></span>' . __('Open the readme file and make sure that <br>
                    license and copyright has been declared for all included<br>third party resources.','theme-review')
                   . '<br><br><span class="dashicons dashicons-visibility"></span> <a href="http://www.gnu.org/licenses/license-list.html#GPLCompatibleLicenses">' . 
                   __('List of GPL compatible licenses','theme-review') . '</a>') ,

  //  array( "naming", __( 'Naming', 'theme-review' ),  __('The theme name must not contain trademarks, "WordPress" or "Theme".','theme-review') ),
    array( "theme_review_options", __( 'Options and Settings', 'theme-review' ),  __('Are all options in the customizer? New themes are no longer allowed to<br> create a separate options page.','theme-review') ),
  //  array( "plugins", __( 'Plugins', 'theme-review' ),  __('description.','theme-review') ),
    array( "theme_review_screenshot", __( 'Screenshot', 'theme-review' ),  __('The Screenshot should be of the actual theme as it appears with <br>default options, not a logo or mockup. It should be no bigger than 1200 x 900px.','theme-review'), ),
    array( "theme_review_security", __( 'Security and Privacy', 'theme-review' ),  __('Themes must not collect user data without asking for permission.','theme-review') ),
    array( "theme_review_links", __( 'Selling, credits and links', 'theme-review' ),  __('Theme shops should be selling under GPL to be in the WordPress.org repo.','theme-review') 
                  . '<br>' . __('Links should be related to the theme and helpful to the user.','theme-review') 
                  . '<br>' .  __('WordPress.org links are reserved for official themes.','theme-review') . '<br>' . $trt_author_uri . ' ' . $trt_theme_uri
                  . '<br>' . __('Check if there is a credit link in the customizer or the footer and if <br> the link is appropriate.','theme-review') 
                  . '<br>' . __('There should only be one footer credit link and it needs to be the same as<br> Author URI or Theme URI.','theme-review')
                  . '<br>' . __('There should be no links to the authors social network pages.','theme-review'),

      ),
    array( "theme_review_stylesheets", __( 'Stylesheets and Scripts', 'theme-review' ), __('<b>Enqueuing scripts correctly is vital, and this section has additional information<br> on the plugin page.</b>','theme-review')
                    . '<br>' . __('Script and stylesheets must be enqueued, with the exception of<br> browser workaround scripts.','theme-review')
                    . '<br>' . __('The theme must use core-bundled scripts if available, and include all scripts<br> rather than hot-linking. ','theme-review') 
                    . '<br>' . __('Themes must not deregister core scripts.','theme-review')
                    . '<br>' . __('If minfied scripts or styles are used, the original file must also be included.','theme-review'),  ),
    array( "theme_review_templates", __( 'Templates', 'theme-review' ),  __('description.','theme-review') ),

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

    $content= $content . '<li class="theme-review-header">' . $req[1] . '</li><li class="description">' . $req[2] 
     . '<br><span class="mark-completed">' . __('Mark as completed:' ,'theme-review') . '</span><br>
        <label><input type="radio" name="' . esc_attr( $req[0] ) . '" value="pass" ' . checked( $value, 'pass', false ) . '> ' . __('Pass','theme-review') .'</label><br>
        <label><input type="radio" name="' . esc_attr( $req[0] ). '" value="fail" ' . checked( $value, 'fail', false ) . '> ' . __('Fail','theme-review'). '</label><br>'
    . '</li>';
} //end for each
    
    $dropdown_section = '<div id="theme-review-dropdown">
    	<form method="POST">
      	<ul>'
        	. $content .
      	'</ul>
        <br>
      	<input type="submit" value="' . __('Save Progress','theme-review') . '" class="button button-primary button-large">
    	</form>
    </div>';

	$wp_admin_bar->add_node( $args );
	$wp_admin_bar->add_node( array( 'id' => 'theme_review_bar_slide', 'parent' => 'theme_review_bar', 'title' => $dropdown_section, 'href' => FALSE ) );
}