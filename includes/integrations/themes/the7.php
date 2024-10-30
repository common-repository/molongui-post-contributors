<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class The7 extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'the7';
    protected $separator = '<span></span>';
    public function init()
    {
        add_action( 'wp_head', array( $this, 'custom_css' ) );
        add_filter( 'presscore_posted_on_html', array( $this, 'add_contributor' ), 11 );
    }
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
        return '<div itemprop="contributor" itemscope="" itemtype="https://schema.org/Person" class="molongui-meta-contributor entry-meta">';
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
    public function separator_autospace( $default )
    {
        return $default;
    }
    public function add_contributor( $html )
    {
        $the_contributor = $this->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            $html = $html . $the_contributor;
        }

        return $html;
    }
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            .molongui-meta-contributor
            {
                display: inline;
            }
            .molongui-meta-contributor > span::after
            {
                content: '' !important;
                padding: 0 !important;
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new The7();
});