<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Blogus extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'blogus';
    protected $separator = '<span>,&nbsp;</span>';
    protected $js_target = '.bs-blog-meta .bs-author';
    protected $js_position = 'afterend';
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
        return '<span class="molongui-meta-contributor">&nbsp;';
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
        return '</span>';
    }
    public function after_end_the_contributor( $default )
    {
        return $default;
    }
    public function separator_between_role_and_name( $default )
    {
        return '';
    }
    public function before_contributors_group( $default )
    {
        return '<div class="molongui-contributor-group">';
    }
    public function after_contributors_group( $default )
    {
        return '</div>';
    }
    public function before_name_separator( $default, $position, $count )
    {
        return $default;
    }
    public function after_name_separator( $default, $position, $count )
    {
        return $default;
    }
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            .molongui-meta-contributor
            {
                display: flex;
            }
            .molongui-meta-contributor::before
            {
                content: "\f058";
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
                position: relative;
                display: inline-block;
                padding-right: 2px;
                padding-left: 1px;
                text-decoration: inherit;
                vertical-align: baseline;
                opacity: 0.8;
            }
            .molongui-contributor-group span
            {
                padding: 0;
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Blogus();
});