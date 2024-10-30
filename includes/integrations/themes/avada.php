<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Avada extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'avada';
    protected $separator = '<span class="fusion-inline-sep">|</span>';
    public function init()
    {
        add_filter( 'fusion_post_metadata_markup', array( $this, 'add_contributor' ), 10, 1 );
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
        return $default;
    }
    public function add_contributor( $html )
    {
        $contributor = $this->get_the_contributor();

        if ( !empty( $contributor ) )
        {
            $wrappers = array
            (
                'meta_single' => array
                (
                    'open'  => '<div class="fusion-meta-info"><div class="fusion-meta-info-wrapper">',
                    'close' => '</div></div>',
                ),
                'meta_alternate' => array
                (
                    'open'  => '<p class="fusion-single-line-meta">',
                    'close' => '</p>',
                ),
                'meta_else' => array
                (
                    'open'  => '<div class="fusion-alignleft">',
                    'close' => '</div>',
                ),
                'no_meta' => array
                (
                    'open'  => '',
                    'close' => '',
                ),
            );

            foreach ( $wrappers as $wrapper )
            {
                if ( substr( $html, 0, strlen( $wrapper['open'] ) ) === $wrapper['open'] )
                {
                    $_html = substr( $html, strlen( $wrapper['open'] ) );
                    $_html = substr( $_html, 0, -strlen( $wrapper['close'] ) );
                    $_html = $_html . $contributor;
                    $html = $wrapper['open'] . $_html . $wrapper['close'];

                    break;
                }
            }
        }

        return $html;
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Avada();
});