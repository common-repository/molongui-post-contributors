<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Kadence extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'kadence';
    protected $separator = '<span class="molongui-post-byline-separator"></span>';
    public function init()
    {
        add_action( 'wp_head', array( $this, 'custom_css' ) );
        add_filter( 'kadence_author_meta_output', array( $this, 'add_contributor' ), 11 );
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
    public function add_contributor( $author_output )
    {
        $the_contributor = $this->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            $author_output = $author_output . $the_contributor;
        }

        return $author_output;
    }
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            .molongui-post-byline-separator::after
            {
                margin-left:.5rem;
                margin-right: .5rem;
                content: "";
                display: inline-block;
                background-color: currentColor;
                height: .25rem;
                width: .25rem;
                opacity: .8;
                border-radius: 9999px;
                vertical-align: .1875em;
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Kadence();
});