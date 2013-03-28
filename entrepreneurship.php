<?php
/*
Plugin Name: Entrepreneurship +1
Plugin URI: http://disruptive.nu/wordpress-plugins/entrepreneurship/
Description: Entrepreneurship +1
Author: Christian Rudolf
Author URI: http://disruptive.nu/
Version: 1.1
License: GPLv2
*/

/*  Copyright 2011 Christian Rudolf (email : rudolf@disruptive.nu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**********************************************************************
 * Constants
 **********************************************************************/
define( 'EPO_VERSION', '1.1' );
define( 'EPO_TEXT_DOMAIN', 'entrepreneurship' );

/**********************************************************************
 * Translation
 **********************************************************************/
load_plugin_textdomain( EPO_TEXT_DOMAIN, false, 'entrepreneurship/languages'); // TODO: make folder name to an constant OR use a WP function for this?

/**********************************************************************
 * Plugin Activation and DeActivation
 **********************************************************************/
register_activation_hook( __FILE__, 'epo_activation' );
function epo_activation() {
	
	if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
		die(__('<strong>Entrepreneurship +1:</strong> requires WordPress 3.0 or newer', EPO_TEXT_DOMAIN));
    }
	$epo_options = get_option( 'epo_options' );
	if( $epo_options['plugin_version'] == '' ){
		/** Set default options **/
		$epo_options = array(
			'plugin_version' => EPO_VERSION,
			'general_show_on_post' => 1,
			'general_show_on_page' => 1,
			'general_default_share_message' => 'Share entrepreneurship',
			'general_share_box_background' => '#F9F9F9',
			'general_share_box_border' => '#CCCCCC',
			'facebook_activated' => 1,
			'facebook_send_button' => 1,
			'facebook_layout_style' => 'standard',
			'facebook_show_faces' => 1,
			'facebook_verb' => 'like',
			'facebook_color_scheme' => 'light',
			'facebook_font' => '',
			'twitter_activated' => 1,
			'twitter_default_hash' => '',
			'twitter_data_count' => 'vertical',
			'twitter_data_via' => '',
			'twitter_data_related' => '',
			'twitter_data_related_description' => '',
			'linkedin_activated' => 1,
			'linkedin_data_counter' => 'top',
			'google_activated' => 1,
			'google_size' => 'tall',
			'google_count' => 1,
			'google_parse' => 'default',
			'google_js_callback' => '',
			'pusha_activated' => 1,
			'pusha_bakgrund' => '#FFFFFF',
			'pusha_nyttfonster' => 1
		);
		update_option( 'epo_options', $epo_options);
	}

}

register_deactivation_hook( __FILE__, 'epo_deactivation' );
function epo_deactivation() {
	// Nothing to do yet!
}

/**********************************************************************
 * Register Javascript and Stylesheets for admin pages
 **********************************************************************/
add_action("admin_init", "epo_admin_init");
function epo_admin_init(){
	wp_register_script('epo_admin_js', plugins_url( 'js/admin.js' , __FILE__ ) );
	wp_register_script('epo_colorpicker_js', plugins_url( '/js/colorpicker.js', __FILE__ ));
}

/**********************************************************************
 * Register and Add Stylesheets to front-end
 **********************************************************************/
add_action('wp_head', 'epo_wp_head_css');
function epo_wp_head_css() {

	$epo_options = get_option( 'epo_options' );

?>
	<!-- Entrepreneurship +1 CSS -->
	<style type='text/css'>
	
		.epo-share-box-container {
			background: none repeat scroll 0 0 <?php echo $epo_options['general_share_box_background']; ?>;
			border: 1px solid <?php echo $epo_options['general_share_box_border']; ?>;
			box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1) inset;
			float: left;
			margin-bottom: 10px;
			text-align: center;
			width: 99%;			
		}

		.epo-share-message{
			font-size: 18px;
			font-weight: bold;
		}
		
		.epo-share-buttons {
			display: inline-block;		
		}
		
		.epo-share-button {
			float: left;
		}

		#epo-share-button-twitter, #epo-share-button-linkedin, #epo-share-button-google {
			margin-right: 10px;
		}

		#epo-share-button-facebook-standard {
			margin-left: 45px;
		}
		
		#epo-share-button-facebook-button_count {
			margin-left: 20px;
		}
				
		.epo-created-by {
			clear: both;
			font-size: 10px;
		}		
	
	</style>
<?php
}

/**********************************************************************
 * Plugin menu and pages
 **********************************************************************/
add_action( 'admin_menu', 'epo_admin_menu' );
function epo_admin_menu() {

	global $epo_page;
	
	/** General Settings Page **/
	$epo_page['general'] = add_menu_page( __('Entrepreneurship +1', EPO_TEXT_DOMAIN), __('Entrepreneurship +1', EPO_TEXT_DOMAIN),
		'manage_options', 'epo_general' , 'epo_draw_general',
		plugins_url( '/images/heart_16x16.gif', __FILE__ ) );

	$epo_page['general'] = add_submenu_page( 'epo_general', __('General', EPO_TEXT_DOMAIN), __('General', EPO_TEXT_DOMAIN),
		'manage_options', 'epo_general', 'epo_draw_general' );		

	add_action( "admin_print_scripts-".$epo_page['general'], 'epo_farbtastic_script');
	add_action( "admin_print_styles-".$epo_page['general'], "epo_farbtastic_style" );
	add_action( "admin_print_scripts-".$epo_page['general'], 'epo_colorpicker_script');
		
	/** Social Network Page **/
	$epo_page['networks'] = add_submenu_page( 'epo_general', __('Social Networks', EPO_TEXT_DOMAIN), __('Social Networks', EPO_TEXT_DOMAIN),
		'manage_options', 'epo_networks', 'epo_draw_networks' );

	add_action( "admin_print_scripts-".$epo_page['networks'], 'epo_admin_script');

	add_action( "admin_print_scripts-".$epo_page['networks'], 'epo_farbtastic_script');
	add_action( "admin_print_styles-".$epo_page['networks'], "epo_farbtastic_style" );
	add_action( "admin_print_scripts-".$epo_page['networks'], 'epo_colorpicker_script');
	
}
 
/**********************************************************************
 * Add Stylesheet to Admin
 **********************************************************************/

// Load Farbtastic CSS stylesheet (color picker)
function epo_farbtastic_style() {
	wp_enqueue_style('farbtastic');
}

/**********************************************************************
 * Add JavaScript to Admin
 **********************************************************************/
function epo_admin_script() {
	wp_enqueue_script( 'epo_admin_js' );
}

// Load Farbtastic javascript (color picker)
function epo_farbtastic_script() {
	wp_enqueue_script( 'farbtastic' );
}	

// Load custom color picker javascript
function epo_colorpicker_script() {
	wp_enqueue_script( 'epo_colorpicker_js' );
}

/**********************************************************************
 * Register and define the settings
 **********************************************************************/
add_action('admin_init', 'epo_register_and_define_settings');
function epo_register_and_define_settings(){

	register_setting(
		'epo_options',
		'epo_options',
		'epo_validate_options'
	);

	/** General section **/
	add_settings_section(
		'epo_general',
		__('General Settings for Entrepreneurship +1', EPO_TEXT_DOMAIN),
		'epo_general_section_text',
		'epo_general_general'
	);

	add_settings_field(
		'general_default_share_message',
		__('Default share message:', EPO_TEXT_DOMAIN),
		'epo_general_default_share_message_callback',
		'epo_general_general',
		'epo_general'
	);

	add_settings_field(
		'general_show_on_post',
		__('Show share box on post:', EPO_TEXT_DOMAIN),
		'epo_general_show_on_post_callback',
		'epo_general_general',
		'epo_general'
	);
	
	add_settings_field(
		'general_show_on_page',
		__('Show share box on page:', EPO_TEXT_DOMAIN),
		'epo_general_show_on_page_callback',
		'epo_general_general',
		'epo_general'
	);	
	
	add_settings_field(
		'general_share_box_background',
		__('Background color:', EPO_TEXT_DOMAIN),
		'epo_general_share_box_background_callback',
		'epo_general_general',
		'epo_general'
	);
	
	add_settings_field(
		'general_share_box_border',
		__('Border color:', EPO_TEXT_DOMAIN),
		'epo_general_share_box_border_callback',
		'epo_general_general',
		'epo_general'
	);
		
	/** Facebook section **/
	add_settings_section(
		'epo_facebook',
		__('Settings for Facebook', EPO_TEXT_DOMAIN),
		'epo_facebook_section_text',
		'epo_networks_facebook'
	);
	
	add_settings_field(
		'facebook_activated',
		__('Add Facebook Like:', EPO_TEXT_DOMAIN),
		'epo_facebook_activated_callback',
		'epo_networks_facebook',
		'epo_facebook'
	);

	/** Facebook section advanced **/
	add_settings_section(
		'epo_facebook',
		__('Advanced settings for Facebook', EPO_TEXT_DOMAIN),
		'epo_facebook_advanced_section_text',
		'epo_networks_facebook_advanced'
	);
	
	add_settings_field(
		'facebook_send',
		__('Include Facebook Send button:', EPO_TEXT_DOMAIN),
		'epo_facebook_send_button_callback',
		'epo_networks_facebook_advanced',
		'epo_facebook'
	);

	add_settings_field(
		'facebook_layout_style',
		__('Layout style:', EPO_TEXT_DOMAIN),
		'epo_facebook_layout_style_callback',
		'epo_networks_facebook_advanced',
		'epo_facebook'
	);
	
	add_settings_field(
		'facebook_show_faces',
		__('Show Faces:', EPO_TEXT_DOMAIN),
		'epo_facebook_show_faces_callback',
		'epo_networks_facebook_advanced',
		'epo_facebook'
	);
	
	add_settings_field(
		'facebook_verb',
		__('Verb to display:', EPO_TEXT_DOMAIN),
		'epo_facebook_verb_callback',
		'epo_networks_facebook_advanced',
		'epo_facebook'
	);

	add_settings_field(
		'facebook_color_scheme',
		__('Color Scheme:', EPO_TEXT_DOMAIN),
		'epo_facebook_color_scheme_callback',
		'epo_networks_facebook_advanced',
		'epo_facebook'
	);
	
	add_settings_field(
		'facebook_font',
		__('Font:', EPO_TEXT_DOMAIN),
		'epo_facebook_font_callback',
		'epo_networks_facebook_advanced',
		'epo_facebook'
	);

	/** Twitter section **/
	add_settings_section(
		'epo_twitter',
		__('Settings for Twitter', EPO_TEXT_DOMAIN),
		'epo_twitter_section_text',
		'epo_networks_twitter'
	);
	
	add_settings_field(
		'twitter_activated',
		__('Add Twitter button:', EPO_TEXT_DOMAIN),
		'epo_twitter_activated_callback',
		'epo_networks_twitter',
		'epo_twitter'
	);
	
	/** Twitter section advanced **/
	add_settings_section(
		'epo_twitter',
		__('Advanced settings for Twitter', EPO_TEXT_DOMAIN),
		'epo_twitter_advanced_section_text',
		'epo_networks_twitter_advanced'
	);	
	
	add_settings_field(
		'twitter_default_hash',
		__('Optional default hash:', EPO_TEXT_DOMAIN),
		'epo_twitter_default_hash_callback',
		'epo_networks_twitter_advanced',
		'epo_twitter'
	);
	
	add_settings_field(
		'twitter_data_count',
		__('Button style:', EPO_TEXT_DOMAIN),
		'epo_twitter_data_count_callback',
		'epo_networks_twitter_advanced',
		'epo_twitter'
	);		

	add_settings_field(
		'twitter_data_via',
		__('Via account:', EPO_TEXT_DOMAIN),
		'epo_twitter_data_via_callback',
		'epo_networks_twitter_advanced',
		'epo_twitter'
	);
	
	add_settings_field(
		'twitter_data_related',
		__('Releated account:', EPO_TEXT_DOMAIN),
		'epo_twitter_data_related_callback',
		'epo_networks_twitter_advanced',
		'epo_twitter'
	);
	
	add_settings_field(
		'twitter_data_related_description',
		__('Releated account description:', EPO_TEXT_DOMAIN),
		'epo_twitter_data_related_description_callback',
		'epo_networks_twitter_advanced',
		'epo_twitter'
	);	

	/** LinkedIn section **/
	add_settings_section(
		'epo_linkedin',
		__('Settings for LinkedIn', EPO_TEXT_DOMAIN),
		'epo_linkedin_section_text',
		'epo_networks_linkedin'
	);
	
	add_settings_field(
		'linkedin_activated',
		__('Add LinkedIn button:', EPO_TEXT_DOMAIN),
		'epo_linkedin_activated_callback',
		'epo_networks_linkedin',
		'epo_linkedin'
	);
	
	/** LinkedIn section advanced **/
	add_settings_section(
		'epo_linkedin',
		__('Advanced settings for LinkedIn', EPO_TEXT_DOMAIN),
		'epo_linkedin_advanced_section_text',
		'epo_networks_linkedin_advanced'
	);	
	
	add_settings_field(
		'linkedin_data_counter',
		__('Button style:', EPO_TEXT_DOMAIN),
		'epo_linkedin_data_counter_callback',
		'epo_networks_linkedin_advanced',
		'epo_linkedin'
	);

	/** Google +1 section **/
	add_settings_section(
		'epo_google',
		__('Settings for Google +1', EPO_TEXT_DOMAIN),
		'epo_google_section_text',
		'epo_networks_google'
	);
	
	add_settings_field(
		'google_activated',
		__('Add Google +1 button:', EPO_TEXT_DOMAIN),
		'epo_google_activated_callback',
		'epo_networks_google',
		'epo_google'
	);
	
	/** Google +1 section advanced **/
	add_settings_section(
		'epo_google',
		__('Advanced settings for Google +1', EPO_TEXT_DOMAIN),
		'epo_google_advanced_section_text',
		'epo_networks_google_advanced'
	);	
	
	add_settings_field(
		'google_size',
		__('Button size:', EPO_TEXT_DOMAIN),
		'epo_google_size_callback',
		'epo_networks_google_advanced',
		'epo_google'
	);
	
	add_settings_field(
		'google_count',
		__('Include count:', EPO_TEXT_DOMAIN),
		'epo_google_count_callback',
		'epo_networks_google_advanced',
		'epo_google'
	);
	
	add_settings_field(
		'google_parse',
		__('Parse:', EPO_TEXT_DOMAIN),
		'epo_google_parse_callback',
		'epo_networks_google_advanced',
		'epo_google'
	);
	
	add_settings_field(
		'google_js_callback',
		__('JS Callback function:', EPO_TEXT_DOMAIN),
		'epo_google_js_callback_callback',
		'epo_networks_google_advanced',
		'epo_google'
	);
	
	/** Pusha section **/
	add_settings_section(
		'epo_pusha',
		__('Settings for Pusha', EPO_TEXT_DOMAIN),
		'epo_pusha_section_text',
		'epo_networks_pusha'
	);	

	add_settings_field(
		'pusha_activated',
		__('Add Pusha button:', EPO_TEXT_DOMAIN),
		'epo_pusha_activated_callback',
		'epo_networks_pusha',
		'epo_pusha'
	);
	
	/** Pusha section advanced **/
	add_settings_section(
		'epo_pusha',
		__('Advanced settings for Pusha', EPO_TEXT_DOMAIN),
		'epo_pusha_advanced_section_text',
		'epo_networks_pusha_advanced'
	);	
	
	add_settings_field(
		'pusha_nyttfonster',
		__('Open in a new window:', EPO_TEXT_DOMAIN),
		'epo_pusha_nyttfonster_callback',
		'epo_networks_pusha_advanced',
		'epo_pusha'
	);	
	
	add_settings_field(
		'pusha_bakgrund',
		__('Background color:', EPO_TEXT_DOMAIN),
		'epo_pusha_bakgrund_callback',
		'epo_networks_pusha_advanced',
		'epo_pusha'
	);
		
	
}

/**********************************************************************
 * Validate Options
 **********************************************************************/
function epo_validate_options( $input ){

	// TODO: Is there a better way to validate checkboxes?

	$valid = get_option( 'epo_options' );
	if( isset($input['general_share_box_background']) ) { // Check so that we validate the right page... TODO: Is there a better way of doing this?
	
		/** General Page **/
	
		// general_show_on_post
		if( isset($input['general_show_on_post']) ){
			$valid['general_show_on_post'] = absint( $input['general_show_on_post'] );
		}else{
			$valid['general_show_on_post'] = 0;
		}	
		
		// general_show_on_page
		if( isset($input['general_show_on_page']) ){
			$valid['general_show_on_page'] = absint( $input['general_show_on_page'] );
		}else{
			$valid['general_show_on_page'] = 0;
		}	
		
		// general_default_share_message
		if(isset($input['general_default_share_message'])){
			$valid['general_default_share_message'] = wp_filter_nohtml_kses($input['general_default_share_message']);
		}	
		
		// general_share_box_background
		if(isset($input['general_share_box_background'])){
			$valid['general_share_box_background'] = wp_filter_nohtml_kses($input['general_share_box_background']);
		}
		
		// general_share_box_border
		if(isset($input['general_share_box_border'])){
			$valid['general_share_box_border'] = wp_filter_nohtml_kses($input['general_share_box_border']);
		}	

	}else{
		
		/** Networks Page **/
		
		// facebook_activated
		if( isset($input['facebook_activated']) ){
			$valid['facebook_activated'] = absint( $input['facebook_activated'] );
		}else{
			$valid['facebook_activated'] = 0;
		}
		
		// facebook_send_button
		if(isset($input['facebook_send_button'])){
			$valid['facebook_send_button'] = absint( $input['facebook_send_button'] );
		}else{
			$valid['facebook_send_button'] = 0;
		}

		// facebook_layout_style
		if(isset($input['facebook_layout_style'])){
			switch( $input['facebook_layout_style'] ){
				case 'standard':
				case 'button_count':
				case 'box_count':
					$valid['facebook_layout_style'] = $input['facebook_layout_style'];
					break;
				default:
					$valid['facebook_layout_style'] = 'standard';
					add_settings_error(
						'facebook_layout_style',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;		
			}
		}
		
		// facebook_show_faces
		if(isset($input['facebook_show_faces'])){
			$valid['facebook_show_faces'] = absint( $input['facebook_show_faces'] );
		}else{
			$valid['facebook_show_faces'] = 0;
		}	
		
		// facebook_verb
		if(isset($input['facebook_verb'])){
			switch( $input['facebook_verb'] ){
				case 'like':
				case 'recommend':
					$valid['facebook_verb'] = $input['facebook_verb'];
					break;
				default:
					$valid['facebook_verb'] = 'like';
					add_settings_error(
						'facebook_verb',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;		
			}
		}

		// facebook_color_scheme
		if(isset($input['facebook_color_scheme'])){
			switch( $input['facebook_color_scheme'] ){
				case 'light':
				case 'dark':
					$valid['facebook_color_scheme'] = $input['facebook_color_scheme'];
					break;
				default:
					$valid['facebook_color_scheme'] = 'light';
					add_settings_error(
						'facebook_color_scheme',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;		
			}
		}

		// facebook_font
		if(isset($input['facebook_font'])){
			switch( $input['facebook_font'] ){
				case '':
				case 'arial':
				case 'lucida grande':
				case 'segoe ui':
				case 'tahoma':
				case 'trebuchet ms':
				case 'verdana':
					$valid['facebook_font'] = $input['facebook_font'];
					break;
				default:
					$valid['facebook_font'] = '';
					add_settings_error(
						'facebook_font',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;		
			}
		}
		
		// twitter_activated
		if( isset($input['twitter_activated']) ){
			$valid['twitter_activated'] = absint( $input['twitter_activated'] );
		}else{
			$valid['twitter_activated'] = 0;
		}		

		// twitter_default_hash
		if(isset($input['twitter_default_hash'])){
			$valid['twitter_default_hash'] = wp_filter_nohtml_kses($input['twitter_default_hash']);

		}		

		// twitter_data_count
		if(isset($input['twitter_data_count'])){
			switch( $input['twitter_data_count'] ){
				case 'vertical':
				case 'horizontal':
				case 'none':
					$valid['twitter_data_count'] = $input['twitter_data_count'];
					break;
				default:
					$valid['twitter_data_count'] = 'vertical';
					add_settings_error(
						'twitter_data_count',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;
			}
		}

		// twitter_data_via
		if(isset($input['twitter_data_via'])){
			$valid['twitter_data_via'] = wp_filter_nohtml_kses($input['twitter_data_via']);
		}		

		// twitter_data_related
		if(isset($input['twitter_data_related'])){
			$valid['twitter_data_related'] = wp_filter_nohtml_kses($input['twitter_data_related']);
		}		

		// twitter_data_related_description
		if(isset($input['twitter_data_related_description'])){
			$valid['twitter_data_related_description'] = wp_filter_nohtml_kses($input['twitter_data_related_description']);
		}

		// linkedin_activated
		if( isset($input['linkedin_activated']) ){
			$valid['linkedin_activated'] = absint( $input['linkedin_activated'] );
		}else{
			$valid['linkedin_activated'] = 0;
		}

		// linkedin_data_counter
		if(isset($input['linkedin_data_counter'])){
			switch( $input['linkedin_data_counter'] ){
				case 'top':
				case 'right':
				case 'none':
					$valid['linkedin_data_counter'] = $input['linkedin_data_counter'];
					break;
				default:
					$valid['linkedin_data_counter'] = 'top';
					add_settings_error(
						'linkedin_data_counter',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;		
			}
		}		

		// google_activated
		if( isset($input['google_activated']) ){
			$valid['google_activated'] = absint( $input['google_activated'] );
		}else{
			$valid['google_activated'] = 0;
		}		

		// google_size
		if(isset($input['google_size'])){
			switch( $input['google_size'] ){
				case 'standard':
				case 'small':
				case 'medium':
				case 'tall':
					$valid['google_size'] = $input['google_size'];
					break;
				default:
					$valid['google_size'] = 'standard';
					add_settings_error(
						'google_size',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;		
			}
		}
		
		// google_count
		if( isset($input['google_count']) ){
			$valid['google_count'] = absint( $input['google_count'] );
		}else{
			$valid['google_count'] = 0;
		}

		// google_parse
		if(isset($input['google_parse'])){
			switch( $input['google_parse'] ){
				case 'default':
				case 'explicit':
					$valid['google_parse'] = $input['google_parse'];
					break;
				default:
					$valid['google_parse'] = 'default';
					add_settings_error(
						'google_parse',
						'settings_updated',
						__('You can only choose between the options in the dropdown.', EPO_TEXT_DOMAIN ),
						'error'
					);
					break;		
			}
		}

		// google_js_callback
		if(isset($input['google_js_callback'])){
			$valid['google_js_callback'] = wp_filter_nohtml_kses($input['google_js_callback']);
		}
		
		// pusha_activated
		if( isset($input['pusha_activated']) ){
			$valid['pusha_activated'] = absint( $input['pusha_activated'] );
		}else{
			$valid['pusha_activated'] = 0;
		}

		// pusha_bakgrund
		if(isset($input['pusha_bakgrund'])){
			$valid['pusha_bakgrund'] = wp_filter_nohtml_kses($input['pusha_bakgrund']);
		}

		// pusha_nyttfonster
		if( isset($input['pusha_nyttfonster']) ){
			$valid['pusha_nyttfonster'] = absint( $input['pusha_nyttfonster'] );
		}else{
			$valid['pusha_nyttfonster'] = 0;
		}
		
	} // If !isset($input['general_share_box_background'])
	return $valid;

}


/**********************************************************************
 * Draw pages
 **********************************************************************/
/** Draw Social Networks Page **/
function epo_draw_general() {
?>
	<?php settings_errors(); ?>
	<div class="wrap">
		<a href="http://disruptive.nu/"><div id="epo-icon" style="background: url(<?php echo plugins_url( '/images/heart_32x32.gif', __FILE__ ) ?>) no-repeat;" class="icon32"><br /></div></a>
		<h2><?php _e('General Settings', EPO_TEXT_DOMAIN); ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields('epo_options'); ?>
			<?php do_settings_sections('epo_general_general'); ?>
			<div id="picker" class="picker" style=""></div>
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes', EPO_TEXT_DOMAIN); ?>" /></p>
		</form>
	</div>
	
<?php
}
 
 
/** Draw Social Networks Page **/
function epo_draw_networks() {
?>
	<?php settings_errors(); ?>
	<div class="wrap">
		<a href="http://disruptive.nu/"><div id="epo-icon" style="background: url(<?php echo plugins_url( '/images/heart_32x32.gif', __FILE__ ) ?>) no-repeat;" class="icon32"><br /></div></a>
		<h2><?php _e('Social Networks', EPO_TEXT_DOMAIN); ?></h2>
		<p><?php _e('For each social network there are some settings that you can do. If you are new to this, just leave it as it is. I have made sure that the default settings is okay. You can return back to this page at any time to change these settings. You can always send me an email on rudolf@disruptive.nu if you need support.', EPO_TEXT_DOMAIN); ?></p>		
		<form action="options.php" method="post">
			<?php settings_fields('epo_options'); ?>
			<?php do_settings_sections('epo_networks_facebook'); ?>
			<div id="facebook-advanced-settings-fields">
				<?php do_settings_sections('epo_networks_facebook_advanced'); ?>
			</div>
			<?php do_settings_sections('epo_networks_twitter'); ?>
			<div id="twitter-advanced-settings-fields">
				<?php do_settings_sections('epo_networks_twitter_advanced'); ?>
			</div>
			<?php do_settings_sections('epo_networks_linkedin'); ?>
			<div id="linkedin-advanced-settings-fields">
				<?php do_settings_sections('epo_networks_linkedin_advanced'); ?>
			</div>
			<?php do_settings_sections('epo_networks_google'); ?>
			<div id="google-advanced-settings-fields">
				<?php do_settings_sections('epo_networks_google_advanced'); ?>
			</div>
			<?php do_settings_sections('epo_networks_pusha'); ?>
			<div id="pusha-advanced-settings-fields">
				<?php do_settings_sections('epo_networks_pusha_advanced'); ?>
				<div id="picker" class="picker" style=""></div>
			</div>				
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes', EPO_TEXT_DOMAIN); ?>" /></p>
		</form>
	</div>
	
<?php
}

/**********************************************************************
 * Section text functions
 **********************************************************************/
function epo_general_section_text(){
	echo '<p>' . __('Here are the general settings for Entrepreneurship +1. You can further customize the settings when writing posts or pages.', EPO_TEXT_DOMAIN ) . '</p>';
}
 
function epo_facebook_section_text(){
	echo '<p>' . __('Enter your settings for Facebook.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_facebook_advanced_section_text(){
	echo '<p>' . __('Further customize your settings for Facebook.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_twitter_section_text(){
	echo '<p>' . __('Enter your settings for Twitter.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_twitter_advanced_section_text(){
	echo '<p>' . __('Further customize your settings for Twitter.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_linkedin_section_text(){
	echo '<p>' . __('Enter your settings for LinkedIn.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_linkedin_advanced_section_text(){
	echo '<p>' . __('Further customize your settings for LinkedIn.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_google_section_text(){
	echo '<p>' . __('Enter your settings for Google +1.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_google_advanced_section_text(){
	echo '<p>' . __('Further customize your settings for Google +1.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_pusha_section_text(){
	echo '<p>' . __('Enter your settings for Pusha.', EPO_TEXT_DOMAIN ) . '</p>';
}

function epo_pusha_advanced_section_text(){
	echo '<p>' . __('Further customize your settings for Pusha.', EPO_TEXT_DOMAIN ) . '</p>';
}

/**********************************************************************
 * Callback functions
 **********************************************************************/

 /* General Settings Page */
 
function epo_general_show_on_post_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='general_show_on_post' name='epo_options[general_show_on_post]' type='checkbox' value='1' <?php checked( $epo_options['general_show_on_post'], '1' ) ?> />
<?php
}

function epo_general_show_on_page_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='general_show_on_page' name='epo_options[general_show_on_page]' type='checkbox' value='1' <?php checked( $epo_options['general_show_on_page'], '1' ) ?> />
<?php
}
 
 
function epo_general_default_share_message_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='general_default_share_message' name='epo_options[general_default_share_message]'
		type='text' value='<?php echo esc_attr( $epo_options['general_default_share_message'] ); ?>' />
		<?php echo '<small>' . __('If you don\'t choose a call to action this will be chosen. This call to action is placed in the top of the widget and should contain a generic suggestion to your readers to share your post or page. On Disruptive I use "Tell your friends about Entrepreneurship"', EPO_TEXT_DOMAIN ) .  '</small>'; ?>
<?php
}
 
function epo_general_share_box_background_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input class="colorwell" id='general_share_box_background' name='epo_options[general_share_box_background]'
		type='text' value='<?php echo esc_attr( $epo_options['general_share_box_background'] ); ?>' />
<?php
}

function epo_general_share_box_border_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input class="colorwell" id='general_share_box_border' name='epo_options[general_share_box_border]'
		type='text' value='<?php echo esc_attr( $epo_options['general_share_box_border'] ); ?>' />
<?php
}
 
 
/* Social Networks Page */ 
function epo_facebook_activated_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='facebook_activated' name='epo_options[facebook_activated]' type='checkbox' value='1' <?php checked( $epo_options['facebook_activated'], '1' ) ?> />
	<a href="#" id="facebook-advanced-settings-button"  class="button-secondary"><?php _e('Show/Hide advanced settings', EPO_TEXT_DOMAIN); ?></a>
<?php
}
 
function epo_facebook_send_button_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='facebook_send_button' name='epo_options[facebook_send_button]' type='checkbox' value='1' <?php checked( $epo_options['facebook_send_button'], '1' ) ?> />
<?php
}

function epo_facebook_layout_style_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='facebook_layout_style' name='epo_options[facebook_layout_style]'>
		<option value='standard' <?php selected( $epo_options['facebook_layout_style'], 'standard' ); ?> ><?php _e('standard', EPO_TEXT_DOMAIN); ?></option>
		<option value='button_count' <?php selected( $epo_options['facebook_layout_style'], 'button_count' ); ?> ><?php _e('button_count', EPO_TEXT_DOMAIN); ?></option>
		<option value='box_count' <?php selected( $epo_options['facebook_layout_style'], 'box_count' ); ?> ><?php _e('box_count', EPO_TEXT_DOMAIN); ?></option>
	</select>
<?php		
}

function epo_facebook_show_faces_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='facebook_show_faces' name='epo_options[facebook_show_faces]' type='checkbox' value='1' <?php checked( $epo_options['facebook_show_faces'], '1' ) ?> />
<?php
}

function epo_facebook_verb_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='facebook_verb' name='epo_options[facebook_verb]'>
		<option value='like' <?php selected( $epo_options['facebook_verb'], 'like' ); ?> ><?php _e('like', EPO_TEXT_DOMAIN); ?></option>
		<option value='recommend' <?php selected( $epo_options['facebook_verb'], 'recommend' ); ?> ><?php _e('recommend', EPO_TEXT_DOMAIN); ?></option>
	</select>
<?php		
}

function epo_facebook_color_scheme_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='facebook_color_scheme' name='epo_options[facebook_color_scheme]'>
		<option value='light' <?php selected( $epo_options['facebook_color_scheme'], 'light' ); ?> ><?php _e('light', EPO_TEXT_DOMAIN); ?></option>
		<option value='dark' <?php selected( $epo_options['facebook_color_scheme'], 'dark' ); ?> ><?php _e('dark', EPO_TEXT_DOMAIN); ?></option>
	</select>
<?php		
}

function epo_facebook_font_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='facebook_font' name='epo_options[facebook_font]'>
		<option value='' <?php selected( $epo_options['facebook_font'], '' ); ?> ></option>
		<option value='arial' <?php selected( $epo_options['facebook_font'], 'arial' ); ?> ><?php _e('arial', EPO_TEXT_DOMAIN); ?></option>
		<option value='lucida grande' <?php selected( $epo_options['facebook_font'], 'lucida grande' ); ?> ><?php _e('lucida grande', EPO_TEXT_DOMAIN); ?></option>
		<option value='segoe ui' <?php selected( $epo_options['facebook_font'], 'segoe ui' ); ?> ><?php _e('segoe ui', EPO_TEXT_DOMAIN); ?></option>
		<option value='tahoma' <?php selected( $epo_options['facebook_font'], 'tahoma' ); ?> ><?php _e('tahoma', EPO_TEXT_DOMAIN); ?></option>
		<option value='trebuchet ms' <?php selected( $epo_options['facebook_font'], 'trebuchet ms' ); ?> ><?php _e('trebuchet ms', EPO_TEXT_DOMAIN); ?></option>
		<option value='verdana' <?php selected( $epo_options['facebook_font'], 'verdana' ); ?> ><?php _e('verdana', EPO_TEXT_DOMAIN); ?></option>
	</select>
<?php		
}

function epo_twitter_activated_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='twitter_activated' name='epo_options[twitter_activated]' type='checkbox' value='1' <?php checked( $epo_options['twitter_activated'], '1' ) ?> />
	<a href="#" id="twitter-advanced-settings-button"  class="button-secondary"><?php _e('Show/Hide advanced settings', EPO_TEXT_DOMAIN); ?></a>
<?php
}

function epo_twitter_default_hash_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='twitter_default_hash' name='epo_options[twitter_default_hash]' type='text' value='<?php echo esc_attr( $epo_options['twitter_default_hash'] ); ?>' />
			<?php echo '<small>' . __('This hash tag will be used as default when your visitors click on Twitter button and retweet your content.', EPO_TEXT_DOMAIN ) .  '</small>'; ?>
<?php
}

function epo_twitter_data_count_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='twitter_data_count' name='epo_options[twitter_data_count]'>
		<option value='vertical' <?php selected( $epo_options['twitter_data_count'], 'vertical' ); ?> ><?php _e('vertical', EPO_TEXT_DOMAIN); ?></option>
		<option value='horizontal' <?php selected( $epo_options['twitter_data_count'], 'horizontal' ); ?> ><?php _e('horizontal', EPO_TEXT_DOMAIN); ?></option>
		<option value='none' <?php selected( $epo_options['twitter_data_count'], 'none' ); ?> ><?php _e('none', EPO_TEXT_DOMAIN); ?></option>
	</select>
<?php		
}

function epo_twitter_data_via_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='twitter_data_via' name='epo_options[twitter_data_via]' type='text' value='<?php echo esc_attr( $epo_options['twitter_data_via'] ); ?>' />
<?php
}

function epo_twitter_data_related_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='twitter_data_related' name='epo_options[twitter_data_related]' type='text' value='<?php echo esc_attr( $epo_options['twitter_data_related'] ); ?>' />
<?php
}

function epo_twitter_data_related_description_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='twitter_data_related_description' name='epo_options[twitter_data_related_description]' type='text' value='<?php echo esc_attr( $epo_options['twitter_data_related_description'] ); ?>' />
<?php
}

function epo_linkedin_activated_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='linkedin_activated' name='epo_options[linkedin_activated]' type='checkbox' value='1' <?php checked( $epo_options['linkedin_activated'], '1' ) ?> />
	<a href="#" id="linkedin-advanced-settings-button"  class="button-secondary"><?php _e('Show/Hide advanced settings', EPO_TEXT_DOMAIN); ?></a>
<?php
}

function epo_linkedin_data_counter_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='linkedin_data_counter' name='epo_options[linkedin_data_counter]'>
		<option value='top' <?php selected( $epo_options['linkedin_data_counter'], 'top' ); ?> ><?php _e('top', EPO_TEXT_DOMAIN); ?></option> <?php // TODO: Translation needed for dropdowns? ?>
		<option value='right' <?php selected( $epo_options['linkedin_data_counter'], 'right' ); ?> ><?php _e('right', EPO_TEXT_DOMAIN); ?></option>
		<option value='none' <?php selected( $epo_options['linkedin_data_counter'], 'none' ); ?> ><?php _e('none', EPO_TEXT_DOMAIN); ?></option>
	</select>
<?php		
}

function epo_google_activated_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='google_activated' name='epo_options[google_activated]' type='checkbox' value='1' <?php checked( $epo_options['google_activated'], '1' ) ?> />
	<a href="#" id="google-advanced-settings-button"  class="button-secondary"><?php _e('Show/Hide advanced settings', EPO_TEXT_DOMAIN); ?></a>
<?php
}

function epo_google_size_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='google_size' name='epo_options[google_size]'>		
		<option value='small' <?php selected( $epo_options['google_size'], 'small' ); ?> ><?php _e('small (15px)', EPO_TEXT_DOMAIN); ?></option>
		<option value='standard' <?php selected( $epo_options['google_size'], 'standard' ); ?> ><?php _e('standard (24px)', EPO_TEXT_DOMAIN); ?></option> <?php // TODO: Translation needed for dropdowns? ?>		
		<option value='medium' <?php selected( $epo_options['google_size'], 'medium' ); ?> ><?php _e('medium (20px)', EPO_TEXT_DOMAIN); ?></option>
		<option value='tall' <?php selected( $epo_options['google_size'], 'tall' ); ?> ><?php _e('tall (60px)', EPO_TEXT_DOMAIN); ?></option>
	</select>
<?php		
}

function epo_google_count_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='google_count' name='epo_options[google_count]' type='checkbox' value='1' <?php checked( $epo_options['google_count'], '1' ) ?> />
<?php
}

function epo_google_parse_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<select id='google_parse' name='epo_options[google_parse]'>		
		<option value='default' <?php selected( $epo_options['google_parse'], 'default' ); ?> ><?php _e('default', EPO_TEXT_DOMAIN); ?></option>
		<option value='explicit' <?php selected( $epo_options['google_parse'], 'explicit' ); ?> ><?php _e('explicit', EPO_TEXT_DOMAIN); ?></option> <?php // TODO: Translation needed for dropdowns? ?>		
	</select>
<?php		
}

function epo_google_js_callback_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='google_js_callback' name='epo_options[google_js_callback]'
		type='text' value='<?php echo esc_attr( $epo_options['google_js_callback'] ); ?>' />
<?php
}

function epo_pusha_activated_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='pusha_activated' name='epo_options[pusha_activated]' type='checkbox' value='1' <?php checked( $epo_options['pusha_activated'], '1' ) ?> />
	<a href="#" id="pusha-advanced-settings-button"  class="button-secondary"><?php _e('Show/Hide advanced settings', EPO_TEXT_DOMAIN); ?></a>
<?php
}

function epo_pusha_bakgrund_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input class="colorwell" id='pusha_bakgrund' name='epo_options[pusha_bakgrund]'
		type='text' value='<?php echo esc_attr( $epo_options['pusha_bakgrund'] ); ?>' />
<?php
}

function epo_pusha_nyttfonster_callback( $input ){
	$epo_options = get_option( 'epo_options' );
?>
	<input id='pusha_nyttfonster' name='epo_options[pusha_nyttfonster]' type='checkbox' value='1' <?php checked( $epo_options['pusha_nyttfonster'], '1' ) ?> />
<?php
}

/**********************************************************************
 * Add contextual help
 **********************************************************************/
add_filter('contextual_help', 'epo_contextual_help', 10, 3);
function epo_contextual_help($contextual_help, $screen_id, $screen) {

	global $epo_page;


	if ($screen_id == $epo_page['general']) {
		$epo_contextual_help = __('At present time there are no help for the general page.', EPO_TEXT_DOMAIN);
	}elseif ($screen_id == $epo_page['networks']) {
		$epo_contextual_help = __('At present time there are no help for the networks page.', EPO_TEXT_DOMAIN);
	}else {
		// Default help text	
		$epo_contextual_help = $contextual_help;
	}

	return $epo_contextual_help;
}

/**********************************************************************
 * Meta Boxes
 **********************************************************************/
add_action( 'add_meta_boxes',  'epo_add_meta_boxes' );
function epo_add_meta_boxes() {
	add_meta_box( 'epo-meta', __('Entrepreneurship +1', EPO_TEXT_DOMAIN), 'epo_show_meta_box', 'post', 'normal', 'high');
	add_meta_box( 'epo-meta', __('Entrepreneurship +1', EPO_TEXT_DOMAIN), 'epo_show_meta_box', 'page', 'normal', 'high');
}

// Display the meta box data
function epo_show_meta_box( $post ) {
	// Retrieve the metadata values if they exist
	$epo_share_message = get_post_meta( $post->ID, '_epo_share_message', true );
	$epo_twitter_title = get_post_meta( $post->ID, '_epo_twitter_title', true );	
	$epo_twitter_hash = get_post_meta( $post->ID, '_epo_twitter_hash', true );
	$epo_twitter_data_via = get_post_meta( $post->ID, '_epo_twitter_data_via', true );
	$epo_twitter_data_related = get_post_meta( $post->ID, '_epo_twitter_data_related', true );
	$epo_twitter_data_related_description = get_post_meta( $post->ID, '_epo_twitter_data_related_description', true );
	$epo_pusha_titel = get_post_meta( $post->ID, '_epo_pusha_titel', true );
	$epo_pusha_beskrivning = get_post_meta( $post->ID, '_epo_pusha_beskrivning', true );
	
?>
	<p>
		<em><?php _e('From this box you can do some customizations for this post/page.', EPO_TEXT_DOMAIN); ?></em>
	</p>
	<table>	
		<tr>
			<td><?php _e('Share message', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_share_message" value="<?php echo esc_attr( $epo_share_message ); ?>" /></td>
			<td></td>			
		</tr>		
		<tr>		
			<th><?php _e('Twitter', EPO_TEXT_DOMAIN); ?></th>
		</tr>			
		<tr>
			<td><?php _e('Title', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_twitter_title" value="<?php echo esc_attr( $epo_twitter_title ); ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><?php _e('Hash', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_twitter_hash" value="<?php echo esc_attr( $epo_twitter_hash ); ?>" /></td>
			<td><small><?php _e('Dont forget to add # before the tag. Example: #Entrepreneurship', EPO_TEXT_DOMAIN); ?></small></td>
		</tr>
		<tr>
			<td><?php _e('Via account', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_twitter_data_via" value="<?php echo esc_attr( $epo_twitter_data_via ); ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><?php _e('Releated account', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_twitter_data_related" value="<?php echo esc_attr( $epo_twitter_data_related ); ?>" /></td>
			<td></td>			
		</tr>
		<tr>
			<td><?php _e('Releated account description', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_twitter_data_related_description" value="<?php echo esc_attr( $epo_twitter_data_related_description ); ?>" /></td>
			<td></td>			
		</tr>
		<tr>		
			<th><?php _e('Pusha', EPO_TEXT_DOMAIN); ?></th>
		</tr>		
		<tr>
			<td><?php _e('Title', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_pusha_titel" value="<?php echo esc_attr( $epo_pusha_titel ); ?>" /></td>
			<td></td>			
		</tr>
		<tr>
			<td><?php _e('Description', EPO_TEXT_DOMAIN); ?></td>
			<td><input type="text" name="epo_pusha_beskrivning" value="<?php echo esc_attr( $epo_pusha_beskrivning ); ?>" /></td>
			<td></td>			
		</tr>
	</table>
	
	
<?php

}

// Save the meta box data
add_action( 'save_post',  'epo_save_meta_box' );
add_action( 'save_page',  'epo_save_meta_box' );
function epo_save_meta_box( $post_id ) {
	// Verify the meta data is set
	if ( isset( $_POST['epo_share_message'] ) ) {
		update_post_meta( $post_id, '_epo_share_message', wp_filter_nohtml_kses( $_POST['epo_share_message'] ) );
	}
	if ( isset( $_POST['epo_twitter_title'] ) ) {
		update_post_meta( $post_id, '_epo_twitter_title', wp_filter_nohtml_kses( $_POST['epo_twitter_title'] ) );
	}	
	if ( isset( $_POST['epo_twitter_hash'] ) ) {
		update_post_meta( $post_id, '_epo_twitter_hash', wp_filter_nohtml_kses( $_POST['epo_twitter_hash'] ) );
	}
	if ( isset( $_POST['epo_twitter_data_via'] ) ) {
		update_post_meta( $post_id, '_epo_twitter_data_via', wp_filter_nohtml_kses( $_POST['epo_twitter_data_via'] ) );
	}
	if ( isset( $_POST['epo_twitter_data_related'] ) ) {
		update_post_meta( $post_id, '_epo_twitter_data_related', wp_filter_nohtml_kses( $_POST['epo_twitter_data_related'] ) );
	}
	if ( isset( $_POST['epo_twitter_data_related_description'] ) ) {
		update_post_meta( $post_id, '_epo_twitter_data_related_description', wp_filter_nohtml_kses( $_POST['epo_twitter_data_related_description'] ) );
	}
	
	if ( isset( $_POST['epo_pusha_titel'] ) ) {
		update_post_meta( $post_id, '_epo_pusha_titel', wp_filter_nohtml_kses( $_POST['epo_pusha_titel'] ) );
	}
	
	if ( isset( $_POST['epo_pusha_beskrivning'] ) ) {
		update_post_meta( $post_id, '_epo_pusha_beskrivning', wp_filter_nohtml_kses( $_POST['epo_pusha_beskrivning'] ) );
	}	
}

/**********************************************************************
 * Front-end functions
 **********************************************************************/
add_filter("the_content","epo_share_box");
function epo_share_box($content) {
	global $post;
	$epo_options = get_option('epo_options');
	
	// Where to show the share box?
	// TODO: How to choose a custom post type?
	if( is_home() || ( is_single() && !$epo_options['general_show_on_post'] ) || ( is_page()  && !$epo_options['general_show_on_page'] ) ) {
		return $content;
	}
	
	// Create share box
	$share_box = '<div class="epo-share-box-container">';
	// Check if there is a custom message
	$share_box .= '<div class="epo-share-message">';
	if ( get_post_meta( $post->ID, '_epo_share_message', true ) ) {
		$share_box .= get_post_meta($post->ID, '_epo_share_message', true);
	}else{
		$share_box .= $epo_options['general_default_share_message'];
	}
	$share_box .= '</div>';
	$share_box .= '<div class="epo-share-buttons">';
	if($epo_options['twitter_activated'] == 1 ){
		$share_box .= '<div class="epo-share-button" id="epo-share-button-twitter">';
		$share_box .= epo_twitter();
		$share_box .= '</div>';
	}
	if($epo_options['facebook_activated'] == 1 && $epo_options['facebook_layout_style'] == 'box_count' ){
		$share_box .= '<div class="epo-share-button"  id="epo-share-button-facebook">';
		$share_box .= epo_facebook();
		$share_box .= '</div>';
	}
	if($epo_options['linkedin_activated'] == 1 ){
		$share_box .= '<div class="epo-share-button" id="epo-share-button-linkedin">';
		$share_box .= epo_linkedin();
		$share_box .= '</div>';
	}
	if($epo_options['google_activated'] == 1 ){
		$share_box .= '<div class="epo-share-button" id="epo-share-button-google">';
		$share_box .= epo_google();
		$share_box .= '</div>';
	}
	if($epo_options['pusha_activated'] == 1 ){
		$share_box .= '<div class="epo-share-button" id="epo-share-button-pusha">';
		$share_box .= epo_pusha();
		$share_box .= '</div>';
	}	
	$share_box .= '</div>';
	if($epo_options['facebook_activated'] == 1 && $epo_options['facebook_layout_style'] != 'box_count' ){
		$share_box .= '<div id="epo-share-button-facebook-' . $epo_options['facebook_layout_style'] . '">';
		$share_box .= epo_facebook();
		$share_box .= '</div>';
	}	
	//$share_box .= '<div class="epo-created-by">' . __('Created by', EPO_TEXT_DOMAIN) . ' <a href="http://disruptive.nu">disruptive.nu</a></div>';
	$share_box .= '</div>';
	$content .= $share_box;
	
	return $content;
	
}

// Add facebook XFBML to <HTML>
// http://designpx.net/thesis/facebook-open-graph-protocol-meta-tags/
add_filter('language_attributes', 'epo_facebook_xfbml');
function epo_facebook_xfbml($content) {
	return 'xmlns:fb="http://www.facebook.com/2008/fbml" ' . $content;
}

// Get Facebook button (if activated)
function epo_facebook() {
	global $post;
	$epo_options = get_option('epo_options');
	$facebook = '<div id="fb-root"></div>';
	$facebook .= '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>';
	if( $epo_options['facebook_layout_style'] == 'box_count' ) {
		$epo_facebook_width = 60;
	}elseif ( $epo_options['facebook_layout_style'] == 'button_count' ) {
		$epo_facebook_width = 100;
	}elseif ( $epo_options['facebook_layout_style'] == 'standard' ) {
		$epo_facebook_width = 450;
	}
	$facebook .= '<fb:like width="' . $epo_facebook_width . '"';
	$facebook .= ' href="' . get_permalink( $post->ID ) . '"';
	$facebook .= ' layout="' . $epo_options['facebook_layout_style'] . '"';
	$facebook .= ' action="' . $epo_options['facebook_verb'] . '"';
	$facebook .= ' colorscheme="' . $epo_options['facebook_color_scheme'] . '"';
	$facebook .= ' font="' . $epo_options['facebook_font'] . '"';
	if($epo_options['facebook_send_button'] == 1 ){
		$facebook .= ' send="true"';
	}else{
		$facebook .= ' send="false"';	
	}
	if($epo_options['facebook_show_faces'] == 1 ){
		$facebook .= ' show_faces="true"';
	}else{
		$facebook .= ' show_faces="false"';	
	}	
	$facebook .= '></fb:like>';
	return $facebook;
}

// Get Twitter button (if activated)
function epo_twitter() {
	global $post;
	$epo_options = get_option('epo_options');
	
	$twitter = '<a href="http://twitter.com/share" class="twitter-share-button"';
	
	if( get_post_meta($post->ID, '_epo_twitter_title', true) ) {
		$epo_twitter_title = get_post_meta($post->ID, '_epo_twitter_title', true);
	}else {
		$epo_twitter_title = get_the_title($post->ID);
	}
	if( get_post_meta($post->ID, '_epo_twitter_hash', true) ) {
		$epo_twitter_hash = get_post_meta($post->ID, '_epo_twitter_hash', true);
	}else {
		$epo_twitter_hash = $epo_options['twitter_default_hash'];
	}	
	$twitter .= ' data-text="' . $epo_twitter_title . ' ' . $epo_twitter_hash . '"';
	
	$twitter .= ' data-count="' . $epo_options['twitter_data_count'] . '"';
	
	if( get_post_meta($post->ID, '_epo_twitter_data_via', true) ) {
		$epo_twitter_data_via = get_post_meta($post->ID, '_epo_twitter_data_via', true);
	}else {
		$epo_twitter_data_via = $epo_options['twitter_data_via'];
	}	
	$twitter .= ' data-via="' . $epo_twitter_data_via . '"';
	
	if( get_post_meta($post->ID, '_epo_twitter_data_related', true) ) {
		$epo_twitter_data_related = get_post_meta($post->ID, '_epo_twitter_data_related', true);
	}else {
		$epo_twitter_data_related = $epo_options['twitter_data_related'];
	}
	if( get_post_meta($post->ID, '_epo_twitter_data_related_description', true) ) {
		$epo_twitter_data_related_description = ':' . get_post_meta($post->ID, '_epo_twitter_data_related_description', true);
	}else {
		$epo_twitter_data_related_description = ':' . $epo_options['twitter_data_related_description'];
	}
	$twitter .= ' data-related="' . $epo_twitter_data_related . $epo_twitter_data_related_description . '"';
	$twitter .= '>Tweet</a>';
	$twitter .= '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';

	return $twitter;
}

// Get LinkedIn button (if activated)
function epo_linkedin() {
	global $post;
	$epo_options = get_option('epo_options');
	$linkedin = '<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script>';
	$linkedin .= '<script type="in/share" data-url="' . get_permalink( $post->ID ) . '"';
	if($epo_options['linkedin_data_counter'] != 'none' ){
		$linkedin .= ' data-counter="' . $epo_options['linkedin_data_counter'] . '"';
	}
	$linkedin .= '></script>';
	return $linkedin;
}

// Get Google +1 button (if activated)
function epo_google() {
	global $post;
	$epo_options = get_option('epo_options');
	$google = '<script type="text/javascript" src="https://apis.google.com/js/plusone.js">';
	if($epo_options['google_parse'] == 'explicit' ){
		$google .= '{parsetags: \'explicit\'}';
	}
	$google .= '</script>';	
	$google .= '<g:plusone';
	if($epo_options['google_count'] != 1 ){
		$google .= ' count="false"';
	}
	if($epo_options['google_size'] != 'standard' ){	
		$google .= ' size="' . $epo_options['google_size'] . '"';
	}
	if($epo_options['google_js_callback'] != '' ){	
		$google .= ' callback="' . $epo_options['google_js_callback'] . '"';
	}
	$google .= ' href="' . get_permalink( $post->ID ) . '"';
	$google .= '></g:plusone>';
	return $google;
}

// Get Pusha button (if activated)
function epo_pusha() {
	global $post;
	$epo_options = get_option('epo_options');
	$pusha = '<script src="http://www.pusha.se/knapp/pusha.js" type="text/javascript"></script>';
	$pusha .= '<script type="text/javascript">';
	$pusha .= ' var pusha_url="' . get_permalink( $post->ID ) . '";';
	$pusha .= ' var pusha_bakgrund="' . $epo_options['pusha_bakgrund'] . '";';
	if( get_post_meta($post->ID, '_epo_pusha_titel', true) ) {
		$pusha .= ' var pusha_titel="' . get_post_meta($post->ID, '_epo_pusha_titel', true) . '";';
	}else{
		$pusha .= ' var pusha_titel="' . get_the_title($post->ID) . '";';
	}
	if( get_post_meta($post->ID, '_epo_pusha_beskrivning', true) ) {
		$pusha .= ' var pusha_beskrivning="' . get_post_meta($post->ID, '_epo_pusha_beskrivning', true) . '";';
	}
	if($epo_options['pusha_nyttfonster'] == 1 ){
		$pusha .= ' var pusha_nyttfonster="true";';
	}	
	$pusha .= '</script>';
	return $pusha;
}
?>