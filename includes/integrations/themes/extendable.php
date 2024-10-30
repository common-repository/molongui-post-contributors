<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Extendable extends \Molongui\Contributors\Integrations\Block
{
    protected $name = 'extendable';
    protected $separator = '<span class="has-small-font-size">|</span>';
    public function init()
    {
        add_filter( 'molongui_contributors/block_theme_before_adding_the_contributor', array( $this, 'add_written_by' ), 10, 3 );

        parent::init();
    }
    public function add_written_by( $block_content, $block, $instance )
    {
        if ( apply_filters( 'molongui_contributors/theme_add_written_by', true ) )
        {
            $by = '<div class="has-small-font-size">' . Settings::get( 'by', __( "Written by:", 'molongui-post-contributors' ) ) . '</div>';
            $block_content = $by . $block_content;
        }

        return $block_content;
    }
    public function before_begin_contributor_role( $default )
    {
        return $default;
    }
    public function open_tag_contributor_role( $default )
    {
        return '<p class="has-small-font-size">';
    }
    public function after_begin_contributor_role( $default )
    {
        return $default;
    }
    public function before_end_contributor_role( $default )
    {
        return ':';
    }
    public function close_tag_contributor_role( $default )
    {
        return '</p>';
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
        return '<div class="wp-block-post-author has-small-font-size"><div class="wp-block-post-author__content"><p class="wp-block-post-author__name">';
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
        return '</p></div></div>';
    }
    public function after_end_contributor_name( $default )
    {
        return $default;
    }
    public function before_begin_the_contributor( $default )
    {
        return '<p class="has-small-font-size">|</p>';;
    }
    public function open_tag_the_contributor( $default )
    {
        return '';
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
        return '';
    }
    public function after_end_the_contributor( $default )
    {
        return $default;
    }
    public function separator_between_role_and_name( $default )
    {
        return '';
    }
    public function before_name_separator( $default, $position, $count )
    {
        return '<p class="has-small-font-size">';
    }
    public function after_name_separator( $default, $position, $count )
    {
        return '</p>';
    }
    public function separator_autospace( $default )
    {
        return false;
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Extendable();
});