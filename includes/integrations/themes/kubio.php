<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Kubio extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'kubio';
    protected $separator = '<span class="metadata-separator">-</span>';
    protected $js_target = '#post-metadata';
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
        return $this->get_separator();
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
        return '';
    }
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            .molongui-meta-contributor
            {
                display: inline-flex;
                flex-wrap: wrap;
                align-items: center;
            }
            .molongui-meta-contributor > span:not(.metadata-separator)
            {
                color: rgba(var(--kubio-color-6),.5);
                font-family: Syne,Helvetica,Arial,Sans-Serif,serif;
                font-size: 12px;
                font-weight: 400;
                letter-spacing: 1px;
                line-height: 1.5;
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Kubio();
});