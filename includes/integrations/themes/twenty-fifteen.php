<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class TwentyFifteen extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'twenty-fifteen';
    protected $separator = ',&nbsp';
    protected $js_target = '.entry-footer';
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
        return '<span class="contributor molongui-meta-contributor">';
    }
    public function after_begin_the_contributor( $default )
    {
        return '<span class="meta-icon">'
                . '<span class="screen-reader-text">' . esc_html__( "Post contributor", 'molongui-post-contributor' ) . '</span>'
                . '<svg class="svg-icon" aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" x="0" y="0" width="16" height="16" viewBox="0 0 24 24">'
                . '<path d="M 20.292969 5.2929688 L 9 16.585938 L 4.7070312 12.292969 L 3.2929688 13.707031 L 9 19.414062 L 21.707031 6.7070312 L 20.292969 5.2929688 z"></path>'
                . '</svg>'
                . '</span>';
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
            .molongui-meta-contributor .meta-icon
            {
                <?php if ( is_rtl() ) : ?>
                    margin-left: 2px;
                <?php else : ?>
                    margin-right: 2px;
                <?php endif; ?>
            }
            .molongui-meta-contributor .meta-icon svg
            {
                position: relative;
                top: 2px;
                fill: #707070;
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new TwentyFifteen();
});