<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class TwentyTwentyTwo extends \Molongui\Contributors\Integrations\Block
{
    protected $name = 'twenty-twenty-two';
    protected $separator = '';
    public function before_begin_contributor_role( $default )
    {
        return $default;
    }
    public function open_tag_contributor_role( $default )
    {
        return '<span class="has-small-font-size">';
    }
    public function after_begin_contributor_role( $default )
    {
        return $default;
    }
    public function before_end_contributor_role( $default )
    {
        return '';
    }
    public function close_tag_contributor_role( $default )
    {
        return '</span>';
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
        return $default;
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
        return $default;
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
        return '<span class="has-small-font-size">';
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
    new TwentyTwentyTwo();
});