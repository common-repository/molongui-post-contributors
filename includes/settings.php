<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Utils\Plugin;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Settings extends \Molongui\Contributors\Common\Modules\Settings
{
    public function __construct()
    {
        parent::__construct();

        add_filter( 'molongui_contributors/default_options', array( __CLASS__, 'set_defaults' ) );
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
        add_filter( 'molongui_contributors/plugin_settings', array( __CLASS__, 'define_plugin_settings' ) );
        add_filter( 'molongui_contributors/validate_options', array( __CLASS__, 'validate_freemium_options' ), 10, 2 );
        add_filter( 'molongui_contributors/validate_options', array( $this, 'validate_options' ), 10, 2 );
        add_filter( 'molongui_contributors/sanitize_option', array( $this, 'custom_sanitization' ), 10, 3 );

        add_action( 'admin_init', array( $this, 'use_cdn' ), 0 );
        add_action( 'init', array( $this, 'use_cdn' ), 0 );
        add_filter( 'molongui_contributors/options/script', function( $file )
        {
            return MOLONGUI_CONTRIBUTORS_FOLDER . '/assets/js/options.2e65.min.js';
        }, 10, 1 );
        add_action( 'molongui_contributors/options/enqueue_required_deps', function()
        {
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );
        });
        add_filter( 'molongui_contributors/options/script_params', function( $fw_params )
        {
            $params = apply_filters( "molongui_contributors/options/params", array
            (
                'is_premium' => false,
            ));

            return $params + $fw_params;
        }, 10, 1 );
    }
    public static function set_defaults( $fw_options )
    {
        return array_merge( $fw_options, array
        (

            'post_types' => 'post',
            'add_to_content'    => true,
            'layout'            => 1,
            'show_author'       => true,
            'link_author'       => true,
            'show_contributors' => true,
            'link_contributor'  => true,
            'show_publish_date' => false,
            'show_update_date'  => false,
            'show_categories'   => false,
            'show_tags'         => false,
            'show_comment_link' => false,
            'alignment'         => 'start',
            'text_color'        => '#666',
            'author_text_color' => '#000',
            'highlight_author'  => true,
            'byline_small'      => false,
            'by'           => __( "Written by", 'molongui-post-contributors' ),
            'published_on' => __( "Published on", 'molongui-post-contributors' ),
            'updated_on'   => __( "Updated on", 'molongui-post-contributors' ),
            'in'           => _x( "In", 'A string that is output before one or more categories', 'molongui-post-contributors' ),
            'separator'    => '&#xFF5C;',
            'post_template_override' => false,
            'post_template'          => 1,
            'post_sidebar'           => 'right',
            'post_title'      => true,
            'post_thumbnail'  => true,
            'post_categories' => false,
            'post_tags'       => false,
            'post_share'      => true,
            'post_related'    => false,
            'post_navigation' => false,
            'post_comments'   => true,
            'content-wrap-column-gap' => 3,
            'content-wrap-padding'    => 3,
            'content-area-max-width'  => 800,
            'post-wrap-row-gap'       => 2,
            'post-wrap-padding'       => 0,
        ));
    }
    public function add_menu_item()
    {
        $position = 2;

        add_options_page
        (
            MOLONGUI_CONTRIBUTORS_TITLE . ' ' . __( 'Settings' ),
            trim( str_replace( 'Molongui ', '', MOLONGUI_CONTRIBUTORS_TITLE ) ),
            'manage_options',
            'molongui-post-contributors',
            array( __CLASS__, 'render' ),
            $position
        );
    }
    public static function define_plugin_settings()
    {
        $is_pro  = Plugin::has_pro();
        $options = array();
        if ( apply_filters( 'molongui_contributors/options/show_get_started_tab', true ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'get_started',
                'name'    => __( "Getting Started", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                /*! // translators: %s: The plugin name. */
                'label'   => sprintf( __( "Welcome to %s", 'molongui-post-contributors' ), MOLONGUI_CONTRIBUTORS_TITLE ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'first_steps_header',
                'label'   => __( "First Steps", 'molongui-post-contributors' ),
                'button'  => array(),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'button',
                'class'   => 'is-compact',
                'default' => '',
                /*! // translators: %s: The plugin name. */
                'label'   => sprintf( __( "%sGo edit a post%sFind the panel labeled as %sContributors%s%sStart typing the name of the contributor you want to add and pick your selection%sSelect how they contributed to the post%sSave changes and that should be it!%sIf you need to add more contributor roles, switch to the Contributors tab or click %s.", 'molongui-post-contributors' ),
                    '<ol><li>', '</li><li>', '<code>', '</code>', '</li><li>', '</li><li>', '</li><li>', '</li></ol>', '<a href="'.admin_url( 'edit-tags.php?taxonomy=contributor_role' ).'" target="_blank">here</a>' ),
                'button'  => array
                (
                    'display'  => true,
                    'type'     => 'link',
                    'href'     => admin_url( 'edit.php' ),
                    'id'       => 'edit_posts',
                    'label'    => __( "Posts", 'molongui-post-contributors' ),
                    'title'    => __( "View All Posts", 'molongui-post-contributors' ),
                    'class'    => 'same-width',
                    'disabled' => false,
                ),
            );
            $getting_started_ads[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'pro_ads_group_header',
                'label'   => __( "Need more features?", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => false,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $getting_started_ads[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'multiple_contributors_ad',
                'title'   => __( "Need to add multiple contributors to your post?", 'molongui-post-contributors' ),
                'desc'    => __( "If you need to add more than one contributor to a piece of work, try the Pro version of this plugin.", 'molongui-post-contributors' ),
                'label'   => '',
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $getting_started_ads[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'guest_contributors_ad',
                'title'   => __( "Would you like to allow contributors without requiring them to have a user account?", 'molongui-post-contributors' ),
                'desc'    => __( "Do you have one-time contributors that do not require an account in your site? Go with Guest Contributors!", 'molongui-post-contributors' ),
                'label'   => '',
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/getting_started_ads', $getting_started_ads ) );
            $options[] = array
            (
                'display' => !is_plugin_active( 'molongui-authorship/molongui-authorship.php' ),
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'molongui_authorship_ad_group_header',
                'label'   => __( "Molongui Post Authors", 'molongui-post-contributors' ),
                'button'  => array(),
            );
            $options[] = array
            (
                'display' => !is_plugin_active( 'molongui-authorship/molongui-authorship.php' ),
                'deps'    => '',
                'search'  => '',
                'type'    => 'button',
                'class'   => 'is-compact',
                'default' => '',
                'label'   => __( "Need to add multiple authors to your posts? Or display an author box for them? Try the Molongui Autorship plugin", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'display'  => current_user_can( 'activate_plugins' ),
                    'type'     => 'link',
                    'href'     => wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=molongui-authorship' ), 'install-plugin_molongui-authorship' ),
                    'id'       => 'install_authorship',
                    'label'    => __( "Install", 'molongui-post-contributors' ),
                    'title'    => __( "Install Molongui Authorship Now", 'molongui-post-contributors' ),
                    'class'    => 'same-width',
                    'disabled' => false,
                ),
            );
        }
        if ( apply_filters( 'molongui_contributors/options/show_contributors_tab', true ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'contributors',
                'name'    => __( "Contributors", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                'label'   => __( "Customize contributor roles adding those you need.", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'bylines_contributor_roles_group_header',
                'label'   => __( "Contributor Roles", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'link',
                'class'   => '',
                'default' => '',
                'id'      => 'contributor_roles_link',
                'title'   => '',
                'desc'    => '',
                'help'    => __( "Click to open a new screen where you can add, edit and remove different roles for your contributors.", 'molongui-post-contributors' ),
                'label'   => __( "Manage contributor roles", 'molongui-post-contributors' ),
                'href'    => admin_url( 'edit-tags.php?taxonomy=contributor_role' ), //admin_url( 'edit-tags.php?taxonomy=contributor_role&post_type=contributor' ),
                'target'  => '_self',
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'bylines_custom_post_types_group_header',
                'label'   => __( "Custom Post Type", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $custom_post_type[] = array
            (
                'display'  => true,
                'advanced' => false,
                'type'     => 'dropdown',
                'atts'     => array
                (
                    'search' => true,
                    'multi'  => true,
                ),
                'deps'     => '',
                'search'   => '',
                'id'       => 'post_types',
                'default'  => 'post',
                'class'    => '',
                'title'    => '',
                'desc'     => __( "Contributors can be added to posts. If you need to add contributors to custom post types, consider upgrading to Pro.", 'molongui-post-contributors-pro' ),
                'help'     => '',
                'label'    => '',
                'options'  => self::registered_post_types(),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/custom_post_type', $custom_post_type ) );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'bylines_guest_contributors_group_header',
                'label'   => __( "Guest Contributors", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => false,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $guest_contributors[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'guest_contributors_ad',
                'title'   => __( "Prevent unnecessary access to your Dashboard", 'molongui-post-contributors' ),
                'desc'    => __( "Do you have one-time contributors that do not require an account in your site? Go with Guest Contributors!", 'molongui-post-contributors' ),
                'label'   => '',
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/guest_contributors', $guest_contributors ) );

        }
        if ( apply_filters( 'molongui_contributors/options/show_byline_tab', true ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'byline',
                'name'    => __( "Byline", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                'label'   => __( "Personalize how the byline in your posts is displayed.", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => false,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'byline_preview_group_header',
                'label'   => __( "Live Preview", 'molongui-post-contributors' ),
                'buttons' => array(),
            );
            $options[] = array
            (
                'display'  => false,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'callback',
                'class'    => '',
                'default'  => '',
                'id'       => 'byline_preview',
                'title'    => '',
                'desc'     => __( "A byline is a line of text that tells readers who has written a piece of content. The byline in your posts will look like this. You can customize them playing around with the settings below.", 'molongui-post-contributors' ),
                'help'     => '',
                'link'     => '',
                'callback' => array( Post::class, 'get_the_byline_preview' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'bylines_display_group_header',
                'label'   => __( "Display", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => true,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'add_to_content',
                'title'    => '',
                'desc'     => '',
                /*! // translators: %1$s: <b>. %2$s: </b>. %3$s: <br><br>. %4$s: <b>. %5$s: </b>. */
                'help'     => sprintf( __( "By enabling this option, the post byline will %1\$sautomatically%2\$s be added to the top of your content.%3\$sIf you are using a theme builder like Elementor or Divi, you should rather use the %4\$s[molongui_post_meta]%5\$s shortcode.", 'molongui-post-contributors' ), '<b>', '</b>', '<br><br>', '<b>', '</b>'  ),
                'label'    => __( "Add the post byline to the top of your post content", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'inline-dropdown',
                'class'   => '',
                'default' => '1',
                'id'      => 'layout',
                'title'   => '',
                'desc'    => '',
                'help'    => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'   => sprintf( __( "To display the post bylines use the %s", 'molongui-post-contributors' ),  '{input}' ),
                'options' => array
                (
                    '1' => array
                    (
                        'icon'  => '',
                        'label' => __( "layout 1", 'molongui-post-contributors' ),
                    ),
                    '2' => array
                    (
                        'icon'  => '',
                        'label' => __( "layout 2", 'molongui-post-contributors' ),
                    ),
                    '3' => array
                    (
                        'icon'  => '',
                        'label' => __( "layout 3", 'molongui-post-contributors' ),
                    ),
                    '4' => array
                    (
                        'icon'     => '',
                        'label'    => __( "layout 4", 'molongui-post-contributors' ),
                    ),
                    '5' => array
                    (
                        'icon'     => '',
                        'label'    => __( "layout 5", 'molongui-post-contributors' ),
                    ),
                    '6' => array
                    (
                        'icon'     => '',
                        'label'    => __( "layout 6", 'molongui-post-contributors' ),
                        'disabled' => !$is_pro,
                    ),
                    '7' => array
                    (
                        'icon'     => '',
                        'label'    => __( "layout 7", 'molongui-post-contributors' ),
                        'disabled' => !$is_pro,
                    ),
                ),
            );
            $options[] = array
            (
                'display'     => true,
                'advanced'    => true,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-text',
                'placeholder' => '#byline, .post-byline',
                'default'     => '',
                'class'       => 'inline',
                'id'          => 'hide_default_byline',
                'title'       => '',
                'desc'        => '',
                'help'        => array
                (
                    /*! // translators: %1$s: <p>. %2$s: The plugin name. %3$s: </p>. %4$s: <p>. %5$s: </p>. */
                    'text'    => sprintf( __( "%1\$sMost themes display a byline for your posts, but they do not support the bylines displayed by %2\$s.%3\$s %4\$sIf your theme doesn't offer you the option to hide their byline, you can use this option to provide the CSS ID or class of their byline and we will make it go away.%5\$s", 'molongui-post-contributors' ), '<p>', MOLONGUI_CONTRIBUTORS_TITLE, '</p>', '<p>', '</p>' ),
                    'link'    => 'https://www.molongui.com/help/docs/molongui-contributors/',
                ),
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'       => sprintf( __( "Hide default theme's byline whose CSS ID or class is: %s", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'bylines_content_group_header',
                'label'   => __( "Content", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'show_author',
                'title'    => '',
                'desc'     => '',
                /*! // translators: %s: <br><br>. */
                'help'     => sprintf( __( "By enabling this option, the post bylines will include the post author.%sIf enabled, you may want to hide your default theme's byline. Some themes offer the option to hide post bylines, while others do not. If your theme doesn't provide this option, you can use the setting below to hide them.", 'molongui-post-contributors' ), '<br><br>' ),
                'label'    => __( "Show the post author", 'molongui-post-contributors' ),
            );
            $show_author_avatar[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'show_author_avatar_ad',
                'title'   => '',
                'desc'    => '',
                'label'   => __( "Show the author avatar", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/show_author_avatar', $show_author_avatar ) );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'link_author',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the author name will link to the author page.", 'molongui-post-contributors' ) ),
                'label'    => __( "Make the author name link to their author page", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'show_contributors',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post bylines will include the post contributors.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the post contributors", 'molongui-post-contributors' ),
            );
            $show_contributor_avatar[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'show_contributor_avatar_ad',
                'title'   => '',
                'desc'    => '',
                'label'   => __( "Show the contributors avatar", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/show_contributor_avatar', $show_contributor_avatar ) );
            $show_contributor_email[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'show_contributor_email_link_ad',
                'title'   => '',
                'desc'    => '',
                'label'   => __( "Show an email icon after the contributor name", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/show_contributor_email_link', $show_contributor_email ) );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'link_contributor',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the contributor name will link to the author page.", 'molongui-post-contributors' ) ),
                'label'    => __( "Make the contributor name link to their author page", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'show_publish_date',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post bylines will include the post contributors.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the published date", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'show_update_date',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post bylines will include the post contributors.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the latest update date", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'show_categories',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post bylines will include the post contributors.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the post categories", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'show_tags',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post bylines will include the post contributors.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the post tags", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'show_comment_link',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post bylines will include the post contributors.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show a link to the comments section", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'bylines_styling_group_header',
                'label'   => __( "Styling", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'inline-dropdown',
                'class'   => '',
                'default' => 'start',
                'id'      => 'alignment',
                'title'   => '',
                'desc'    => '',
                'help'    => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'   => sprintf( __( "Align the byline to the %s", 'molongui-post-contributors' ),  '{input}' ),
                'options' => array
                (
                    'start' => array
                    (
                        'icon'  => '',
                        'label' => __( "left", 'molongui-post-contributors' ),
                    ),
                    'center' => array
                    (
                        'icon'  => '',
                        'label' => __( "center", 'molongui-post-contributors' ),
                    ),
                    'end' => array
                    (
                        'icon'  => '',
                        'label' => __( "right", 'molongui-post-contributors' ),
                    ),
                ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'color',
                'default'  => '#666',
                'class'    => '',
                'id'       => 'text_color',
                'title'    => '',
                'desc'     => __( "Byline text color", 'molongui-post-contributors' ),
                'help'     => '',
                'label'    => '',
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'color',
                'default'  => '#000',
                'class'    => '',
                'id'       => 'author_text_color',
                'title'    => '',
                'desc'     => __( "Author text color", 'molongui-post-contributors' ),
                'help'     => '',
                'label'    => '',
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'highlight_author',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the author part of the byline will use a bold font.", 'molongui-post-contributors' ) ),
                'label'    => __( "Highlight the author in the byline using bold", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'byline_small',
                'title'    => '',
                'desc'     => '',
                /*! // translators: %1$s: <p>. %2$s: </p>. %3$s: <p>. %4$s: <strong>. %5$s: </strong>. %6$s: <strong>. %7$s: </strong>. %8$s: </p>. %9$s: <pre>. %10$s: </pre> */
                'help'     => sprintf( __( "%1\$sBy enabling this option, all the bylines elements but the post author are displayed smaller.%2\$s%3\$sTo specify a particular font size, you can use the %4\$sCustom CSS%5\$s setting in the %6\$sAdvanced%7\$s section by entering a rule like this:%8\$s%9\$s.molongui-post-byline .molongui-post-byline__column:not(.molongui-post-author) { font-size:10px; }%10\$s", 'molongui-post-contributors' ), '<p>', '</p>', '<p>', '<strong>', '</strong>', '<strong>', '</strong>', '</p>', '<pre>', '</pre>' ),
                'label'    => __( "Make byline elements, other than the author, smaller", 'molongui-post-contributors' ),
            );
            $role_badge[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'role_badge_ad',
                'title'   => '',
                'desc'    => '',
                'label'   => __( "Display the contributor role as a badge", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/role_badge', $role_badge ) );
            $role_icon[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'role_icon_ad',
                'title'   => '',
                'desc'    => '',
                'label'   => __( "Display a check icon before the contributor role", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Learn More", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade to Pro and unlock this premium feature", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/role_icon', $role_icon ) );
            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/additional_styling_options', array() ) );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'bylines_strings_group_header',
                'label'   => __( "Strings", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display'     => true,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-text',
                'placeholder' => '',
                'default'     => __( "Written by", 'molongui-post-contributors' ),
                'class'       => 'inline',
                'id'          => 'by',
                'title'       => '',
                'desc'        => '',
                'help'        => __( "Usually the 'By' or the 'Written by' text is used, but you can enter any text you like.", 'molongui-post-contributors' ),
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'       => sprintf( __( "The text to add before the author name: %s", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'type'     => 'link',
                'deps'     => '',
                'search'   => '',
                'id'       => 'edit_leading_text',
                'default'  => '',
                'class'    => '',
                'title'    => '',
                'desc'     => '',
                'help'     => __( "Click to open the contributor roles editor", 'molongui-post-contributors' ),
                'label'    => __( "The prefix text for each contributor varies based on their role. Click here to edit contributor roles.", 'molongui-post-contributors' ),
                'href'     => admin_url( 'edit-tags.php?taxonomy=contributor_role' ),
                'target'   => '_self',
            );
            $options[] = array
            (
                'display'     => true,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-text',
                'placeholder' => '',
                'default'     => __( "Published on", 'molongui-post-contributors' ),
                'class'       => 'inline',
                'id'          => 'published_on',
                'title'       => '',
                'desc'        => '',
                'help'        => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'       => sprintf( __( "The text to add before the publish date: %s", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'     => true,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-text',
                'placeholder' => '',
                'default'     => __( "Updated on", 'molongui-post-contributors' ),
                'class'       => 'inline',
                'id'          => 'updated_on',
                'title'       => '',
                'desc'        => '',
                'help'        => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'       => sprintf( __( "The text to add before the update date: %s", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'     => true,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-text',
                'placeholder' => '',
                'default'     => _x( "In", 'A string that is output before one or more categories', 'molongui-post-contributors' ),
                'class'       => 'inline',
                'id'          => 'in',
                'title'       => '',
                'desc'        => '',
                'help'        => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'       => sprintf( __( "The text to add before the post categories: %s", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'     => true,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-text',
                'placeholder' => '',
                'default'     => '&#xFF5C;',
                'class'       => 'inline',
                'id'          => 'separator',
                'title'       => '',
                'desc'        => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'       => sprintf( __( "The character or string to use between byline items: %s", 'molongui-post-contributors' ), '{input}' ),
                'help'        => array
                (
                    /*! // translators: %1$s: <p>. %2$s: </p>. %3$s: <p>. %4$s: </p>. %5$s: <p>. %6$s: </p>. */
                    'text' => sprintf( __( "%1\$sSome byline items might be displayed on the same line, so you may want to add a separator between them for clarity.%2\$s%3\$sYou can enter any character or string without any limitation in length.%4\$s%5\$sBy default, the fullwidth vertical line symbol (&#xFF5C;) is used.%6\$s", 'molongui-post-contributors' ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>' ),
                    'link' => array
                    (
                        'label'  => __( "HTML symbols", 'molongui-post-contributors' ),
                        'url'    => 'https://www.toptal.com/designers/htmlarrows/symbols/',
                        'target' => '_blank',
                    ),
                ),
            );
        }
        if ( apply_filters( 'molongui_contributors/options/show_posts_tab', true ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'posts',
                'name'    => __( "Posts", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                /*! // translators: %s: The plugin name. */
                'label'   => sprintf( __( "Ready-to-use templates for your posts, fully compatible with %s.", 'molongui-post-contributors' ), MOLONGUI_CONTRIBUTORS_TITLE ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'post_template_group_header',
                'label'   => __( "Template", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => false,
                'id'       => 'post_template_override',
                'title'    => '',
                'desc'     => '',
                'help'     => __( "By enabling this option, your current post template will be replaced by one provided by this plugin. You have several templates to choose from. If replacing your current post template is not an option for you, you can display post bylines following different approaches, like configuring the plugin to automatically add them to the top of your post content or adding a template tag to your current post template. You also have the option to use a shortcode if you are using a theme builder like Elementor or Divi.", 'molongui-post-contributors' ),
                'label'    => __( "Override current post template with one that ensures post bylines are displayed properly.", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => 'post_template_override',
                'search'  => '',
                'type'    => 'inline-dropdown',
                'class'   => '',
                'default' => '1',
                'id'      => 'post_template',
                'title'   => '',
                'desc'    => '',
                'help'    => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'   => sprintf( __( "Use custom %s for posts", 'molongui-post-contributors' ),  '{input}' ),
                'options' => array
                (
                    '1' => array
                    (
                        'icon'  => '',
                        'label' => __( "template #1", 'molongui-post-contributors' ),
                    ),
                    '2' => array
                    (
                        'icon'  => '',
                        'label' => __( "template #2", 'molongui-post-contributors' ),
                    ),
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'inline-dropdown',
                'class'   => '',
                'default' => 'right',
                'id'      => 'post_sidebar',
                'title'   => '',
                'desc'    => '',
                'help'    => '',
                /*! // translators: %s: The tag to be replaced with the actual input. */
                'label'   => sprintf( __( "Display the post sidebar %s", 'molongui-post-contributors' ),  '{input}' ),
                'options' => array
                (
                    'none' => array
                    (
                        'icon'  => '',
                        'label' => __( "nowhere", 'molongui-post-contributors' ),
                    ),
                    'left' => array
                    (
                        'icon'  => '',
                        'label' => __( "on the left", 'molongui-post-contributors' ),
                    ),
                    'right' => array
                    (
                        'icon'  => '',
                        'label' => __( "on the right", 'molongui-post-contributors' ),
                    ),
                ),
            );
            $options[] = array
            (
                'display' => false,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'post_types_group_header',
                'label'   => __( "Post Types", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => false,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display' => false,
                'deps'    => '',
                'search'  => '',
                'type'    => 'dropdown',
                'atts'    => array
                (
                    'search' => true,
                    'multi'  => true,
                ),
                'class'   => '',
                'default' => '',
                'id'      => 'dropdown_option_id', // TODO: Update this id accordingly
                'title'   => '',
                /*! // translators: %1$s: <strong>. %2$s: </strong>. %3$s: <strong>. %4$s: </strong>. */
                'desc'    => $is_pro ? esc_html__( "Select those post types where plugin features will be enabled on.", 'molongui-post-contributors' ) : sprintf( __( "Select those post types where plugin features will be enabled on. By default, they are enabled on %sPosts%s and %sPages%s.", 'molongui-post-contributors' ), '<strong>', '</strong>', '<strong>', '</strong>' ),
                'help'    => array
                (
                    /*! // translators: %1$s: <p>. %2$s: </p>. %3$s: <p>. %4$s: </p>. %5$s: <p>. %6$s: </p>. */
                    'text' => sprintf( __( "%sThere are a ton of social networks. To avoid clutter, select those you want to enable.%s %sYou can select as many as you wish. And you can filter displayed list by typing the name of the network you are looking for.%s %sAnd if you find one missing, you can request us to include it.%s %s" ), '<p>', '</p>', '<p>', '</p>', '<p>', '</p>', ( !$is_pro ? sprintf( __( "%sDisabled options are only available in %sMolongui Bylines Pro%s%s", 'molongui-post-contributors' ), '<p>', '<a href="'.MOLONGUI_CONTRIBUTORS_WEB.'" target="_blank">', '</a>', '</p>' ) : '' ) ),
                    'link' => 'https://www.molongui.com/docs/molongui-boilerplate/author-box/social-networks/',
                ),
                'label'   => '',
                'options' => array(), // TODO: Replace with a function
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'post_content_group_header',
                'label'   => __( "Content", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_title',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post title will be displayed.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the post title", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_thumbnail',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post featured image will be displayed.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the post featured image", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_categories',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post categories will be displayed.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show post categories", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_tags',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post tags will be displayed.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show post tags", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_share',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the 'share this post' section will be displayed.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show sharing options", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_navigation',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post pages link navigation for previous and next pages will be displayed.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the post navigation", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => false,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_related',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the related posts section will be displayed.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show related posts", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display'  => true,
                'advanced' => false,
                'deps'     => '',
                'search'   => '',
                'type'     => 'toggle',
                'class'    => '',
                'default'  => true,
                'id'       => 'post_comments',
                'title'    => '',
                'desc'     => '',
                'help'     => sprintf( __( "By enabling this option, the post comments will be displayed —only if comments are open or the post already has any comment.", 'molongui-post-contributors' ) ),
                'label'    => __( "Show the post comments", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'post_styling_group_header',
                'label'   => __( "Styling", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display'     => true,
                'advanced'    => false,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-number',
                'default'     => 3,
                'placeholder' => '3',
                'min'         => 0,
                'max'         => '',
                'step'        => 1,
                'class'       => '',
                'id'          => 'content-wrap-column-gap',
                'title'       => '',
                'desc'        => '',
                'help'        => sprintf( __( "%sThe size of the gap (gutter) between the post content and the sidebar (if displayed).%s", 'molongui-post-contributors' ), '<p>', '</p>' ),
                'label'       => sprintf( __( "Content wrap column gap: %s em", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'     => true,
                'advanced'    => false,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-number',
                'default'     => 3,
                'placeholder' => '3',
                'min'         => 0,
                'max'         => '',
                'step'        => 1,
                'class'       => '',
                'id'          => 'content-wrap-padding',
                'title'       => '',
                'desc'        => '',
                'help'        => sprintf( __( "%sThe space around the content wrapper.%s", 'molongui-post-contributors' ), '<p>', '</p>' ),
                'label'       => sprintf( __( "Content wrap padding: %s em", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'     => true,
                'advanced'    => false,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-number',
                'default'     => 800,
                'placeholder' => '800',
                'min'         => 600,
                'max'         => '',
                'step'        => 1,
                'class'       => '',
                'id'          => 'content-area-max-width',
                'title'       => '',
                'desc'        => '',
                'help'        => sprintf( __( "%sThe maximum width of the content area element.%s", 'molongui-post-contributors' ), '<p>', '</p>' ),
                'label'       => sprintf( __( "Content area max width: %s px", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'     => true,
                'advanced'    => false,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-number',
                'default'     => 2,
                'placeholder' => '2',
                'min'         => 0,
                'max'         => '',
                'step'        => 1,
                'class'       => '',
                'id'          => 'post-wrap-row-gap',
                'title'       => '',
                'desc'        => '',
                'help'        => sprintf( __( "%sThe size of the gap (gutter) between post content's items (Title, featured image, body, sharing buttons...).%s", 'molongui-post-contributors' ), '<p>', '</p>' ),
                'label'       => sprintf( __( "Post wrap row gap: %s em", 'molongui-post-contributors' ), '{input}' ),
            );
            $options[] = array
            (
                'display'     => true,
                'advanced'    => false,
                'deps'        => '',
                'search'      => '',
                'type'        => 'inline-number',
                'default'     => 0,
                'placeholder' => '0',
                'min'         => 0,
                'max'         => '',
                'step'        => 1,
                'class'       => '',
                'id'          => 'post-wrap-padding',
                'title'       => '',
                'desc'        => '',
                'help'        => sprintf( __( "%sThe space around the post wrapper.%s", 'molongui-post-contributors' ), '<p>', '</p>' ),
                'label'       => sprintf( __( "Post wrap padding: %s em", 'molongui-post-contributors' ), '{input}' ),
            );

        }
        if ( apply_filters( 'molongui_contributors/options/show_compatibility_tab', false ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'compat',
                'name'    => __( 'Compatibility', 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                'label'   => __( "Most of the issues you might have with the plugin can be easily fixed with these settings.", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'compat_themes_header',
                'label'   => __( "Themes", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'toggle',
                'class'   => '',
                'default' => true,
                'id'      => 'enable_theme_compat',
                'title'   => '',
                /*! // translators: %1$s: The opening a tag. %2$s: The closing a tag. */
                'desc'    => sprintf( __( "Molongui Bylines plugin works great with just about every theme, especially with the most popular. Some require tailored functions to achieve full compatibility, so you need to enable this setting. If you experience issues with the information displayed on your bylines or anything related to your authors information, make sure this is enabled. If it is and the issue persists, please %sopen a support ticket%s with us so we can assist.", 'molongui-post-contributors' ), '<a href="https://www.molongui.com/support/#open-ticket" target="_blank">', '</a>' ),
                /*! // translators: %1$s: <p>. %2$s: </p>. %3$s: <p>. %4$s: </p>. */
                'help'    => sprintf( __( "%sSome themes require this setting to be enabled in order to work properly.%s %sIn case of doubt, keep it enabled.%s", 'molongui-post-contributors' ), '<p>', '</p>', '<p>', '</p>'),
                'label'   => __( "Enable theme compatibility", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'link',
                'class'   => '',
                'default' => '',
                'id'      => '',
                'title'   => '',
                'desc'    => '',
                'help'    => __( "Click to open a Support Ticket", 'molongui-post-contributors' ),
                'label'   => __( "Issue persists? Report it", 'molongui-post-contributors' ),
                'href'    => molongui_get_support(),
                'target'  => '_blank',
            );
            $options[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'deps'    => '',
                'search'  => '',
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'theme_premium_support',
                'title'   => __( "Need to make your theme work with Molongui Bylines ASAP?", 'molongui-post-contributors' ),
                'desc'    => __( "Get Premium support to make your theme run smoothly with Molongui Bylines.", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB.'/pricing/',
                    'target' => '_blank',
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'compat_plugins_header',
                'label'   => __( "Plugins", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'toggle',
                'class'   => '',
                'default' => true,
                'id'      => 'enable_plugin_compat',
                'title'   => '',
                /*! // translators: %1$s: Opening a tag. %2$s: Closing a tag. */
                'desc'    => sprintf( __( "Some third plugins require tailored functions to be compatible with Molongui Bylines, so you need to enable this setting. If you experience issues related to your authors information, make sure this is enabled. If it is and the issue persists, please %sopen a support ticket%s with us so we can assist.", 'molongui-post-contributors' ), '<a href="https://www.molongui.com/support/#open-ticket" target="_blank">', '</a>' ),
                /*! // translators: %1$s: <p>. %2$s: </p>. %3$s: <p>. %4$s: </p>. */
                'help'    => sprintf( __( "%sSome plugins require this setting to be enabled in order to work properly.%s %sIn case of doubt, keep it enabled.%s", 'molongui-post-contributors' ), '<p>', '</p>', '<p>', '</p>'),
                'label'   => __( "Enable plugin compatibility", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'link',
                'class'   => '',
                'default' => '',
                'id'      => '',
                'title'   => '',
                'desc'    => '',
                'help'    => __( "Click to open a Support Ticket", 'molongui-post-contributors' ),
                'label'   => __( "Issue persists? Report it", 'molongui-post-contributors' ),
                'href'    => molongui_get_support(),
                'target'  => '_blank',
            );
            $options[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'deps'    => '',
                'search'  => '',
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'plugin_premium_support',
                'title'   => __( "Want to benefit from Priority?", 'molongui-post-contributors' ),
                'desc'    => __( "Get elevated levels of support to help you keep your favourite plugins running smoothly together.", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB.'/pricing/',
                    'target' => '_blank',
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => 'enable_author_boxes',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'compat_cdn_header',
                'label'   => __( "CDN", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => 'enable_author_boxes',
                'search'  => '',
                'type'    => 'toggle',
                'class'   => '',
                'default' => false,
                'id'      => 'enable_cdn_compat',
                'title'   => '',
                /*! // translators: %1$s: Opening a tag. %2$s: Closing a tag. */
                'desc'    => sprintf( __( "Messed up author box layout? And you using a CDN? Enable this setting and clear every cache you might have, including your CDN's. If the issue persists, please %sopen a support ticket%s with us so we can assist.", 'molongui-post-contributors' ), '<a href="https://www.molongui.com/support/#open-ticket" target="_blank">', '</a>' ),
                'help'    => array
                (
                    /*! // translators: %1$s: <p><strong>. %2$s: </strong></p>. %3$s: <p>. %4$s: </p>. */
                    'text' => sprintf( __( "%sActivate this setting only if you are experiencing issues.%s %sWhen using a CDN to serve stylesheet files, author box layout might look messed up. Enabling this setting should fix that.%s", 'molongui-post-contributors' ), '<p><strong>', '</strong></p>', '<p>', '</p>' ),
                    'link' => 'https://www.molongui.com/docs/molongui-boilerplate/troubleshooting/the-author-box-layout-is-being-displayed-oddly/',
                ),
                'label'   => __( "Enable CDN compatibility to fix author box layout and make it display nicely.", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => 'enable_author_boxes',
                'search'  => '',
                'type'    => 'link',
                'class'   => '',
                'default' => '',
                'id'      => '',
                'title'   => '',
                'desc'    => '',
                'help'    => __( "Click to open a Support Ticket", 'molongui-post-contributors' ),
                'label'   => __( "Issue persists? Report it", 'molongui-post-contributors' ),
                'href'    => molongui_get_support(),
                'target'  => '_blank',
            );
            $options[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'deps'    => 'enable_author_boxes',
                'search'  => '',
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'cdn_premium_support',
                'title'   => __( "Need Premium Support?", 'molongui-post-contributors' ),
                'desc'    => __( "Paid users are given top priority in our support system, with replies to their support tickets taking precedence.", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB.'/pricing/',
                    'target' => '_blank',
                ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '', // This header has multiple dependencies, so it must be handled with tailor-made JS.
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'compat_rest_header',
                'label'   => __( "REST API", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $rest_api   = array();
            $rest_api[] = array
            (
                'display' => apply_filters( 'molongui_contributors/options/display/banners', true ),
                'deps'    => '', // This header has multiple dependencies, so it must be handled with tailor-made JS.
                'search'  => '',
                'type'    => 'banner',
                'class'   => '',
                'default' => '',
                'id'      => 'rest_api',
                'title'   => __( "Allow third-party applications to interact with your posts and authors via the WordPress REST API.", 'molongui-post-contributors' ),
                'desc'    => __( "Expose post co-authors and guest author object.", 'molongui-post-contributors' ),
                'button'  => array
                (
                    'label'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'title'  => __( "Upgrade", 'molongui-post-contributors' ),
                    'class'  => 'm-upgrade',
                    'href'   => MOLONGUI_CONTRIBUTORS_WEB,
                    'target' => '_blank',
                ),
            );

            $options = array_merge( $options, apply_filters( '_molongui_contributors/options/rest_api/markup', $rest_api ) );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'compat_misc_header',
                'label'   => __( "Misc", 'molongui-post-contributors' ),
                'buttons' => array
                (
                    'advanced' => array
                    (
                        'display'  => false,
                        'type'     => 'advanced',
                        'label'    => __( "Show Advanced", 'molongui-post-contributors' ),
                        'title'    => __( "Click to show advanced settings", 'molongui-post-contributors' ),
                        'class'    => 'm-advanced-options',
                        'disabled' => false,
                    ),
                    'save' => array
                    (
                        'display'  => true,
                        'type'     => 'save',
                        'label'    => __( "Save", 'molongui-post-contributors' ),
                        'title'    => __( "Save Settings", 'molongui-post-contributors' ),
                        'class'    => 'm-save-options',
                        'disabled' => true,
                    ),
                ),
            );
            $options[] = array
            (
                'display'     => true,
                'deps'        => '',
                'search'      => '',
                'type'        => 'text',
                'placeholder' => '#author-bio, .author-box-wrap',
                'default'     => '',
                'class'       => 'inline',
                'id'          => 'hide_elements',
                'title'       => '',
                'desc'        => '',
                'help'        => array
                (
                    /*! // translators: %1$s: <p>. %2$s: </p>. %3$s: <p>. %4$s: </p>. */
                    'text'    => sprintf( __( "%sMany themes add elements to your site without giving the option to disable them.%s %sNow you can hide unwanted author boxes, byline decorations or whatever.%s", 'molongui-post-contributors' ), '<p>', '</p>', '<p>', '</p>' ),
                    'link'    => 'https://www.molongui.com/docs/molongui-boilerplate/troubleshooting/the-author-box-shows-up-twice/',
                ),
                /*! // translators: %1$s: Opening a tag. %2$s: Closing a tag. */
                'label'       => sprintf( __( "Need to get rid of some elements you don't have the setting to? Provide a comma-separated list of CSS IDs and/or classes and Molongui Bylines will prevent them from being displayed on the front-end.", 'molongui-post-contributors' ), '<a href="https://www.molongui.com/docs/molongui-boilerplate/troubleshooting/the-author-box-shows-up-twice/" target="_blank">', '</a>' ),
            );
        }
        if ( apply_filters( 'molongui_contributors/options/show_shortcodes_tab', true ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'shortcodes',
                'name'    => __( "Shortcodes", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                'label'   => __( "Handy shortcodes that will make building your site a lot easier.", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'shortcodes_header',
                'label'   => __( "Shortcodes", 'molongui-post-contributors' ),
                'buttons' => array(),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'notice',
                'class'   => '',
                'default' => '',
                'id'      => 'shortcode_molongui_post_meta',
                'title'   => '[molongui_post_meta]',
                /*! // translators: %1$s: <a> tag open. %2$s: </a> tag close */
                'desc'    => sprintf( __( "Displays the post bylines anywhere you want. You can customize which post information to show and how it is displayed by using additional attributes. All styling settings can be overridden. %sLearn more%s.", 'molongui-post-contributors' ), '<a href="https://www.molongui.com/help/docs/" target="_blank">', '</a>' ),
                'help'    => '',
                'link'    => "https://www.molongui.com/help/docs/", // todo: Update the link when doc ready
            );
        }
        if ( apply_filters( 'molongui_contributors/options/show_tools_tab', true ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'tools',
                'name'    => __( 'Tools' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                'label'   => __( "Convenient tools to easily manage plugin data.", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'header',
                'class'   => '',
                'id'      => 'tools_bylines_wizard_header',
                'label'   => __( "Setup Wizard", 'molongui-post-contributors' ),
                'button'  => array(),
            );
            $options[] = array
            (
                'display' => true,
                'deps'    => '',
                'search'  => '',
                'type'    => 'button',
                'class'   => 'is-compact',
                'default' => '',
                /*! // translators: %s: The plugin name. */
                'label'   => sprintf( __( "We'll guide you through each step needed to get %s fully set up on your site.", 'molongui-post-contributors' ), MOLONGUI_CONTRIBUTORS_TITLE ),
                'button'  => array
                (
                    'display'  => true,
                    'type'     => 'link',
                    'href'     => admin_url( 'index.php?page=' . MOLONGUI_CONTRIBUTORS_NAME . '-setup-wizard' ),
                    'id'       => 'run_wizard',
                    'label'    => __( "Launch", 'molongui-post-contributors' ),
                    'title'    => __( "Launch Setup Wizard", 'molongui-post-contributors' ),
                    'class'    => 'same-width',
                    'disabled' => false,
                ),
            );
        }
        if ( apply_filters( 'molongui_contributors/options/show_advanced_tab', true ) )
        {
            $options[] = array
            (
                'display' => true,
                'type'    => 'section',
                'id'      => 'advanced',
                'name'    => __( "Advanced", 'molongui-post-contributors' ),
            );
            $options[] = array
            (
                'display' => true,
                'type'    => 'title',
                /*! // translators: %1$s: The plugin name. */
                'label'   => sprintf( __( "Make the most out of %s.", 'molongui-post-contributors' ), MOLONGUI_CONTRIBUTORS_TITLE ),
            );
        }

        return $options;
    }
    public static function validate_freemium_options( $options, $current )
    {
        $options['post_types'] = 'post';

        return $options;
    }
    public function validate_options( $options, $current )
    {

        return $options;
    }
    public function custom_sanitization( $sanitized_text_field, $key, $value )
    {
        switch ( $key )
        {
            case 'by':
            case 'published_on':
            case 'updated_on':
            case 'in':
            case 'separator':
                return wp_kses_post( $value );
                break;
        }

        return $sanitized_text_field;
    }
    public function use_cdn()
    {
        add_filter( 'molongui_contributors/assets/load_remote', function()
        {
            $options = self::get();
            return !empty( $options['assets_cdn'] );
        });
    }
    static public function registered_post_types()
    {
        $options = array();

        foreach( Post::get_post_types( 'all', 'objects', false ) as $post_type )
        {
            $options[] = array
            (
                'id'       => $post_type->name,
                'label'    => $post_type->labels->name,
                'disabled' => $post_type->name != 'post',
            );
        }

        return $options;
    }

} // class
new Settings();