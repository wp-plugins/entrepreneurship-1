jQuery(document).ready(function() {

	// Hide all advanced fields per default
	jQuery('#facebook-advanced-settings-fields').hide();
	jQuery('#twitter-advanced-settings-fields').hide();
	jQuery('#linkedin-advanced-settings-fields').hide();
	jQuery('#google-advanced-settings-fields').hide();
	jQuery('#pusha-advanced-settings-fields').hide();
	
	// Facebook
	jQuery('#facebook-advanced-settings-button').click(function() {
		jQuery('#facebook-advanced-settings-fields').toggle();
	});

	// Twitter
	jQuery('#twitter-advanced-settings-button').click(function() {
		jQuery('#twitter-advanced-settings-fields').toggle();
	});
	
	// LinkedIn
	jQuery('#linkedin-advanced-settings-button').click(function() {
		jQuery('#linkedin-advanced-settings-fields').toggle();
	});	

	// Google +1
	jQuery('#google-advanced-settings-button').click(function() {
		jQuery('#google-advanced-settings-fields').toggle();
	});	
	
	// Pusha
	jQuery('#pusha-advanced-settings-button').click(function() {
		jQuery('#pusha-advanced-settings-fields').toggle();
	});		
	
});