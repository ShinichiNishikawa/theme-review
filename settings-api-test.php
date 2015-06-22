<?php
function theme_review_theme_options_init() {
    register_setting(
        'theme_review_options', // Options group, see settings_fields() call in theme_review_render_api_page
        'theme_review_theme_options', // Database option, see theme_review_get_theme_options()
        'theme_review_validate' // The sanitization callback
    );

    // Register our settings field group
    add_settings_section(
        'general', // Unique identifier for the settings section
        'TEST SECTION', // Section title (we don't want one)
        '__return_false', // Section callback (we don't want anything)
        'theme_review_settings_api' // Menu slug, used to uniquely identify the page; see theme_review_add_api_page()
    );
    // Register our individual settings fields
    //add_settings_field( $id, $title, $callback, $page, $section, $args );
    add_settings_field( 'radio_buttons', __( 'Mark as completed:', 'theme_review' ), 'theme_review_settings_field_radio_buttons', 'theme_review_settings_api', 'general' );
    
    //lets create our list of requirements.
    //add_settings_section( $id, $title, $callback, $page );
        add_settings_section('accessibility', __( 'Accessibility', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('code', __( 'Code', 'theme-review' ) ,  '__return_false', 'theme_review_settings_api' );
        add_settings_section('core', __( 'Core Functionality and Features', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('presentation', __( 'Presentation vs Functionality', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('documentation', __( 'Documentation', 'theme-review' ), '__return_false', 'theme_review_settings_api' );
        add_settings_section('favicons', __( 'Favicons', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('language', __( 'Language', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('licensing', __( 'Licensing', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('naming', __( 'Naming', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('options', __( 'Options and Settings', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('plugins', __( 'Plugins', 'theme-review' ),   '__return_false', 'theme_review_settings_api' );
        add_settings_section('screenshot', __( 'Screenshot', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('security', __( 'Security and Privacy', 'theme-review' ), '__return_false', 'theme_review_settings_api' );
        add_settings_section('selling', __( 'Selling, credits and links', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('stylesheets', __( 'Stylesheets and Scripts', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );
        add_settings_section('templates', __( 'Templates', 'theme-review' ),  '__return_false', 'theme_review_settings_api' );

        //each settings needs the same option with radio button: Pass or Fail.
        //add_settings_field( $id, $title, $callback, $page, $section, $args );
        add_settings_field( 'accessibility_radio_buttons', __( 'Mark as completed:', 'theme_review' ), 'theme_review_settings_field_radio_buttons', 'theme_review_settings_api', 'accessibility' );
}
add_action( 'admin_init', 'theme_review_theme_options_init' );

/**
 * Change the capability required to save the 'theme_review_options' options group.
 *
 * @see theme_review_theme_options_init() First parameter to register_setting() is the name of the options group.
 * @see theme_review_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function theme_review_option_page_capability( $capability ) {
    return 'edit_theme_options';
}
add_filter( 'option_page_capability_theme_review_options', 'theme_review_option_page_capability' );

/**
 * Add our theme options page to the admin menu.
 *
 * This function is attached to the admin_menu action hook.
 */
function theme_review_add_api_page() {
    $theme_page = add_submenu_page( 
        'tools.php',
        __( 'Theme Review Settings API Test', 'theme_review' ),   // Name of page
        __( 'Theme Review Settings API Test', 'theme_review' ),   // Label in menu
        'edit_theme_options',          // Capability required
        'theme_review_settings_api',               // Menu slug, used to uniquely identify the page
        'theme_review_render_api_page' // Function that renders the options page
    );
}
add_action( 'admin_menu', 'theme_review_add_api_page' );

/**
 * Returns an array of radio options registered for theme_review.
 *
 */
function theme_review_radio_buttons() {
    $radio_buttons = array(
        'yes' => array(
            'value' => 'yes',
            'label' => __( 'Pass', 'theme_review' )
        ),
        'no' => array(
            'value' => 'no',
            'label' => __( 'Fail', 'theme_review' )
        )
    );

    return apply_filters( 'theme_review_radio_buttons', $radio_buttons );
}

/**
 * Returns the options array for theme_review.
 *
 */
function theme_review_get_theme_options() {
    $saved = (array) get_option( 'theme_review_theme_options' );
    $defaults = array(
        'radio_buttons'  => '',
    );

    $defaults = apply_filters( 'theme_review_default_theme_options', $defaults );

    $options = wp_parse_args( $saved, $defaults );
    $options = array_intersect_key( $options, $defaults );

    return $options;
}

/**
 * Renders the radio options setting field.
 */
function theme_review_settings_field_radio_buttons() {
    $options = theme_review_get_theme_options();

    foreach ( theme_review_radio_buttons() as $button ) {
    ?>
    <div class="layout">
        <label>
            <input type="radio" name="theme_review_theme_options[radio_buttons]" value="<?php echo esc_attr( $button['value'] ); ?>" <?php checked( $options['radio_buttons'], $button['value'] ); ?> />
            <?php echo $button['label']; ?>
        </label>
    </div>
    <?php
    }
}

/**
 * Renders the administration screen.
 */
function theme_review_render_api_page() {
    ?>
    <div class="wrap">
       <?php settings_errors(); ?>
 
        <form method="post" action="options.php">
            <?php
                settings_fields( 'theme_review_options' );
                do_settings_sections( 'theme_review_settings_api' );
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 */
function theme_review_validate( $input ) {
    $output = array();
    // The radio button value must be in our array of radio button values
    if ( isset( $input['radio_buttons'] ) && array_key_exists( $input['radio_buttons'], theme_review_radio_buttons() ) )
        $output['radio_buttons'] = $input['radio_buttons'];

    return apply_filters( 'theme_review_validate', $output, $input );
}
?>