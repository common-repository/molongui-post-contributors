<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class YITHWonder extends \Molongui\Contributors\Integrations\Block
{
    protected $name = 'yith-theme';
    protected $separator = '<span class="has-x-small-font-size">|</span>';
    public function open_tag_contributor_role( $default )
    {
        return '<span class="has-x-small-font-size">';
    }
    public function before_end_contributor_role( $default )
    {
        return ':';
    }
    public function close_tag_contributor_role( $default )
    {
        return '&nbsp;</span>';
    }
    public function open_tag_contributor_name( $default )
    {
        return '<div class="wp-block-post-author has-x-small-font-size"><div class="wp-block-post-author__content"><p class="wp-block-post-author__name">';
    }
    public function close_tag_contributor_name( $default )
    {
        return '</p></div></div>';
    }
    public function before_begin_the_contributor( $default )
    {
        return '<span class="has-x-small-font-size">|</span>';;
    }
    public function open_tag_the_contributor( $default )
    {
        return '';
    }
    public function close_tag_the_contributor( $default )
    {
        return '';
    }
    public function separator_between_role_and_name( $default )
    {
        return '';
    }
    public function before_contributors_group( $default )
    {
        return '<span class="has-small-font-size" style="display: flex;">';
    }
    public function after_contributors_group( $default )
    {
        return '</span>';
    }
    public function before_name_separator( $default, $position, $count )
    {
        return '<span class="has-x-small-font-size">';
    }
    public function after_name_separator( $default, $position, $count )
    {
        return '</span>';
    }
    public function separator_autospace( $default )
    {
        return true;
    }

} // class
add_action( 'after_setup_theme', function()
{
    new YITHWonder();
});