<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Utils\WP;
use Molongui\Contributors\Common\Modules\DB_Update;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Activator
{
    public static function activate( $network_wide )
    {
	    if ( function_exists( 'is_multisite' ) and is_multisite() and $network_wide )
	    {
		    if ( !is_super_admin() ) return;
		    foreach ( WP::get_sites() as $site_id )
		    {
			    switch_to_blog( $site_id );
			    self::activate_single_blog();
			    restore_current_blog();
		    }
        }
        else
        {
	        if ( !current_user_can( 'activate_plugins' ) ) return;

	        self::activate_single_blog();
        }
    }
	private static function activate_single_blog()
	{
        wp_cache_flush();
        $update_db = new DB_Update( MOLONGUI_CONTRIBUTORS_DB_SCHEMA, MOLONGUI_CONTRIBUTORS_DB_VERSION, MOLONGUI_CONTRIBUTORS_NAMESPACE );
        if ( $update_db->db_update_needed() )
        {
            $update_db->run_update();
        }
		self::save_installation_data();
		self::add_default_options();
        self::run_background_tasks();
        self::maybe_redirect();
    }
	public static function activate_on_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta )
	{
		if ( is_plugin_active_for_network( MOLONGUI_CONTRIBUTORS_BASENAME ) )
		{
			switch_to_blog( $blog_id );
			self::activate_single_blog();
			restore_current_blog();
		}
	}
	public static function save_installation_data()
	{
		if ( get_option( MOLONGUI_CONTRIBUTORS_INSTALL ) )
        {
            return;
        }
        $installation = array
        (
            'timestamp' => time(),
            'version'   => MOLONGUI_CONTRIBUTORS_VERSION,
        );
		add_option( MOLONGUI_CONTRIBUTORS_INSTALL, $installation, '', false );
	}
    public static function add_default_options()
    {
        Settings::add_defaults();
    }
    public static function run_background_tasks()
    {
        if ( apply_filters( 'molongui_contributors/check_wp_cron', true ) and ( defined( 'DISABLE_WP_CRON' ) and DISABLE_WP_CRON ) )
        {
            return false;
        }
    }
    public static function maybe_redirect()
    {
        set_transient( MOLONGUI_CONTRIBUTORS_NAME.'-activation-redirect', true, 30 );
    }

} // class