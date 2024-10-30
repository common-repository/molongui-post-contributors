<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Utils\Assets;
use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Common\Modules\Settings;
use Molongui\Contributors\Common\Utils\Helpers;
use Molongui\Contributors\Common\Utils\WP;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Post extends \Molongui\Contributors\Common\Utils\Post
{
    const BYLINE_STYLESHEET     = '/assets/css/post-byline.d08b.min.css';
    const BYLINE_STYLESHEET_RTL = '/assets/css/post-byline-rtl.d08b.min.css';
    public function __construct()
    {
        add_action( 'add_meta_boxes', array( $this, 'add_contributors_metabox' ), 0, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_contributors_metabox_scripts' ) );
        add_filter( 'molongui_contributors/contributors_metabox_script_params', array( $this, 'add_contributors_metabox_script_params' ), 10 );
        add_action( 'save_post', array( $this, 'on_save_post' ), 10, 2 );
        add_action( 'init', array( $this, 'autoadd_bylines_to_content' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_byline_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_byline_styles' ) );
        add_filter( 'molongui_contributors/byline_extra_styles', array( $this, 'byline_extra_styles' ), 1 );
        add_filter( 'molongui_contributors/byline_extra_styles', array( $this, 'add_custom_styles' ) );
        add_filter( 'molongui_contributors/custom_css', array( $this, 'hide_theme_byline' ), 10 );
    }
    public function add_contributors_metabox( $post_type, $post )
    {
        /*!
         * FILTER HOOK
         *
         * Allows controlling whether the meta box is added to the edit-post screen.
         *
         * @param bool   Current user editor capabilities.
         * @param string Current post type.
         * @since 1.0.0
         */
        $editor_caps = apply_filters( 'molongui_contributors/editor_caps', current_user_can( 'edit_others_pages' ) or current_user_can( 'edit_others_posts' ), $post_type );
        if ( !$editor_caps )
        {
            return;
        }

        /*!
         * FILTER HOOK
         *
         * Allows controlling whether the contributors dropdown select is displayed.
         *
         * @param bool   True by default.
         * @param string Current post type.
         * @since 1.0.0
         */
        if ( self::is_post_type_enabled( $post_type ) and apply_filters( 'molongui_contributors/add_contributors_metabox', true, $post_type ) )
        {
            add_meta_box
            (
                'molongui-post-contributors-metabox'
                , __( "Contributors", 'molongui-post-contributors' )
                , array( $this, 'render_contributors_metabox' )
                , $post_type
                , 'side' //'normal'
                , 'high'
            );
        }
    }
    public function render_contributors_metabox( $post )
    {
        self::contributor_selector( $post );
    }
    public static function contributor_selector( $post = null, $screen = 'edit' )
    {
        include MOLONGUI_CONTRIBUTORS_DIR . 'views/admin/html-contributor-selector.php';
    }
    public function register_contributors_metabox_scripts()
    {
        $scope = 'contributors_metabox';

        /*!
         * FILTER HOOK
         *
         * Allows third-party to provide a custom file to load.
         *
         * @param string Relative path to the file. Do not include the WP_PLUGIN_DIR part.
         * @since 1.0.0
         */
        $file = apply_filters( 'molongui_contributors/contributors_metabox_scripts_file', MOLONGUI_CONTRIBUTORS_FOLDER . '/assets/js/edit-post.5731.min.js' );

        /*!
         * FILTER HOOK
         *
         * Allows third-party to require custom dependencies.
         *
         * @param array List of dependencies to load.
         * @since 1.0.0
         */
        $deps = apply_filters( 'molongui_contributors/contributors_metabox_scripts_deps', array( 'jquery', 'suggest' ) );
        if ( !empty( $deps ) )
        {
            add_filter( "molongui_contributors/{$scope}/inline_script", '__return_false' );
        }

        Assets::register_script( $file, $scope, $deps );
    }
    public static function enqueue_contributors_metabox_scripts()
    {
        /*!
         * FILTER HOOK
         *
         * Allows third-party to provide a custom file to load.
         *
         * @param string Relative path to the file. Do not include the WP_PLUGIN_DIR part.
         * @since 1.0.0
         */
        $file = apply_filters( 'molongui_contributors/contributors_metabox_scripts_file', MOLONGUI_CONTRIBUTORS_FOLDER . '/assets/js/edit-post.5731.min.js' );

        Assets::enqueue_script( $file, 'contributors_metabox', true );
    }
    public function add_contributors_metabox_script_params( $params )
    {
        $contributor_roles = array();
        foreach ( Contributor_Role::get_contributor_roles() as $contributor_role )
        {
            $contributor_roles[] = array( 'value' => $contributor_role->slug, 'text' => $contributor_role->name );
        }
        $ajax_suggest_link = add_query_arg( array
        (
            'action'    => 'contributors_ajax_suggest',
            'post_type' => rawurlencode( get_post_type() ),
        ), wp_nonce_url( 'admin-ajax.php', 'contributors-search' ) );

        return array
        (
            'tag_title'          => esc_html__( "Drag this contributor to reorder", 'molongui-post-contributors' ),
            'up_label'           => esc_html__( "Move up", 'molongui-post-contributors' ),
            'down_label'         => esc_html__( "Move down", 'molongui-post-contributors' ),
            'delete_label'       => esc_html__( "Remove", 'molongui-post-contributors' ),
            'confirm_delete'     => esc_html__( "Are you sure you want to remove this contributor from selection?", 'molongui-post-contributors' ),
            'as_label'           => _x( "as", 'Connector between contributor name and their role', 'molongui-post-contributors' ),
            'limit_reached_text' => __( "Currently, you can add only one contributor. To add multiple contributors and unlock additional premium features, consider upgrading to the Pro version of the plugin.", 'molongui-post-contributors' ),
            'contributor_roles'  => $contributor_roles,
            'ajax_suggest_link'  => $ajax_suggest_link,

            'debug_mode'         => Debug::is_enabled(),
        );
    }
    public function on_save_post( $post_id, $post )
    {
        if ( !self::can_save_post( $post_id ) )
        {
            return;
        }
        if ( !User::current_user_can_set_contributors() )
        {
            return;
        }
        if ( WP::verify_nonce( 'molongui_post_contributors' ) )
        {
            $contributors = (array) $_POST['molongui_post_contributors'];  // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $contributors = map_deep( $contributors, 'sanitize_title' ); // 'map_deep' because $contributors is a multidimensional array
            self::set_contributors( $post_id, $contributors );

            /*!
             * ACTION HOOK
             *
             * Carry out additional actions on save post.
             *
             * @param int     $post_id The ID of the post being saved.
             * @param WP_Post $post    The post object.
             * @since 1.0.0
             */
            do_action( 'molongui_contributors/save_post', $post_id, $post );
        }
    }
    public function autoadd_bylines_to_content()
    {
        $options = Settings::get();
        if ( empty( $options['add_to_content'] ) ) return;
        if ( !empty( $options['post_template_override'] ) ) return;

        /*!
         * FILTER HOOK
         *
         * Allows filtering the priority at which to hook the bylines markup.
         *
         * @param int Hook priority. Default 10.
         * @since 1.0.0
         */
        $priority = apply_filters( 'molongui_contributors/content_priority', 10 );

        add_filter( 'the_content', array( __CLASS__, 'add_bylines_to_content' ), $priority );
    }
    public static function register_byline_styles()
    {
        /*!
         * FILTER HOOK
         *
         * Allows loading a different stylesheet.
         *
         * @param string Relative path to the file. Do not include the WP_PLUGIN_DIR part.
         * @since 1.0.0
         * @since 1.4.0  Renamed from 'molongui_contributors/post_byline/styles'
         */
        $file = apply_filters( 'molongui_contributors/post_byline_styles', MOLONGUI_CONTRIBUTORS_FOLDER . ( is_rtl() ? self::BYLINE_STYLESHEET_RTL : self::BYLINE_STYLESHEET ) );

        Assets::register_style( $file, 'byline' );
    }
    public static function enqueue_byline_styles()
    {
        /*!
         * FILTER HOOK
         *
         * Allows loading a different stylesheet.
         *
         * @param string Relative path to the file. Do not include the WP_PLUGIN_DIR part.
         * @since 1.0.0
         * @since 1.4.0  Renamed from 'molongui_contributors/post_byline/styles'
         */
        $file = apply_filters( 'molongui_contributors/post_byline_styles', MOLONGUI_CONTRIBUTORS_FOLDER . ( is_rtl() ? self::BYLINE_STYLESHEET_RTL : self::BYLINE_STYLESHEET ) );

        Assets::enqueue_style( $file, 'byline' );
    }
    public static function byline_extra_styles()
    {
        $css     = '';
        $options = apply_filters( 'molongui_contributors/byline_settings', Settings::get() );

        $byline_margin = apply_filters( 'molongui_contributors/margin', '20px' );
        $byline_align  = apply_filters( 'molongui_contributors/alignment', $options['alignment'] );
        $byline_color  = apply_filters( 'molongui_contributors/text_color', $options['text_color'] );
        $byline_small  = apply_filters( 'molongui_contributors/smaller_text', !empty( $options['byline_small'] ) ? 'smaller' : 'inherit;' );
        $author_color  = apply_filters( 'molongui_contributors/author_text_color', $options['author_text_color'] );
        $author_bold   = apply_filters( 'molongui_contributors/author_bold', !empty( $options['highlight_author'] ) ? 'bold' : 'inherit;' );
        $css .='.molongui-post-byline { margin-top:'. $byline_margin .'; margin-bottom:'. $byline_margin .'; color:'. $byline_color .'; }';
        $css .='.molongui-post-byline--default-template { align-items:'. $byline_align .'; justify-content:'. $byline_align .'; }';
        $css .='.molongui-post-byline--default-template .molongui-post-byline__row > div:not(.molongui-post-author) { font-size:'. $byline_small .'; }';
        $css .='.molongui-post-author { font-weight:'. $author_bold .'; color:'. $author_color .'; }';

        /*!
         * FILTER HOOK
         *
         * Allows filtering the extra styles.
         *
         * @param string $css     Custom styles to add inline.
         * @param array  $options Plugin options.
         * @since 1.0.0
         * @since 1.4.0  Renamed from 'molongui_contributors/post_byline/extra_styles'
         */
        return Helpers::minify_css( apply_filters( 'molongui_contributors/post_byline_extra_styles', $css, $options ) );
    }
    public function add_custom_styles( $css )
    {
        $custom_css = Settings::get_custom_css();

        if ( !empty( $custom_css ) )
        {
            $css .= Helpers::minify_css( $custom_css );
            Debug::console_log( null, "Custom CSS loaded." );
        }

        return $css;
    }
    public static function hide_theme_byline( $custom_css )
    {
        $default_byline = Settings::get( 'hide_default_byline' );

        if ( !empty( $default_byline ) )
        {
            $custom_css .= $default_byline . '{display:none}';
        }

        return $custom_css;
    }
    public static function is_post_type_enabled( $post_type = null )
    {
        if ( !$post_type )
        {
            $post_type = get_post_type();
            if ( !$post_type and is_admin() )
            {
                $post_type = get_current_screen()->post_type;
            }
        }

        $enabled_post_types = explode( ",", Settings::get( 'post_types', 'post' ) );

        return in_array( $post_type, $enabled_post_types );
    }
    public static function get_contributor_terms( $post = null )
    {
        $post_id = self::get_id( $post );

        if ( !$post_id )
        {
            return array();
        }

        $cache_key         = 'contributors_post_' . $post_id;
        $contributor_terms = wp_cache_get( $cache_key, 'molongui-post-contributors' );
        if ( false === $contributor_terms )
        {
            $args = apply_filters( 'molongui_contributors/contributor_terms_for_post_args', array
            (
                'orderby' => 'term_order',
                'order'   => 'ASC',
                'number'  => 1,
            ));
            $contributor_terms = wp_get_object_terms( $post_id, Contributor_Role::get_contributor_taxonomies(), $args );
            if ( is_wp_error( $contributor_terms ) )
            {
                return array();
            }

            wp_cache_set( $cache_key, $contributor_terms, 'molongui-post-contributors' );
        }

        return $contributor_terms;
    }
    public static function has_contributors( $role = '', $post = null )
    {
        $post_contributors = self::get_contributor_terms( $post );

        if ( !empty( $role ) )
        {
            $terms = array();

            if ( !is_array( $role ) )
            {
                $role = (array) $role;
            }

            foreach ( $role as $item )
            {
                if ( is_string ( $item ) )
                {
                    $term = get_term_by( 'slug', $item, 'contributor_role' );
                    if ( !$term )
                    {
                        $term = get_term_by( 'name', $item, 'contributor_role' );
                    }

                    $terms[] = $term;
                }
                elseif ( is_int ( $item ) )
                {
                    $term = get_term_by( 'id', $item, 'contributor_role' );

                    $terms[] = $term;
                }
            }
            $post_contributors = array_filter( $post_contributors, function ( $obj ) use ( $terms )
            {
                $queried_roles = array_column( (array)$terms, 'slug' );
                return in_array( substr( $obj->taxonomy, strlen( Contributor_Role::get_taxonomy_prefix() ) ), $queried_roles );
            });
        }

        return !empty( $post_contributors );
    }
    public static function set_contributors( $post_id, $contributors, $append = false, $query_type = 'user_nicename' )
    {
        $post_id = (int) $post_id;
        if ( $append )
        {
            $field                 = apply_filters( 'molongui_contributors/post/list_pluck_field', 'user_login' );
            $existing_contributors = wp_list_pluck( self::get_contributors( $post_id ), $field );
        }
        else
        {
            $existing_contributors = array();
        }
        $contributors        = array_replace_recursive( $existing_contributors, $contributors );
        $contributor_objects = array();
        $contributor_terms = self::get_contributor_terms( $post_id );
        if ( is_array( $contributor_terms ) and !empty( $contributor_terms ) )
        {
            foreach ( $contributor_terms as $contributor_term )
            {
                wp_remove_object_terms( $post_id, $contributor_term->term_id, $contributor_term->taxonomy );
            }
        }
        foreach ( $contributors as $role => $role_contributors )
        {
            $_role = $role;
            $role  = Contributor_Role::get_taxonomy_prefix().$_role;

            $contributor_objects[$_role] = array();

            foreach ( $role_contributors as &$contributor_name )
            {
                $field       = apply_filters( 'molongui_contributors/post/get_contributor_by_field', $query_type, $contributor_name );
                $contributor = Contributor::get_by( $field, $contributor_name );
                $term        = Contributor::update_term( $contributor, $role );
                $contributor_objects[$_role][] = $contributor;

                if ( is_object( $term ) )
                {
                    $contributor_name = $term->slug;
                }
            }
            wp_set_post_terms( $post_id, $role_contributors, $role );
        }

        return true;
    }
    public static function get_contributors( $post_id = 0 )
    {
        global $post, $post_ID;

        $post_id = (int) $post_id;
        $contributor_terms = array();
        $post_contributor  = false;

        if ( !$post_id and $post_ID )
        {
            $post_id = $post_ID;
        }

        if ( !$post_id and $post )
        {
            $post_id = $post->ID;
        }

        if ( $post_id )
        {
            $contributor_terms = self::get_contributor_terms( $post_id );
            Debug::console_log( $contributor_terms, sprintf( "Contributor terms for post %s", $post_id ) );

            if ( is_array( $contributor_terms ) and !empty( $contributor_terms ) )
            {
                $contributor_term = reset( $contributor_terms );

                $post_contributor = Contributor::get_by( 'user_nicename', $contributor_term->slug );
                if ( !empty( $post_contributor ) )
                {
                    $role = substr( $contributor_term->taxonomy, strlen( Contributor_Role::get_taxonomy_prefix() ) );
                    $post_contributor->default_role   = $contributor_term->default_role;
                    $post_contributor->post_id        = $post_id;
                    $post_contributor->post_role_id   = $contributor_term->term_taxonomy_id;
                    $post_contributor->post_role_slug = $contributor_term->taxonomy;
                    $post_contributor->post_role_name = $role;
                }
            }
        }

        /*!
         * FILTER HOOK
         *
         * Allows filtering the contributor to the post.
         *
         * @param WP_User   $post_contributor  The contributor assigned to the post.
         * @param WP_Term[] $contributor_terms The contributor terms for the post.
         * @param int       $post_id           The post ID.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/get_post_contributors', $post_contributor, $contributor_terms, $post_id );
    }
    public static function get_byline_prefix()
    {
        $by = Settings::get( 'by', __( "Written by", 'molongui-post-contributors' ) );

        /*!
         * FILTER HOOK
         *
         * Allows filtering the 'By' text.
         *
         * @param string Either the configured 'By' text or the default one set by the plugin.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/by', $by.'&nbsp;' );
    }
    public static function get_the_byline_preview()
    {
        if ( !empty( $_GET['post_id'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            $post_id = $_GET['post_id']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }
        else
        {
            $latest_post = wp_get_recent_posts( array
            (
                'numberposts' => 1,
                'post_status' => 'publish'
            ));

            $post_id = $latest_post[0]['ID'];
        }
        global $post, $authordata;
        $post       = get_post( $post_id );
        $authordata = get_user_by( 'id', $post->post_author );
        add_filter( 'molongui_contributors/is_edit_mode', '__return_true' );

        ob_start();
        include MOLONGUI_CONTRIBUTORS_DIR . 'views/admin/html-byline-preview.php';
        return ob_get_clean();
    }
    public static function add_bylines_to_content( $content )
    {
        if ( is_singular() and in_the_loop() and is_main_query() )
        {
            $html = Template::get_the_meta();
            if ( empty( $html ) ) return $content;

            if ( 'bottom' === apply_filters( 'molongui_contributors/byline_location', 'top' ) )
            {
                $content = $content . $html;
            }
            else
            {
                $content = $html . $content;
            }
        }

        return $content;
    }
    public static function get_the_contributors( $post_id = 0 )
    {
        return Template::get_the_contributor( $post_id );
    }
    public static function the_contributors( $post_id = 0 )
    {
        Template::the_contributor( $post_id );
    }
    public static function get_the_contributor_role_markup( $role )
    {
        return Template::get_the_contributor_role( $role );
    }
    public static function get_the_contributor_name_markup( $contributor )
    {
        return Template::get_the_contributor_name( $contributor );
    }
    public static function get_the_byline( $post_id, $by = null )
    {
        return Template::get_the_byline( $post_id, $by );
    }
    public static function get_the_bylines( $post_id = 0, $layout = null, $settings = array() )
    {
        return Template::get_the_meta( $post_id, $layout, $settings );
    }
    public static function the_byline( $post_id, $by = null )
    {
        Template::the_meta();
    }
    public static function custom_wp_kses_allowed_html( $allowedtags )
    {
        return Template::custom_wp_kses_allowed_html( $allowedtags );
    }

} // class
new Post();