<?php

/*!
 * Molongui Post Contributors
 *
 * @package           Molongui Post Contributors
 * @author            Molongui
 * @copyright         2024 Molongui
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Molongui Post Contributors
 * Plugin URI:        https://www.molongui.com/wordpress-plugin-post-contributors
 * Description:       Add reviewers, fact-checkers, illustrators and any other attribution to your posts.
 * Version:           1.6.1
 * Requires at least: 5.2
 * Tested up to:      6.6
 * Requires PHP:      5.6.20
 * Author:            Molongui
 * Author URI:        https://www.molongui.com
 * Text Domain:       molongui-post-contributors
 * Domain Path:       /i18n
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * This plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or any later version.
 *
 * This plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this plugin. If not, see
 * http://www.gnu.org/licenses.
 */

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Common\Modules\DB_Update;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
final class MolonguiPostContributors
{
    const VERSION = '1.6.1';
    private static $_instance = null;
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( "Cloning instances of this class is forbidden.", 'molongui-post-contributors' ), '1.0.0' );
    }
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( "Unserializing instances of this class is forbidden.", 'molongui-post-contributors' ), '1.0.0' );
    }
    public static function instance()
    {
        if ( is_null( self::$_instance ) )
        {
            self::$_instance = new self();
            do_action( 'molongui_contributors/loaded' );
        }

        return self::$_instance;
    }
    public function __construct()
    {
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
        if ( !$this->checks_passed() )
        {
            return false;
        }
        self::define_constants();
        require_once MOLONGUI_CONTRIBUTORS_DIR . 'common/autoloader.php';
        register_activation_hook( MOLONGUI_CONTRIBUTORS_FILE  , array( $this, 'activate'   ) );
        register_deactivation_hook( MOLONGUI_CONTRIBUTORS_FILE, array( $this, 'deactivate' ) );
        add_action( 'wpmu_new_blog', array( $this, 'activate_on_new_blog' ), 10, 6 );
        add_action( 'plugin_loaded' , array( $this, 'on_plugin_loaded'  ) );
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
        return true;
    }
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain( 'molongui-post-contributors', false, plugin_dir_path( __FILE__ ) . 'i18n/' );
    }
    public function checks_passed()
    {
        if ( version_compare( PHP_VERSION, '5.6.20', '<' ) )
        {
            add_action( 'admin_notices', array( $this, 'fail_php_error' ) );
            return false;
        }
        if ( version_compare( get_bloginfo( 'version' ), '5.2', '<' ) )
        {
            add_action( 'admin_notices', array( $this, 'fail_wp_error' ) );
            return false;
        }

        return true;
    }
    function fail_php_error()
    {
        $min_php_version = '5.6.20';

        if ( isset( $_GET['activate'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            unset( $_GET['activate'] );
        }

        /*! // translators: %1$s: The plugin name. %2$s: Min required PHP version. %3$s: User PHP version */
        $message  = sprintf( esc_html__( '%1$s requires PHP version %2$s or greater to operate. Unfortunately, your current PHP version (%3$s) is too old, so the plugin has been disabled.', 'molongui-post-contributors' ), '<strong>Molongui Post Contributors</strong>', $min_php_version, PHP_VERSION );
        $message .= sprintf( '<p><a href="%s" class="button-primary" target="_blank">%s</a></p>', 'https://www.molongui.com/help/how-to-update-my-php-version/', __( "How to update PHP?", 'molongui-post-contributors' ) );
        $html_message = sprintf( '<div class="notice notice-error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
    function fail_wp_error()
    {
        $min_wp_version = '5.2';

        if ( isset( $_GET['activate'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            unset( $_GET['activate'] );
        }

        /*! // translators: %1$s: The plugin name. %2$s: Min required WordPress version */
        $message = sprintf( esc_html__( '%1$s requires WordPress version %2$s or higher. Please update your WordPress to run this plugin.', 'molongui-post-contributors' ), '<strong>Molongui Post Contributors</strong>', $min_wp_version );
        $html_message = sprintf( '<div class="notice notice-error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    }
    public function define_constants()
    {
        foreach( self::get_constants() as $name => $value )
        {
            if ( !defined( $name ) )
            {
                define( $name, $value );
            }
        }
    }
    public static function get_constants()
    {
        return array
        (
            'MOLONGUI_CONTRIBUTORS_VERSION'    => self::VERSION,
            'MOLONGUI_CONTRIBUTORS_FILE'       => __FILE__,                              // /var/www/domain/wp-content/plugins/molongui-post-contributors/molongui-post-contributors.php
            'MOLONGUI_CONTRIBUTORS_DIR'        => plugin_dir_path( __FILE__ ),           // /var/www/domain/wp-content/plugins/molongui-post-contributors/
            'MOLONGUI_CONTRIBUTORS_FOLDER'     => basename( dirname( __FILE__ ) ),       // molongui-post-contributors
            'MOLONGUI_CONTRIBUTORS_URL'        => plugin_dir_url( __FILE__ ),            // https://domain.tld/wp-content/plugins/molongui-post-contributors/
            'MOLONGUI_CONTRIBUTORS_BASENAME'   => plugin_basename( __FILE__ ),           // molongui-post-contributors/molongui-post-contributors.php
            'MOLONGUI_CONTRIBUTORS_NAMESPACE'  => '\Molongui\Contributors',
            'MOLONGUI_CONTRIBUTORS_PREFIX'     => 'molongui_contributors',
            'MOLONGUI_CONTRIBUTORS_NAME'       => 'molongui-post-contributors',          // slug
            'MOLONGUI_CONTRIBUTORS_DB_SCHEMA'  => 2,
            'MOLONGUI_CONTRIBUTORS_DB_VERSION' => 'molongui_contributors_db_version',    // Options key
            'MOLONGUI_CONTRIBUTORS_INSTALL'    => 'molongui_contributors_install',       // Options key
            'MOLONGUI_CONTRIBUTORS_NOTICES'    => 'molongui_contributors_notices',       // Options key
            'MOLONGUI_CONTRIBUTORS_ID'         => 'contributors',
            'MOLONGUI_CONTRIBUTORS_TITLE'      => 'Molongui Post Contributors',
            'MOLONGUI_CONTRIBUTORS_DEBUG'      => false,
            'MOLONGUI_CONTRIBUTORS_HAS_PRO'    => true,
            'MOLONGUI_CONTRIBUTORS_MIN_PRO'    => '1.0.0',
            'MOLONGUI_CONTRIBUTORS_WEB'        => 'https://www.molongui.com/wordpress-plugin-post-contributors/',
            'MOLONGUI_CONTRIBUTORS_DEMO'       => 'https://demos.molongui.com/test-drive-molongui-post-contributors-pro/',
        );
    }
    public function activate( $network_wide )
    {
        Activator::activate( $network_wide );
    }
    public function deactivate( $network_wide )
    {
        Deactivator::deactivate( $network_wide );
    }
    public function activate_on_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta )
    {
        Activator::activate_on_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta );
    }
    public function on_plugin_loaded( $plugin )
    {
        if ( MOLONGUI_CONTRIBUTORS_FILE !== $plugin )
        {
            return;
        }
        require_once MOLONGUI_CONTRIBUTORS_DIR . 'includes/overwrites.php';
    }
    public function on_plugins_loaded()
    {
        self::maybe_enable_debug_mode();
        if ( self::is_disabled() )
        {
            if ( class_exists( '\Molongui\Contributors\Common\Utils\Debug' ) )
            {
                Debug::console_log( null, "The ".MOLONGUI_CONTRIBUTORS_TITLE." plugin is disabled. Remove the 'noContributors' query string from the URL in order to enable it." );
            }
            return false;
        }
        $this->update_db();

        if ( $this->is_compatible() )
        {
            $this->init();
        }
    }
    private function maybe_enable_debug_mode()
    {
        if ( !is_admin() and isset( $_GET['debugContributors'] ) and 0 !== $_GET['debugContributors'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            add_filter( 'molongui_contributors/debug', '__return_true' );
        }
    }
    private function is_disabled()
    {
        if ( !is_admin() and isset( $_GET['noContributors'] ) and 0 !== $_GET['noContributors'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            return true;
        }

        return false;
    }
    private function is_compatible()
    {

        return true;
    }
    private function update_db()
    {
        $update_db = new DB_Update( MOLONGUI_CONTRIBUTORS_DB_SCHEMA, MOLONGUI_CONTRIBUTORS_DB_VERSION, MOLONGUI_CONTRIBUTORS_NAMESPACE );
        if ( $update_db->db_update_needed() )
        {
            $update_db->run_update();
        }
    }
    public function init()
    {
        $paths = array
        (
            MOLONGUI_CONTRIBUTORS_DIR . 'dropins/',

            MOLONGUI_CONTRIBUTORS_DIR . 'includes/contributor.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/contributor-role.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/post.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/settings.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/template.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/template-tags.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/user.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/integrations.php',

            MOLONGUI_CONTRIBUTORS_DIR . 'includes/wizard.php',
            MOLONGUI_CONTRIBUTORS_DIR . 'includes/pointers.php',

            MOLONGUI_CONTRIBUTORS_DIR . 'includes/shortcodes/',
            MOLONGUI_CONTRIBUTORS_DIR . 'common/hooks.php',
        );
        foreach ( $paths as $path )
        {
            self::require_file( $path );
        }
        Debug::console_log( null, sprintf( "%s %s", MOLONGUI_CONTRIBUTORS_TITLE, MOLONGUI_CONTRIBUTORS_VERSION ) );
        do_action( 'molongui_contributors/init' );
    }
    public static function require_file( $path )
    {
        if ( is_file( $path ) and file_exists( $path ) )
        {
            require_once $path;
        }
        elseif ( is_dir( $path ) )
        {
            foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path ) ) as $file )
            {

                if ( $file->isFile() and 'php' === $file->getExtension() and 'index.php' !== $file->getFilename() )
                {
                    require_once $file->getPathname();
                }
            }
        }
    }

} // class
MolonguiPostContributors::instance();