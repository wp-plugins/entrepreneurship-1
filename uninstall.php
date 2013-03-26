<?php
// If uninstall not called from WordPress exit
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
// Delete option from options database
delete_option('epo_options');

// Delete all post meta data

$args = array( 'numberposts' => -1,
				'post_type'=> any,
				'post_status' => any
			);
			
$allposts = get_posts( $args );

foreach( $allposts as $postinfo) {
    delete_post_meta($postinfo->ID, '_epo_share_message');
	delete_post_meta($postinfo->ID, '_epo_twitter_title');
	delete_post_meta($postinfo->ID, '_epo_twitter_hash');
	delete_post_meta($postinfo->ID, '_epo_twitter_data_via');
	delete_post_meta($postinfo->ID, '_epo_twitter_data_related');
	delete_post_meta($postinfo->ID, '_epo_twitter_data_related_description');
	delete_post_meta($postinfo->ID, '_epo_pusha_titel');
	delete_post_meta($postinfo->ID, '_epo_pusha_beskrivning');
}

?>