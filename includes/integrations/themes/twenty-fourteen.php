<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class TwentyFourteen extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'twenty-fourteen';
    protected $separator = '<span class="molongui-meta-separator"></span>';
    protected $js_target = '.entry-meta:has(.byline)'; //'.entry-meta:not(:has(.cat-links))';
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
        return '<span class="meta-icon"></span>';
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
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            .molongui-meta-contributor
            {
                display: block;
                margin-top: 2px;
            }
            .molongui-meta-separator:before
            {
                content: "\0020\007c\0020";
            }
            @media screen and (min-width: 401px)
            {
                .molongui-meta-contributor .meta-icon:before
                {
                    content: "\f418";
                    font: normal 16px/1 Genericons;
                    -webkit-font-smoothing: antialiased;
                    display: inline-block;
                    position: relative;
                    top: 1px;
                    vertical-align: text-bottom;
                    text-decoration: inherit;
                    <?php if ( is_rtl() ) : ?>
                        margin-left: 1px;
                    <?php else : ?>
                        margin-right: 1px;
                    <?php endif; ?>
                }
                .molongui-meta-separator:before
                {
                    content: ",\0020";
                }
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new TwentyFourteen();
});