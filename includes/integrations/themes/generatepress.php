<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class GeneratePress extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'generatepress';
    protected $separator = ' / ';
    public function init()
    {
        add_filter( 'generate_header_entry_meta_items', array( $this, 'add_contributor' ), 10, 1 );
        add_action( 'generate_post_meta_items', array( $this, 'render_contributor' ), 10, 1 );
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
    public function add_contributor( $meta_items )
     {
         $meta_items[] = 'contributor';
         return $meta_items;
     }
    public function render_contributor( $item )
     {
         if ( 'contributor' === $item )
         {
             echo wp_kses_post( $this->get_the_contributor() );
         }
     }

} // class
add_action( 'after_setup_theme', function()
{
    new GeneratePress();
});