<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Astra extends \Molongui\Contributors\Integrations\Theme
{
    protected $name = 'astra';
    protected $position = 'last';
    public function __construct()
    {
        if ( function_exists( 'astra_get_option' ) )
        {
            $this->separator = astra_get_option( 'ast-dynamic-single-' . strval( get_post_type() ) . '-metadata-separator', '/' );
            if ( !empty( $this->get_separator() ) and '&nbsp;' !== $this->get_separator() )
            {
                $this->separator =  '&nbsp;' . trim( $this->get_separator() ) . '&nbsp;';
            }
        }
        else
        {
            $this->separator = ' / ';
        }

        /*!
         * FILTER HOOK
         *
         * Allows defining the position where the contributor will be displayed in the post byline.
         *
         * To display the contributor at the end of the byline, use 'last'.
         *
         * @param   int|string Either an integer, 'after' to display the contributor after the author name or 'last' to display it at the end.
         * @since   1.0.0
         * @version 1.0.0
         */
        $this->position = apply_filters( 'molongui_contributors/astra_metadata_position', $this->position );

        parent::__construct();
    }
    public function init()
    {
        add_filter( "astra_get_option_ast-dynamic-single-post-metadata", array( $this, 'add_contributor' ), 10, 3 );
        add_filter( 'astra_meta_case_contributor', array( $this, 'render_contributor' ), 10, 3 );
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
    public function add_contributor( $value, $option, $default )
    {
        if ( 'after' === $this->position )
        {
            $author_key = array_search( 'author', $value );
            array_splice( $value, $author_key+1, 0, 'contributor' );
        }
        elseif( is_numeric( $this->position ) )
        {
            array_splice( $value, $this->position, 0, 'contributor' );
        }
        else
        {
            $value[] = 'contributor';
        }

        return array_unique( $value );
    }
    public function render_contributor( $output_str, $loop_count, $separator )
    {
        $the_contributor = $this->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            if ( 1 === $loop_count and '' === $output_str )
            {
                $the_contributor = ltrim( ltrim( $the_contributor, $this->get_separator() ) );
            }
            $output_str .= $the_contributor;
        }

        return $output_str;
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Astra();
});