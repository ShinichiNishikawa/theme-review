<?php
/*
Plugin Name: Theme Review
Plugin URI:  
Description: This plugin is designed as a guide to help you with your first theme review. It can also be helpful as a check list for theme authors.
Version: 1.0.0
Author:      TRT
Author URI:  https://make.wordpress.org/themes/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: theme-review
*/

/**
 * Load up the menu page
 */
add_action( 'admin_menu', 'theme_review_add_page' );
function theme_review_add_page() {
	  $page_hook_suffix = add_submenu_page( 'tools.php', __( 'Theme Review', 'theme-review' ), __( 'Theme Review', 'theme-review' ), 'manage_options', 'theme_review', 'theme_review_do_page' );
	  add_action('admin_print_scripts-' . $page_hook_suffix, 'theme_review_admin_scripts');
}

function theme_review_admin_scripts() {
    wp_enqueue_style('theme-review-style', plugins_url( '/style.css', __FILE__ ) );
}

add_action( 'plugins_loaded', 'theme_review_load_textdomain' );
function theme_review_load_textdomain() {
	load_plugin_textdomain( 'theme-review', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

/* Script to expand the requriement information boxes 
* We need this wherever the admin bar is loaded.
*/
function theme_review_bar_scripts() {
		if ( is_admin_bar_showing() ){
			wp_enqueue_style('theme-review-dropdown-style', plugins_url( '/dropdownstyle.css', __FILE__ ) );
			wp_enqueue_script( 'theme-review-dropdown', plugins_url( '/js/dropdown.js', __FILE__ ), array( 'jquery' ) );
		}
}
add_action( 'wp_enqueue_scripts', 'theme_review_bar_scripts' );
/* Admin side*/
add_action( 'admin_enqueue_scripts', 'theme_review_bar_scripts' );

include( plugin_dir_path( __FILE__ ) . 'dropdown.php');

/**
 * Create the page
 */
function theme_review_do_page() {
?>
<script>
 jQuery(document).ready(function($) {
    $('.welcome-panel').find('.handlediv').click(function(){
      //Expand or collapse this panel
      $(this).next().slideToggle('fast');
    });
  });
</script>

<div class="wrap">
<div class="welcome-panel"><h1><?php _e( 'Theme Review', 'theme-review' );?></h1>	
<div title="<?php _e('Click to toggle','theme-review');?>" class="handlediv"><br></div>
	<div class="welcome-panel-content">
	<p class="about-description"><?php _e('This plugin is designed to be a compliment to the Theme Review guidelines and a guide to help you with your first theme review.','theme-review');?></p>
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column" style="width:65%;">
			<h3><b><?php _e('Preparing for your review','theme-review');?></b></h3>
			<?php 
			_e( 'Awesome! You have already completed the first steps in setting up your testing environment:','theme-review');

			if ( defined( 'WP_DEBUG' ) ){
				 echo ' <code>'. __('WP_DEBUG is on.','theme-review') . '</code> ';
			} else {
				echo  '<p>';
				printf( __('Now, we recommend you to set the following constants to <code>true</code> in your <code>wp-config.php</code> file. <a href="%s" target="_blank">Need help?</a>', 'theme-review' ), esc_url("http://codex.wordpress.org/Editing_wp-config.php") );
				echo '</p> ';
			}

			//Check if Theme check is active
			if (is_plugin_active('theme-check/theme-check.php')) {
				echo __('Theme Check is active.','theme-review');
			}else{
				if ( ! current_user_can( 'install_plugins' ) ) {
					return;
				}
				if (file_exists( WP_PLUGIN_DIR . '/' . 'theme-check/theme-check.php' ) && is_plugin_inactive('theme-check/theme-check.php')){
					echo '<p>' . __('Theme Check is installed but not active.','theme-review') . '</p> ';
					//todo: Use activation link with wp_nonce_url
					echo '<a href="' . admin_url('plugins.php') . '" class="button button-primary button-hero">' . esc_html__( 'Activate Theme Check', 'theme-review' ) . '</a>';
				}else{
					echo '<p>' . __( 'We recommend that you also install the Theme Check plugin:', 'theme-review' ) . '</p> ';
					echo '<a href="' . admin_url('plugins.php') . '" class="button button-primary button-hero">' . esc_html__(' Install Theme Check', 'theme-review' ) . '</a>';
				}
			}
			?>

			<div style="margin-top:20px; padding-bottom:40px;">
				<h3><b><?php _e( 'Now, is your ticket ready?', 'theme-review' );?></b></h3>
				<h5><?php _e('You are reviewing:','theme-review');?>
				<?php
				//fetch and print the theme name
				$trt_theme = wp_get_theme();
				echo $trt_name =$trt_theme->get( 'Name' );
				?>.  
				<?php printf( __('-If this is not your ticket, <a href="%s">install and activate the correct theme.</a>', 'theme-review'), esc_url( admin_url('themes.php') ) );?><br></h5>
				<h4><?php _e( 'Ticket? -What ticket?','theme-review');?></h4>
				<?php _e( 'A theme trac ticket is a ticket with information about a theme that has been submitted to WordPress.org.','theme-review');?><br>
				<?php printf( __( 'If this is your first review, you might be more comfortable reviewing our test theme that you can download <a href="%s">here</a>.', 'theme-review'), esc_url('https://github.com/WPTRT/doingitwrong') );?><br><br>
				<?php printf( __('If you are ready to take on a real ticket, please <a href="%s"><button class="make-request">Request a theme to review.</button></a>', 'theme-review'), esc_url( 'https://make.wordpress.org/themes/' ) );?>
				<?php printf( __('You will then find your tickets here: <a href="%s">https://themes.trac.wordpress.org</a>.', 'theme-review'), esc_url( 'https://themes.trac.wordpress.org/query?status=!closed&owner=$USER' ) );?>
				<br><br>
				<?php _e( 'In your ticket you will find the theme name, the authors name, and a link to a zip file containing the theme that you should review.<br>','theme-review');?>
				<?php _e( 'Below the theme screenshot is a form, so sa "Hi" and <b>introduce yourself to your author.</b>','theme-review');?>
				<br>
			</div>

		</div>
	<div class="welcome-panel-column welcome-panel-last">
		<h4><?php _e( 'Stuck?', 'theme-review' );?></h4>
		<p><b>
		<?php printf( __('If you have questions about reviewing, come talk to other reviewers on <a href="%s">wordpress.slack.com #themereview</a>', 'theme-review'), esc_url( 'https://make.wordpress.org/chat/') );?>
		</b></p>
		<?php printf( __('We also have weekly meetings on Slack:<br>Tuesdays @ 18:00 UTC<br><i>For agenda, visit our Make blog:</i> <a href="%s">make.wordpress.org/themes/</a>', 'theme-review'), esc_url( 'https://make.wordpress.org/themes/') );?>
		<br><br>
	</div>
</div>
</div>
</div>

<div class="welcome-panel">			
	<h2><b><?php _e('Performing your review','theme-review');?></b></h2>
	<div title="<?php _e('Click to toggle','theme-review');?>" class="handlediv"><br></div>
		<div class="welcome-panel-content">
			<h5><?php printf( __('The full guidelines that you will review the theme against can be found <a href="%s">here.</a>', 'theme-review'), esc_url( 'https://make.wordpress.org/themes/handbook/review/required/' ));
				echo ' ' . __('The plugin should only be seen as a complement to the guidelines.','theme-review');?>
			</h5><br>
			<?php _e('You should now have your theme folder and your code editor ready so that you can easilly check the files.','theme-review');?>
			<br>
			<?php 
			echo __('<b>What you will be checking:</b>','theme-review') . '<br>' .
			__('There is more to a review than checking if the content is displayed nicely. You will be checking if the theme is <b>secure</b>, 
			if there are any <b>errors</b>, if it is <b>translatable</b> and if all theme <b>options</b> are working correctly.<br>','theme-review');
			_e('You will be reading a lot of code, and one theme will have a different setup than the other, but don\'t worry, this becomes easier as you gain more experience.<br>','theme-review');
			echo __('<b>Reviewing a theme with the help of the plugin:</b>','theme-review') . '<br>';
			echo __('You will find a new menu in the admin bar to your top right. Each section in the menu represents a requirement, accompanied by a short statement or a question.','theme-review') . '<br>'
			 . __('Once a check has been completed, save your progress and it will be added to the summary below.','theme-review');

			echo '<br><i>' . __('Example:','theme-review') . '</i><br>';
			echo '<img src="' . esc_url( plugins_url( 'lang.png', __FILE__ ) ) . '" alt="" > ';
			echo '<br><i>' . __('Anything in red is a required item that should be noted in your review.','theme-review') . '</i><br>';
			echo __('You should now be able to start your review, so open the theme and start checking those buttons. Some of the sections has additional information, examples and links that can help you on your way.','theme-review')  . '<br><br>';

			echo '<h3><b>'. __('Writing your review','theme-review') . '</b></h3>';
			echo __('Take notes as you review each part of the theme, and write down any questions that you have for the author.','theme-review') . '<br>' . 
			__('The suggested format for your review is as follows:','theme-review') . '<br>';
			echo '<ul><li><b>' . __('Welcome wrapper.','theme-review') . '</b> ' . __('Say Hi to the author, let them know what you are going to do. This may be their first review.','theme-review') . '</li>' .
				'<li><b>' . __('Say the outcome.','theme-review') . '</b> ' . __('Let the author know from the start what the outcome is.','theme-review') . '</li>' .
				'<li><b>' . __('Required.','theme-review') . '</b> ' . __('List all the required items, a theme can\'t be approved until all of these are met.','theme-review') . '</li>' .
				'<li><b>' . __('Recommended.','theme-review') . '</b> ' . __('You can then list all the recommended items. These won\'t be a grounds to not approve, but they are good theme practice.','theme-review') . '</li>' .
				'<li><b>' . __('Notes.','theme-review') . '</b> ' .  __('This could be a section where you add design notes, maybe additional information. Again, this can\'t be something you don\'t approve because, but it can be a way to educate.','theme-review') . '</li>' .
				'<li><b>' . __('Say what is going to happen next.','theme-review') . '</b> '.  __('Keeping the author informed is great. Let them know you will let them upload a new version or what the approval process is.','theme-review') . '</li>' .
				'</ul>';

			_e('<h5>Using the headings "Required, Recommended and Notes" is really helpful for people when viewing the review.</h5>','theme-review') ;
			echo '<br><h3><b>' . __('Finishing your review','theme-review') . '</b></h3>' . 
			__('It is important that you do not close the ticket when you submit your notes.','theme-review') . '<br>' . 
			__('If there are required items that needs to be fixed, the author has seven days to reply. They can then submit a new version, or ask for more time.','theme-review') . '<br>' .
			__('If a new version is submitted, you need to check if all the required items has been fixed.','theme-review') . '<br>' .
			__('When you feel that a theme is ready to be approved, you should ask for feedback from your mentor or an experienced theme reviewer before you close the ticket.','theme-review') . '<br>' .
			__('If seven days has passed without any word from the theme author, you can close the ticket as not approved.','theme-review') . '<br><br>';
			?>
	</div>
</div>

<br><h2><?php _e('Summary:','theme-review');?></h2>
<hr>
<?php
$code = esc_attr( get_option('theme_review_code', __('Not completed','theme-review') ) );
	$prefix = esc_attr( get_option('theme_review_prefix', __('Not completed','theme-review') ) );
	$sanitize = esc_attr( get_option('theme_review_sanitize', __('Not completed','theme-review') ) );
	$hooks = esc_attr( get_option('theme_review_hooks', __('Not completed','theme-review') ) );

$core = esc_attr( get_option('theme_review_core', __('Not completed','theme-review') ) );
	$tags = esc_attr( get_option('theme_review_tags', __('Not completed','theme-review') ) );

$presentation = esc_attr( get_option('theme_review_presentation', __('Not completed','theme-review') ) );

$documentation = esc_attr( get_option('theme_review_documentation', __('Not completed','theme-review') ) );
$favicons = esc_attr( get_option('theme_review_favicons', __('Not completed','theme-review') ) );
$lang = esc_attr( get_option('theme_review_lang', __('Not completed','theme-review') ) );
$license = esc_attr( get_option('theme_review_licensing', __('Not completed','theme-review') ) );

$options = esc_attr( get_option('theme_review_options', __('Not completed','theme-review') ) );
$screenshot = esc_attr( get_option('theme_review_screenshot', __('Not completed','theme-review') ) );
$security = esc_attr( get_option('theme_review_security', __('Not completed','theme-review') ) );
$links = esc_attr( get_option('theme_review_links', __('Not completed','theme-review') ) );

$scripts =  esc_attr( get_option('theme_review_scripts', __('Not completed','theme-review') ) );

$escaping = esc_attr( get_option('theme_review_escaping', __('Not completed','theme-review') ) );
$misc = esc_attr( get_option('theme_review_misc', __('Not completed','theme-review') ) );

 

 echo '<br><br>The summary of the following section still needs to be edited. /Carolina<br><hr><br>';



 echo '<h2>' . __( 'Code', 'theme-review' ) . ': <i class="' . $code . '">' . $code . '</i></h2>';
 	echo '<h3 style="margin-left:22px;">' . __( 'Prefix', 'theme-review' ) . ': <i class="' . $prefix . '">' .  $prefix . '</i></h3>';
 	echo '<h3 style="margin-left:22px;">' . __( 'Sanitize', 'theme-review' ) . ': <i class="' . $prefix . '">' .  $prefix . '</i></h3>';
	echo '<h3 style="margin-left:22px;">' . __( 'Hooks', 'theme-review' ) . ': <i class="' . $hooks . '">' . $hooks . '</i></h3>';

 echo '<h2>' . __( 'Core functionality', 'theme-review' ) . ': <i class="' . $core . '">' . $core  . '</i></h2>';
 			echo '<h3 style="margin-left:22px;">' . __( 'Tags', 'theme-review' ) . ': <i class="' . $tags . '">' . $tags . '</i></h3>';

 echo '<h2>' . __( 'Presentation vs Functionality & Plugins', 'theme-review' ) . ': <i class="' . $presentation . '">' . $presentation  . '</i></h2>';

 echo '<h3>' . __( 'Documentation', 'theme-review' ) . ': <i class="' . $documentation . '">' . $documentation  . '</i></h3>';
 echo '<h3>' . __( 'Favicons', 'theme-review' ) . ': <i class="' . $favicons . '">' . $favicons  . '</i></h3>';
 echo '<h3>' . __( 'Language', 'theme-review' ) . ': <i class="' . $lang . '">' . $lang . '</i></h3>';
 echo '<h3>' . __( 'Naming and Licensing', 'theme-review' ) . ': <i class="' . $license . '">' . $license . '</i></h3>';

 echo '<h2>' . __( 'Options and Settings', 'theme-review' ) . ': <i class="' . $options . '">' . $options  . '</i></h2>';
 echo '<h3>' . __( 'Screenshot', 'theme-review' ) . ': <i class="' . $screenshot . '">' . $screenshot . '</i></h3>';
 echo '<h3>' . __( 'Security and Privacy', 'theme-review' ) . ': <i class="' . $security . '">' . $security . '</i></h3>';
 echo '<h3>' . __( 'Selling, credits and links', 'theme-review' ) . ': <i class="' . $links . '">' . $links . '</i></h3>';

 echo '<h3>' . __( 'Stylesheets and Scripts', 'theme-review' ) . ': <i class="' . $scripts . '">' . $scripts . '</i></h3>';

 echo '<h3>' . __( 'Escaping', 'theme-review' ) . ': <i class="' . $escaping . '">' . $escaping . '</i></h3>';
 echo '<h3>' . __( 'Miscellaneous', 'theme-review' ) . ': <i class="' . $misc . '">' . $misc . '</i></h3>';


   $trt_theme = wp_get_theme();
    $trt_tags =$trt_theme->get( 'Tags' );
    if (in_array ('accessibility-ready' , $trt_tags) ){
    	 echo '<br><h3>' . __( 'Note:','theme-review') . '</h3> ' . __('<b>This theme has an accessibility-ready tag and it needs an additional review by an accessibility expert. It is important that you do not close the ticket when you are finished with the basic review.</b>','theme-review');
    }
?>

<br><br>

<div class="welcome-panel">			
<h2><b><?php _e('Additional help','theme-review');?></b></h2>
<div title="<?php _e('Click to toggle','theme-review');?>" class="handlediv"><br></div>
	<div class="welcome-panel-content"><h3><?php _e('Stylesheets and Scripts','theme-review');?></h3>
		<?php _e('The most common errors for this section are hardcoded scripts or styles in header.php and footer.php, and themes including their own version of jQuery or jQuery UI instead of using the core-bundled scripts.<br>','theme-review');?>
		<?php _e('Please check all folders for minified and duplicate files. It is not uncommon for authors to forget to include the original versions of Font Awesome and Bootstrap.<br>','theme-review');?>

		<b><?php _e('Examples:','theme-review');?></b><br>
		<?php _e('This is the wrong way of adding the stylesheet:','theme-review');?><br>
		<code> &lt;link type="text/css" rel="stylesheet" href="&lt;?php echo get_stylesheet_uri(); ?> /></code><br>
		<?php _e('The correct way of adding a stylesheet to the front end:','theme-review');?><br>
			<code>
					add_action( 'wp_enqueue_scripts', 'theme_slug_css' );<br>
					function theme_slug_css() {<br>
					    wp_enqueue_style( 'theme-slug-style', get_stylesheet_uri() );<br>
					}
			</code><br>
			<?php 
			echo __('The correct way of adding jQuery:','theme-review') . '<br>' . 
					__('jQuery can be added as a dependancy of a custom script like this:','theme-review') . '<br>';
				?>
			<code>
					add_action( 'wp_enqueue_scripts', 'theme_slug_scripts' );<br>
					function theme_slug_scripts() {<br>
						wp_enqueue_script('theme-slug-custom-script', get_stylesheet_directory_uri() . '/js/custom_script.js',	array( 'jquery' ) );<br>
					}
			</code><br>
			<?php _e('Or by itself like this:','theme-review');?><br>	
			<code>
					add_action( 'wp_enqueue_scripts', 'theme_slug_scripts' );<br>
					function theme_slug_scripts() {<br>
						wp_enqueue_script('jquery');<br>
					}
			</code><br>
			<?php    
			echo '<br><h3>' . __('Requesting help','theme-review') . '</b></h3>'
			 . __('You can always search in the Slack archive to see if your question has been answered before.', 'theme-review') . '<br>'
			 . __('In your ticket, below the text area, is a text-field labeled "Cc:". To request help from an admin, add their username as one of the recipients.', 'theme-review') . '<br>';
			printf( __('<a href="%s">List of active admins.</a>', 'theme-review'), esc_url( 'https://make.wordpress.org/themes/handbook/the-team/members/') );
		
			echo '<br><br><h3>' . __('More explanations and examples','theme-review') . '</b></h3><a href="https://make.wordpress.org/themes/handbook/review/required/explanations-and-examples/">Explanations and examples</a><br><br>';
     ?>

	</div>
</div>
</div><!--end wrap -->
	<?php
}