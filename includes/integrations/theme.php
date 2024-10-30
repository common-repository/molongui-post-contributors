<?php

namespace Molongui\Contributors\Integrations;

use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Theme
{
    protected $name = '';
    protected $separator = ' | ';
    private static $_instance = null;
    final public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( "Cloning instances of this class is forbidden.", 'molongui-post-contributors' ), '1.0.0' );
    }
    final public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( "Unserializing instances of this class is forbidden.", 'molongui-post-contributors' ), '1.0.0' );
    }
    final public static function instance()
    {
        if ( is_null( static::$_instance ) )
        {
            static::$_instance = new static();
        }

        return static::$_instance;
    }
    public function __construct()
    {
        /*!
         * FILTER HOOK
         *
         * Allows theme integration to be disabled completely.
         *
         * @since 1.2.0
         */
        if ( apply_filters( 'molongui_contributors/enable_theme_integration', true ) )
        {
            $this->init();
        }
    }
    public function init()
    {
    }
    public function custom_css()
    {
    }
    public function get_name()
    {
        return $this->name;
    }
    public function get_separator()
    {
        return apply_filters( 'molongui_contributors/theme_postmeta_separator', $this->separator );
    }
    public function before_begin_contributor_role( $default )
    {
        return $default;
    }
    public function open_tag_contributor_role( $default )
    {
        return $default;
    }
    public function after_begin_contributor_role( $default )
    {
        return $default;
    }
    public function before_end_contributor_role( $default )
    {
        return $default;
    }
    public function close_tag_contributor_role( $default )
    {
        return $default;
    }
    public function after_end_contributor_role( $default )
    {
        return $default;
    }
    public function before_begin_contributor_name( $default )
    {
        return $default;
    }
    public function open_tag_contributor_name( $default )
    {
        return $default;
    }
    public function before_contributor_name( $default )
    {
        return $default;
    }
    public function before_end_contributor_name( $default )
    {
        return $default;
    }
    public function close_tag_contributor_name( $default )
    {
        return $default;
    }
    public function after_end_contributor_name( $default )
    {
        return $default;
    }
    public function before_begin_the_contributor( $default )
    {
        return $default;
    }
    public function open_tag_the_contributor( $default )
    {
        return $default;
    }
    public function after_begin_the_contributor( $default )
    {
        return $default;
    }
    public function before_end_the_contributor( $default )
    {
        return $default;
    }
    public function close_tag_the_contributor( $default )
    {
        return $default;
    }
    public function after_end_the_contributor( $default )
    {
        return $default;
    }
    public function separator_between_role_and_name( $default )
    {
        return $default;
    }
    public function before_contributors_group( $default )
    {
        return $default;
    }
    public function after_contributors_group( $default )
    {
        return $default;
    }
    public function before_name_separator( $default, $position, $count )
    {
        return $default;
    }
    public function after_name_separator( $default, $position, $count )
    {
        return $default;
    }
    public function separator_autospace( $default )
    {
        return $default;
    }
    public function get_the_contributor()
    {
        add_filter( 'molongui_contributors/before_begin_contributor_role', array( $this, 'before_begin_contributor_role' ) );
        add_filter( 'molongui_contributors/open_tag_contributor_role', array( $this, 'open_tag_contributor_role' ) );
        add_filter( 'molongui_contributors/after_begin_contributor_role', array( $this, 'after_begin_contributor_role' ) );
        add_filter( 'molongui_contributors/before_end_contributor_role', array( $this, 'before_end_contributor_role' ) );
        add_filter( 'molongui_contributors/close_tag_contributor_role', array( $this, 'close_tag_contributor_role' ) );
        add_filter( 'molongui_contributors/after_end_contributor_role', array( $this, 'after_end_contributor_role' ) );

        add_filter( 'molongui_contributors/before_begin_contributor_name', array( $this, 'before_begin_contributor_name' ) );
        add_filter( 'molongui_contributors/open_tag_contributor_name', array( $this, 'open_tag_contributor_name' ) );
        add_filter( 'molongui_contributors/before_contributor_name', array( $this, 'before_contributor_name' ) );
        add_filter( 'molongui_contributors/before_end_contributor_name', array( $this, 'before_end_contributor_name' ) );
        add_filter( 'molongui_contributors/close_tag_contributor_name', array( $this, 'close_tag_contributor_name' ) );
        add_filter( 'molongui_contributors/after_end_contributor_name', array( $this, 'after_end_contributor_name' ) );

        add_filter( 'molongui_contributors/before_begin_the_contributor', array( $this, 'before_begin_the_contributor' ) );
        add_filter( 'molongui_contributors/open_tag_the_contributor', array( $this, 'open_tag_the_contributor' ) );
        add_filter( 'molongui_contributors/after_begin_the_contributor', array( $this, 'after_begin_the_contributor' ) );
        add_filter( 'molongui_contributors/before_end_the_contributor', array( $this, 'before_end_the_contributor' ) );
        add_filter( 'molongui_contributors/close_tag_the_contributor', array( $this, 'close_tag_the_contributor' ) );
        add_filter( 'molongui_contributors/after_end_the_contributor', array( $this, 'after_end_the_contributor' ) );
        add_filter( 'molongui_contributors/separator_between_role_and_name', array( $this, 'separator_between_role_and_name' ) );

        add_filter( 'molongui_contributors/separator', array( $this, 'get_separator' ) );

        add_filter( 'molongui_contributors_pro/before_contributors_group', array( $this, 'before_contributors_group' ) );
        add_filter( 'molongui_contributors_pro/after_contributors_group', array( $this, 'after_contributors_group' ) );
        add_filter( 'molongui_contributors_pro/before_name_separator', array( $this, 'before_name_separator' ), 10, 3 );
        add_filter( 'molongui_contributors_pro/after_name_separator', array( $this, 'after_name_separator' ), 10, 3 );
        add_filter( 'molongui_contributors_pro/separator_autospace', array( $this, 'separator_autospace' ) );
        $markup = Template::get_the_contributor();
        remove_filter( 'molongui_contributors/before_begin_contributor_role', array( $this, 'before_begin_contributor_role' ) );
        remove_filter( 'molongui_contributors/open_tag_contributor_role', array( $this, 'open_tag_contributor_role' ) );
        remove_filter( 'molongui_contributors/after_begin_contributor_role', array( $this, 'after_begin_contributor_role' ) );
        remove_filter( 'molongui_contributors/before_end_contributor_role', array( $this, 'before_end_contributor_role' ) );
        remove_filter( 'molongui_contributors/close_tag_contributor_role', array( $this, 'close_tag_contributor_role' ) );
        remove_filter( 'molongui_contributors/after_end_contributor_role', array( $this, 'after_end_contributor_role' ) );

        remove_filter( 'molongui_contributors/before_begin_contributor_name', array( $this, 'before_begin_contributor_name' ) );
        remove_filter( 'molongui_contributors/open_tag_contributor_name', array( $this, 'open_tag_contributor_name' ) );
        remove_filter( 'molongui_contributors/before_contributor_name', array( $this, 'before_contributor_name' ) );
        remove_filter( 'molongui_contributors/before_end_contributor_name', array( $this, 'before_end_contributor_name' ) );
        remove_filter( 'molongui_contributors/close_tag_contributor_name', array( $this, 'close_tag_contributor_name' ) );
        remove_filter( 'molongui_contributors/after_end_contributor_name', array( $this, 'after_end_contributor_name' ) );

        remove_filter( 'molongui_contributors/before_begin_the_contributor', array( $this, 'before_begin_the_contributor' ) );
        remove_filter( 'molongui_contributors/open_tag_the_contributor', array( $this, 'open_tag_the_contributor' ) );
        remove_filter( 'molongui_contributors/after_begin_the_contributor', array( $this, 'after_begin_the_contributor' ) );
        remove_filter( 'molongui_contributors/before_end_the_contributor', array( $this, 'before_end_the_contributor' ) );
        remove_filter( 'molongui_contributors/close_tag_the_contributor', array( $this, 'close_tag_the_contributor' ) );
        remove_filter( 'molongui_contributors/after_end_the_contributor', array( $this, 'after_end_the_contributor' ) );
        remove_filter( 'molongui_contributors/separator_between_role_and_name', array( $this, 'separator_between_role_and_name' ) );

        remove_filter( 'molongui_contributors/separator', array( $this, 'get_separator' ) );

        remove_filter( 'molongui_contributors_pro/before_contributors_group', array( $this, 'before_contributors_group' ) );
        remove_filter( 'molongui_contributors_pro/after_contributors_group', array( $this, 'after_contributors_group' ) );
        remove_filter( 'molongui_contributors_pro/before_name_separator', array( $this, 'before_name_separator' ), 10 );
        remove_filter( 'molongui_contributors_pro/after_name_separator', array( $this, 'after_name_separator' ), 10 );
        remove_filter( 'molongui_contributors_pro/separator_autospace', array( $this, 'separator_autospace' ) );

        return $markup;
    }

} // class