<?php

namespace Molongui\Contributors\Integrations\Themes;

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Blocksy extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'blocksy';
    public function init()
    {
        add_action( 'wp_head', array( $this, 'custom_css' ) );
        add_filter( 'blocksy:post-meta:items', array( $this, 'add_contributor' ), 10, 3 );
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
    public function add_contributor( $to_return, $post_meta_descriptor, $args )
    {
        $style = 'margin:0 9px; position:relative; top:-0.1em; vertical-align:baseline;';
        switch ( $args['meta_divider'] )
        {
            case 'slash':
                $this->separator = '<span style="'.$style.'">/</span>';
                break;
            case 'line':
                $this->separator = '<span style="'.$style.'">-</span>';
                break;
            case 'circle':
                $this->separator = '<span style="'.$style.'">●</span>';
                break;
            case 'none':
            default:
                $this->separator = ' ';
                break;
        }

        $contributor = $this->get_the_contributor();

        if ( !empty( $contributor ) )
        {
            $to_return = $to_return . $contributor;
        }

        return $to_return;
    }
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            .molongui-meta-contributor
            {
                margin-top: 4px;
            }
        </style>
        <?php

        echo apply_filters( 'molongui_contributors/theme_custom_css', Helpers::minify_css( ob_get_clean() ) );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Blocksy();
});