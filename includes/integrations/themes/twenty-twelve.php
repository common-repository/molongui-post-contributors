<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class TwentyTwelve extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'twenty-twelve';
    protected $separator = '<span class="molongui-meta-separator"></span>';
    protected $js_target = '.author-info'; //'.entry-meta .by-author';
    protected $js_position = 'beforebegin';
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
        return '<span class="molongui-meta-contributor">';
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
                display: block;
            }
            .molongui-meta-separator:before
            {
                content: ",\0020";
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new TwentyTwelve();
});