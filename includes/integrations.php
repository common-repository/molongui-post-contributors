<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Modules\Settings;
use Molongui\Contributors\Common\Utils\Debug;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Integrations
{
    public $themes;
    public $plugins;
    public function __construct()
    {
        if ( !function_exists( 'is_plugin_active' ) )
        {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $this->themes  = self::get_themes();
        $this->plugins = self::get_plugins();

        $this->theme_integration();
        $this->plugins_integration();
    }
    public function theme_integration()
    {
        $options = Settings::get();
        if ( !empty( $options['post_template_override'] ) )
        {
            return;
        }

        if ( apply_filters( 'molongui_contributors/theme_integration', true ) )
        {
            $theme = wp_get_theme();

            $found = array_intersect( array( $theme->name, $theme->parent_theme ), array_keys( $this->themes ) );

            if ( !empty( $found ) )
            {
                $file = strtolower( str_replace( array( ' ', '_' ), '-', ucwords( current( $found ) ) ) );
                $file = MOLONGUI_CONTRIBUTORS_DIR . 'includes/integrations/themes/' . $file . '.php';

                if ( file_exists( $file ) )
                {
                    require_once $file;
                    Debug::console_log( null, sprintf( "Loaded theme integration for %s", ucwords( current( $found ) ) ) );
                }
                else
                {
                    Debug::console_log( null, sprintf( "Theme integration for '%s' not found.", ucwords( $theme->name ) ) );
                }
            }
            else
            {
                Debug::console_log( null, sprintf( "Theme integration for '%s' not available.", ucwords( $theme->name ) ) );
            }
        }
    }
    public function plugins_integration()
    {
        if ( apply_filters( 'molongui_contributors/plugin_integration', true ) )
        {
            foreach ( $this->plugins as $plugin => $include )
            {
                if ( $include )
                {
                    require_once MOLONGUI_CONTRIBUTORS_DIR . 'includes/integrations/plugins/' . $plugin . '.php';
                    Debug::console_log( null, sprintf( "Loaded plugin integration for %s", ucwords( $plugin ) ) );
                }
            }
        }
    }
    public static function current_theme()
    {
        $theme = wp_get_theme();
        $themes = self::get_themes();

        $found = array_intersect( array( $theme->name, $theme->parent_theme ), array_keys( $themes ) );
        $found = current( $found );

        if ( $found )
        {
            return $themes[$found];
        }
        else
        {
            return 'none';
        }
    }
    public static function get_themes()
    {
        return array
        (
            'Agama'               => 'hook',
            'Astra'               => 'full',
            'Avada'               => 'hook',
            'Betheme'             => 'js',
            'Blocksy'             => 'hook',
            'Blogus'              => 'js',
            'Botiga'              => 'none',
            'Bridge'              => 'js',
            'BuddyBoss Theme'     => 'js',
            'Colibri WP'          => 'none',
            'Customify'           => 'js',
            'Divi'                => 'func-override',
            'Enfold'              => 'js',
            'Extendable'          => 'hook', // WP Block-based theme
            'Extra'               => 'js',
            'Flatsome'            => 'js',
            'GeneratePress'       => 'full',
            'Go'                  => 'js',
            'Hestia'              => 'hook',
            'Inspiro'             => 'js',
            'JNews'               => 'hook',
            'JupiterX'            => 'full',
            'Kadence'             => 'hook',
            'Kubio'               => 'js',
            'Neve'                => 'hook',
            'News Portal'         => 'js',
            'Newspaper'           => 'js',
            'OceanWP'             => 'js',
            'OnePress'            => 'js',
            'Phlox'               => 'js',
            'PopularFX'           => 'func-override',
            'Salient'             => 'js',
            'Storefront'          => 'func-override', // js integration not working
            'Sydney'              => 'js',
            'The7'                => 'hook',
            'Total'               => 'js',
            'Twenty Ten'          => 'js',
            'Twenty Eleven'       => 'js',
            'Twenty Twelve'       => 'js',
            'Twenty Thirteen'     => 'js',
            'Twenty Fourteen'     => 'js',
            'Twenty Fifteen'      => 'js',
            'Twenty Sixteen'      => 'js',
            'Twenty Seventeen'    => 'func-override',
            'Twenty Nineteen'     => 'js',
            'Twenty Twenty'       => 'js',
            'Twenty Twenty-One'   => 'func-override',
            'Twenty Twenty-Two'   => 'hook', // WP Block-based theme
            'Twenty Twenty-Three' => 'hook', // WP Block-based theme
            'Twenty Twenty-Four'  => 'hook', // WP Block-based theme
            'Uncode'              => 'js',
            'YITH Wonder'         => 'hook', // WP Block-based theme
            'Zakra'               => 'js',
        );
    }
    public static function get_plugins()
    {
        return array
        (
            'gutenberg'   => true,
            'elementor'   => is_plugin_active( 'elementor/elementor.php' ),
        );
    }

} // class
new Integrations();