<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class OceanWP extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'oceanwp';
    protected $separator = '<span style="padding:0 8px">-</span>';
    protected $js_target = '.single-post ul.meta'; //'.meta.ospm-default, .meta.obem-default';
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
        return '<span class="screen-reader-text">' . esc_html__( "Post contributor", 'molongui-post-contributors' ) . ':</span><i class=" icon-user-following" aria-hidden="true" role="img"></i>';
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
            ul.meta li:nth-last-child(2):after
            {
                display: none;
                padding: 0;
            }

            .molongui-meta-contributor i
            {
                font-size: 17px;
                <?php if ( is_rtl() ) : ?>
                    padding-left: 9px;
                <?php else : ?>
                    padding-right: 9px;
                <?php endif; ?>
            }
        </style>
        <?php

        echo Helpers::minify_css( ob_get_clean() );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new OceanWP();
});