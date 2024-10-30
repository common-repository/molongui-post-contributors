<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Agama extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'agama';
    protected $separator = '<span class="inline-sep">/</span>';
    public function init()
    {
        add_action( 'agama_blog_post_meta', array( $this, 'add_contributor' ), 11 );
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
        return '<p class="single-line-meta" style="margin-top:-20px">';
    }
    public function after_begin_the_contributor( $default )
    {
        return '<i class="fa fa-check-square"></i>&nbsp;';
    }
    public function before_end_the_contributor( $default )
    {
        return $default;
    }
    public function close_tag_the_contributor( $default )
    {
        return '</p>';
    }
    public function after_end_the_contributor( $default )
    {
        return $default;
    }
    public function separator_between_role_and_name( $default )
    {
        return '';
    }
    public function add_contributor()
    {
        $the_contributor = $this->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            echo $the_contributor;
        }
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Agama();
});