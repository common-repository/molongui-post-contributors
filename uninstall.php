<?php

use Molongui\Contributors\Common\Utils\WP;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
defined( 'WP_UNINSTALL_PLUGIN' ) or exit; // Exit if not called by WordPress
if ( dirname( WP_UNINSTALL_PLUGIN ) !== dirname( plugin_basename( __FILE__ ) ) )
{
    status_header( 404 );
    exit;
}
if ( !current_user_can( 'activate_plugins' ) ) return;
if ( function_exists( 'is_multisite' ) and is_multisite() )
{
	foreach ( WP::get_sites() as $site_id )
	{
		switch_to_blog( $site_id );
		molongui_contributors_uninstall_single_site();
		restore_current_blog();
	}
}
else
{
	molongui_contributors_uninstall_single_site();
}
function molongui_contributors_uninstall_single_site()
{
	global $wpdb;

    $plugin_name   = 'molongui-contributors';
    $plugin_prefix = 'molongui_contributors';
    $options       = get_option( $plugin_prefix.'_options' );
    if ( isset( $options['keep_config'] ) and $options['keep_config'] == 0 )
    {
        $like = $plugin_prefix.'_%';
        $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    }
    if ( isset( $options['keep_data'] ) and $options['keep_data'] == 0 )
	{
        $taxonomy = 'contributor_role';
        $terms = get_terms( array
        (
            'taxonomy'   => $taxonomy,
            'hide_empty' => false
        ));
        if ( !is_wp_error( $terms ) and is_array( $terms ) ) foreach ( $terms as $term )
        {
            wp_delete_term( $term->term_id, $taxonomy );
        }
	}
    $like = '_transient_'.$plugin_name.'%';
    $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}