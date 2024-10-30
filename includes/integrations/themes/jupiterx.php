<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class JupiterX extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'jupiterx';
    protected $separator = '</li><li class="jupiterx-post-meta-contributor list-inline-item">';
    protected $position = 21;
    public function init()
    {
        add_filter( 'jupiterx_post_meta_elements', array( $this, 'add_contributor' ) );
        add_filter( 'jupiterx_post_meta_items', array( $this, 'order_contributor' ) );
        add_action( "jupiterx_post_meta_contributor", array( $this, 'render_contributor' ) );
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
        return '';
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
        return '';
    }
    public function after_end_the_contributor( $default )
    {
        return $default;
    }
    public function separator_between_role_and_name( $default )
    {
        return '';
    }
    public function add_contributor( $items )
    {
        $items[] = 'contributor';

        return $items;
    }
    public function order_contributor( $items )
    {
        /*!
         * FILTER HOOK
         *
         * Allows defining the position where the contributor will be displayed in the post byline.
         *
         * @see     themes/jupiterx/lib/templates/fragments/post.php
         *          jupiterx_post_meta()
         *
         * @param   int   $position
         * @since   1.5.0
         * @version 1.5.0
         */
        $items['contributor'] = apply_filters( 'molongui_contributors/jupiterx_contributor_position', $this->position );

        return $items;
    }
    public function render_contributor()
    {
        echo $this->get_the_contributor();
    }

} // class
add_action( 'after_setup_theme', function()
{
    new JupiterX();
});