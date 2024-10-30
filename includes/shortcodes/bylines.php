<?php

use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Post;
use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

add_shortcode( 'molongui_post_meta', 'molongui_contributors_post_meta_shortcode' );
add_shortcode( 'molongui_post_info', 'molongui_contributors_post_meta_shortcode' );
function molongui_contributors_post_meta_shortcode( $atts )
{
    /*!
     * FILTER HOOK
     *
     * Allows this shortcode to be voided.
     *
     * @since 1.0.0
     * @since 1.6.0 Renamed from 'molongui_contributors/void_bylines_shortcode'
     */
    if ( apply_filters( 'molongui_contributors/void_post_meta_shortcode', false ) )
    {
        return;
    }
    add_filter( '_bylines/doing_shortcode', '__return_true' );
    add_filter( '_bylines/doing_shortcode/post_meta', '__return_true' );
    $atts = shortcode_atts( array
    (
        'post_id'           => null,
        'layout'            => null,
        'show_author'       => null,
        'show_contributors' => null,
        'show_publish_date' => null,
        'show_update_date'  => null,
        'show_categories'   => null,
        'show_tags'         => null,
        'show_comment_link' => null,
        'separator'         => null,
        'alignment'         => null,
        'text_color'        => null,
        'author_text_color' => null,
        'highlight_author'  => null,
        'byline_small'      => null,
'html_tag'       => '',
'html_id'        => '',
'html_class'     => '',
        'is_editor'         => false,
    ), (array)$atts );
    $boolean = array
    (
        'show_author',
        'show_contributors',
        'show_publish_date',
        'show_update_date',
        'show_categories',
        'show_tags',
        'show_comment_link',
        'highlight_author',
        'byline_small',
        'is_editor',
    );
    foreach( $boolean as $key )
    {
        if ( !is_null( $atts[$key] ) )
        {
            $atts[$key] = ( true === $atts[$key] or in_array( strtolower( $atts[$key] ), array( 'yes', 'y', 'true', 'on', 'include' ) ) ) ? true : false;
        }
    }
    if ( is_null( $atts['post_id'] ) or !is_numeric( $atts['post_id'] ) or !$atts['post_id'] )
    {
        $atts['post_id'] = Post::get_id();
    }

    Debug::console_log( $atts, "Doing [molongui_post_meta] shortcode:" );
    if ( $atts['is_editor'] )
    {
        add_filter( 'molongui_contributors/is_edit_mode', '__return_true' );
    }
    unset( $atts['is_editor'] );
    remove_filter( '_bylines/doing_shortcode', '__return_true' );
    remove_filter( '_bylines/doing_shortcode/post_meta', '__return_true' );
    if ( !has_filter( 'molongui_contributors/byline_generator' ) )
    {
        add_filter( 'molongui_contributors/byline_generator', function()
        {
            return 'shortcode';
        });
    }
    $overrides = array();
    foreach( $atts as $key => $value )
    {
        if ( !is_null( $value ) )
        {
            $overrides['mpb_'.$key] = $value;
        }
    }

    return Template::get_the_meta( $atts['post_id'], $atts['layout'], $overrides );
}
add_filter( 'molongui_contributors/void_post_meta_shortcode', function( $default )
{
    if ( is_feed() )
    {
        return true;
    }

    return $default;
});
add_shortcode( 'molongui_post_bylines', 'molongui_contributors_shortcode_deprecated' );
add_shortcode( 'molongui_post_byline', 'molongui_contributors_shortcode_deprecated' );
add_shortcode( 'molongui_bylines', 'molongui_contributors_shortcode_deprecated' );
function molongui_contributors_shortcode_deprecated( $atts )
{
    if ( function_exists( 'doing_it_wrong' ) )
    {
        doing_it_wrong(
            __FUNCTION__,
            __( 'The [molongui_post_bylines] shortcode has been deprecated. Please use [molongui_post_meta] instead.', 'molongui-post-contributors' ),
            '1.6.0'
        );
    }

    return molongui_contributors_post_meta_shortcode( $atts );
}