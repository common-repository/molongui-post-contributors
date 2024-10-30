<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Phlox extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'phlox';
    protected $separator = '<span class="molongui-meta-separator meta-sep"></span>';
    protected $js_target = '.entry-info:has(.entry-author) .entry-comments';
    protected $js_position = 'afterend';
    public function before_begin_contributor_role( $default )
    {
        return $default;
    }
    public function open_tag_contributor_role( $default )
    {
        return '<span class="meta-sep">';
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
        return $this->get_separator();
    }
    public function open_tag_the_contributor( $default )
    {
        return '<div class="molongui-meta-contributor entry-contributor" itemprop="contributor" itemscope="" itemtype="https://schema.org/Person">';
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
        return '</div>';
    }
    public function after_end_the_contributor( $default )
    {
        return $default;
    }
    public function separator_between_role_and_name( $default )
    {
        return '';
    }
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            .molongui-meta-separator::before
            {
                content: "" !important;
                position: relative;
                top: 0;
                display: inline-block;
                width: 3px;
                height: 3px;
                border-radius: 50%;
                margin: 0 10px;
                vertical-align: middle;
                background-color: #BBB;
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Phlox();
});