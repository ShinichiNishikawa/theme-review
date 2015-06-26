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

// id, name, description
$requirements = array(
    array( "theme_review_code", __( 'Code', 'theme-review' ), __('Check for any php or javascript errors, warnings or notices on different pages of the theme.','theme-review')
                    . '<br>' . __('Perform a search, check the 404 page, and make sure that you also test any custom functionality and custom widgets.','theme-review')
                    . '<br>' . __('Check for broken images and test any header, logo, and background settings.','theme-review')
                    . '<br><br>' . 
                    sprintf( __('Themes are required to have a valid DOCTYPE declaration and include <a href="%s">language_attributes</a>.','theme-review'), 
                      esc_url( 'https://developer.wordpress.org/reference/hooks/language_attributes/' ) )
                    . '<br> <code> &lt;!DOCTYPE html&gt;<br>&lt;html &lt;?php language_attributes(); ?&gt;></code>'
                    . '<br><br>' . __('<i>Other HTML and css errors are not reasons for not approving a theme, but the theme should not be broken.</i>','theme-review')
                    . '<br>'
                  ),

    array( "theme_review_prefix", '<span class="theme-review-indent"></span>' . __( 'Prefix', 'theme-review' ), __('The theme should use a unique prefix for everything the Theme defines in the public namespace.','theme-review')
                    . '<br>' . __('It is not uncommon for authors to forget to prefix their functions or Classes. The recommended prefix is the theme-slug.','theme-review')      
                    . '<br><br>'. __('<i>Tip: use a text editor that can search entire directories to create a list of all functions.</i>','theme-review')
                    . '<br><br><span class="ab-icon dashicons dashicons-visibility"></span> <a href="http://themereview.co/prefix-all-the-things/">Prefix all the things</a>'
                     . '<br>'
                    ),

    array( "theme_review_sanitize", '<span class="theme-review-indent"></span>' . __( 'Sanitize', 'theme-review' ), __('Sanitize everything.','theme-review') . ' '
                    . __('If the theme uses the customizer, then each setting should have a <code>sanitize_callback</code>. Make sure that the callback function is appropriate and working.','theme-review')
                    . '<br>' . __('If the theme uses meta boxes, the options needs to be sanitized before they are added or updated.','theme-review')
                    . '<br>'
                    ),

    array( "theme_review_hooks", '<span class="theme-review-indent"></span>' .__( 'Hooks', 'theme-review' ), __('Removing or modifying non-presentational hooks is not allowed. 
                           This includes disabling the admin tool bar, removing versions, or removing rss feeds.','theme-review')    
                        . '<br>' . __('Examples:','theme-review')
                        . '<br><code>remove_action( "wp_head", "wp_generator" )</code> '
                        . "<br><code>add_filter( show_admin_bar', '__return_false' );</code> "      
                        . "<br><code>remove_filter( 'the_content','wpautop' );</code> "                       
                        . '<br><br>'. __('Themes should use the correct hooks for their functions.','theme-review') . ' ' 
                        . __('The most common issue in this section is that load_theme_textdomain, add_theme_support, and register_nav_menu(s) should be inside a function, added with <br><code>add_action( "after_setup_theme", "function_name" );</code>','theme-review')
                        . '<br>'
                     ),
  	
    array( "theme_review_core", __( 'Core Functionality and Features', 'theme-review' ), __('The theme should use WordPress functionality and features first, if available. This means that custom functions should not mimic or attempt to replace core functions.','theme-review')
                   . '<br>' . __('(Function parameters and filters should be used instead.)','theme-review')
                   . '<br>' . __('Functionality that authors often missuse includes custom pagination, hard coded search forms, options to disable comments, and custom image resizing.','theme-review')
                   . '<br><br>' .  __('The theme must include comments_template(). (Also see the Template section.)','theme-review')
                  . '<br><br>' .  __('Use get_template_directory() rather than TEMPLATEPATH to return the template path. <br> Use get_stylesheet_directory() rather than STYLESHEETPATH to return the stylesheet path.','theme-review')
                   . '<br>' . __('There should be no pay wall restricting any WordPress feature.','theme-review')
                   . '<br>'
                    ),

    array( "theme_review_tags", '<span class="theme-review-indent"></span>' . __( 'Theme tags', 'theme-review' ),  __('The theme tags and description must match what the theme actually does in respect to functionality and design.','theme-review')
                       .  '<br><span class="ab-icon dashicons dashicons-visibility"></span>' . 
                        sprintf( __('You can view a list of all the tags <a href="%s">here</a>.','theme-review'), esc_url( 'https://make.wordpress.org/themes/handbook/review/required/theme-tags/' ) )
                       . '<br>'
         ),

  	array( "theme_review_presentation", __( 'Presentation vs Functionality, Plugins & favicons', 'theme-review' ),  __('Themes must not generate non-trivial user content or configure non-theme options or functionality.','theme-review')
                     . '<br>' . __('Examples of functionality that should not be in the theme includes: social sharing, flickr widgets, custom post types, shortcodes, dashboard widgets, admin pointers, analytics, ads, SEO. This also includes making changes to login forms, admin interface, user profiles, editors etc.')
                     .'<br><br>' . __('A theme can recommend plugins, but not include them in the theme. The theme must work without plugins, and <i>reviewers are not required to test the theme with the plugins installed.</i>','theme-review')
                     .'<br><br>' . __('If implemented, favicons must be disabled by default, and there must be an option where the user can change the icon.','theme-review')
                     . '<br>'
         ),
    array( "theme_review_documentation", __( 'Documentation', 'theme-review' ),  __('Is there enough documentation for you to set up the theme?','theme-review')  
                    . '<br>'
        ),

    array( "theme_review_language", __( 'Language', 'theme-review' ),  __('All theme text strings are to be translatable.','theme-review') . '<br>' .  __('Text domain: ','theme-review') . ' ' . $trt_textdomain
                   . '<br>' . __( 'WordPress must be spelled with a capital W and P. <br>A theme can be in any language, but only one.','theme-review') . ''
                   . '<br><b style="color:blue">' . __( 'RECOMMENDED:','theme-review') . '</b>'
                   . '<br><span class="ab-icon dashicons dashicons-media-code"></span>' . __('Make sure that there is a language file in the theme folder and that it refers to the correct theme.','theme-review')
                   . '<br><span class="ab-icon dashicons dashicons-visibility"></span> <a href="https://developer.wordpress.org/themes/functionality/internationalization/">' 
                   . __('Theme Handbook: Internationalization.','theme-review') . '</a>'
                   . "<br>" . __('A basic translation string can look like this:','theme-review') . '<br>'
                   . "<code> __( 'text to be internationalized', 'text-domain' );</code><br>"
                   .  __('If a translation is echoed, it can also look like this:','theme-review') . '<br>'
                   . "<code>_e( 'WordPress is the best!', 'text-domain' );</code><br>"
                   .  __('A text that looks like this would fail, since it is not translatable:','theme-review') . '<br>'
                   . "<code>&lt;h2&gt;Hello World&lt;/h2&gt;</code> "
                   . __('should be:','theme-review') . '<br>'
                   . "<code>&lt;h2&gt;&lt;?php _e('Hello World', 'text-domain'); ?&gt; &lt;/h2&gt;</code><br>"
                   . __('Translated attributes should be escaped.','theme-review') . ' '
                   . __('Example:','theme-review') . '<br>'
                   . '<code>value="&lt;?php esc_attr_e("Search","text-domain" ); ?&gt;"</code>'
                    . '<br>'
                    ),

    array( "theme_review_licensing", __( 'Naming and Licensing', 'theme-review' ) , __('The theme needs to be 100% GPL compatible.','theme-review') . ' ' 
                   . __('The theme should:','theme-review') 
                   . '<br>' . __('-Use the license and license uri in the header of style.css.','theme-review') 
                   . '<br>' . __('Declare licenses of any resources included such as fonts or images, including images used in the screenshot.','theme-review') 
                   . '<br>' . __('Have a copyright notice: (Copyright Year Name).','theme-review')
                   . '<br>' . __('The theme name must not contain trademarks, "WordPress" or "Theme".','theme-review')
                   . '<br><br><span class="ab-icon dashicons dashicons-media-code"></span>' 
                   . __('Open the readme file and make sure that license and copyright has been declared for all included third party resources.','theme-review')
                   . '<br><span class="ab-icon dashicons dashicons-visibility"></span> ' .
                    sprintf( __('<a href="%s">List of GPL compatible licenses</a>.','theme-review'), esc_url( 'http://www.gnu.org/licenses/license-list.html#GPLCompatibleLicenses/' ) ) 
                    . '<br>' .
                    sprintf( __('<a href="%s">Proper Copyright/License Attribution for Themes</a>.','theme-review'), esc_url( 'https://make.wordpress.org/themes/2014/07/08/proper-copyrightlicense-attribution-for-themes/' ) )
                   . '<br>'
                    ),

    array( "theme_review_optset", __( 'Options and Settings', 'theme-review' ),  __('Are all options in the customizer? New themes are no longer allowed to create a separate options page.','theme-review')
                                     . '<br>' . __('Themes must respect the user settings, such as the General, Writing, Reading and Discussion settings.','theme-review')
                                     . '<br><br>' .  __('Common problems in this section includes <i>Display Header Text</i> under Site Title and Tagline in the customizer, background colors, and how the front page is displayed. ','theme-review')
                                     . '<br>' . __('If the option <i>Front page displays your latest posts</i> is chosen, then the theme must show the latest posts on the front page.','theme-review')
                                     . '<br>'
      ),

    array( "theme_review_screenshot", __( 'Screenshot', 'theme-review' ),  __('The Screenshot should be of the actual theme as it appears with default options, not a logo or mockup. If the theme is for example a niche or e-commerce theme,
                               then shop plugins may be installed on the screenshot. The Screenshot should be in the 4:3 ratio, no bigger than 1200 x 900px.','theme-review')
                             . '<br>'
      ),
    array( "theme_review_security", __( 'Security and Privacy', 'theme-review' ),  __('Themes must not collect user data without asking for permission. This includes google analytics campaign tracking.','theme-review') 
                     . '<br>' . __(' No URL shorteners should be used in the theme.','theme-review')
      ),

    array( "theme_review_escaping", '<span class="theme-review-indent"></span>' . __( 'Escaping', 'theme-review' ), __('All untrusted data should be escaped before output. This is a very common problem.','theme-review')
                    . '<br>' . __('esc_url() should be used on all URLs, including those in the "src" and "href" attributes of an HTML element.','theme-review')
                    . '<br>' . '<code>&lt;img src="&lt;?php echo esc_url( $great_user_picture_url ); ?&gt;" /&gt;</code>'
                    . '<br>' . __('esc_attr() can be used in other attributes. esc_attr_e() can be used when echoing a translation inside an attribute.','theme-review')
                    . '<br><code>class="&lt;?php echo esc_attr( $stored_class ); ?&gt;"</code>'
                    . '<br>' . __('Recommended:','theme-review')
                    . '<br>' . __('esc_html() is used anytime our HTML element encloses a section of data we are outputting. esc_html_e() can be used when echoing a translation.','theme-review')
                    . '<br><code>&lt;h4&gt;&lt;?php echo esc_html( $title ); ?&gt;&lt;/h4&gt;</code>'
                    . '<br><span class="ab-icon dashicons dashicons-visibility"></span> <a href="https://codex.wordpress.org/Data_Validation">Data Validation</a>
                     <a href="https://codex.wordpress.org/Validating_Sanitizing_and_Escaping_User_Data#Escaping:_Securing_Output">Escaping: Securing Output</a>'
                   . '<br>'
          ),

    array( "theme_review_links", __( 'Selling, credits and links', 'theme-review' ),  $trt_author_uri . '<br>' . $trt_theme_uri
                  . '<br>' . __('Theme URI is optional. If used, it is required to link to a page with information about the Theme. If a demonstration site is being used, the content must be related to the theme itself and not test data.','theme-review') 
                  . '<br>' . __(' Author URI is optional. If used it is required to link to an author’s personal website or project/development website.','theme-review') 
                  . '<br><br>' . __('Check if there are any credit- or upsell links and if they are appropriate.','theme-review') 
                  .  ' ' . __('There should only be one footer credit link.','theme-review')
                  . '<br>' . __('WordPress.org links are reserved for official themes.','theme-review')
                  . '<br><br>' . __('Theme shops should be selling under GPL to be in the WordPress.org repo.','theme-review') 
                  . '<br>' . __('There should be no links to the authors social network pages.','theme-review')
                  . '<br>'
        ),
    array( "theme_review_scripts", __( 'Stylesheets and Scripts', 'theme-review' ), __('The theme must use core-bundled scripts if available, and include all scripts rather than hot-linking.','theme-review') 
                    . '<br>' . __('Themes must not deregister core scripts.','theme-review')
                    . '<br>' . __('If minfied scripts or styles are used, the original file must also be included.','theme-review')
                    . '<br>' . __('The most common errors for this section are hardcoded scripts or styles in header.php and footer.php, and theme authors including their own version of jQuery or jQuery UI instead of using the core-bundled scripts.','theme-review')
                    . '<br>' . __('Check all folders for minified and duplicate files. It is not uncommon for authors to forget to include the original versions of Font Awesome and Bootstrap.','theme-review')
                    . '<br>'
          ),

    array( "theme_review_enqueue_styles", '<span class="theme-review-indent"></span>' . __( 'Enqueueing Stylesheets', 'theme-review' ), __('Script and stylesheets must be enqueued, with the exception of browser workaround scripts.','theme-review')
                   . '<br>' . __('This is the wrong way of adding the stylesheet:','theme-review')
                   . '<br>' .'<code> &lt;link type="text/css" rel="stylesheet" href="&lt;?php echo get_stylesheet_uri(); ?> /></code><br>'
                   . __('The correct way of adding a stylesheet to the front end:','theme-review') 
                   . "<br>
                    <code>
                        add_action( 'wp_enqueue_scripts', 'theme_slug_css' );<br>
                        function theme_slug_css() {<br>
                            wp_enqueue_style( 'theme-slug-style', get_stylesheet_uri() );<br>
                        }
                     </code>"
                    . '<br>'
                 ),

    array( "theme_review_enqueue_scripts", '<span class="theme-review-indent"></span>' . __( 'Enqueueing Scripts', 'theme-review' ),  __('jQuery can be added as a dependancy of a custom script like this:','theme-review') 
                    . "<br><code>
                        add_action( 'wp_enqueue_scripts', 'theme_slug_scripts' );<br>
                        function theme_slug_scripts() {<br>
                        wp_enqueue_script('theme-slug-script', get_stylesheet_directory_uri() . '/js/script.js', array( 'jquery' ) );<br>
                        }
                      </code><br>"
                    . __('Or by itself like this:','theme-review')
                    . "<br> 
                         <code>
                          add_action( 'wp_enqueue_scripts', 'theme_slug_scripts' );<br>
                          function theme_slug_scripts() {<br>
                            wp_enqueue_script('jquery');<br>
                          }
                       </code>"
                   . '<br>'
     ),

    array( "theme_review_templates", __( 'Templates', 'theme-review' ),  __('If used in the theme, standard template files are required to be called using the correct template tag:','theme-review')
                   . '<br>' . 'header.php ( get_header() )'
                   . '<br>' . 'footer.php ( get_footer() )'
                   . '<br>' . 'sidebar.php ( get_sidebar() )'
                   . '<br>' . 'comments.php ( comments_template() )'
                   . '<br>' . 'searchform.php ( get_search_form() )'
                   . '<br><br>' . __( 'If custom template files are used, the author must include documentation as to what it refers to.','theme-review') 
                  . '<br>'
                  ),
);

foreach ($requirements as $req) {  
         
    if ( isset( $_POST[ $req[0] ] ) ) {
        //validate before update
        if  ( $_POST[ $req[0] ] =='fail' || $_POST[ $req[0] ] =='pass') { 
            update_option( $req[0] , $_POST[ $req[0] ]);
        }
    }

    $value = esc_attr( get_option($req[0], '0') );

    $content= $content . '<li class="theme-review-header">' . $req[1] . '</li><li class="theme-review-description">' . $req[2] 
     . '<br><span class="theme-review-mark-completed">' . __('Mark as completed:' ,'theme-review') . '</span><br>
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