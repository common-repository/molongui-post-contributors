<?php

use Molongui\Contributors\Post;
use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

/*!
 * Public Template Tags.
 *
 * Functions in this file can be used to display/retrieve information regarding contributors for a post.
 *
 *   molongui_the_contributor        --  Displays the contributor for a post.
 *   molongui_get_the_contributor    --  Returns the markup to display the contributor for a post.
 *   molongui_get_post_contributors  --  Returns an array of contributor objects assigned to a post.
 *   molongui_the_post_meta          --  Displays the post meta —that's the post author, contributor and other post info.
 *   molongui_get_the_post_meta      --  Returns the markup to display the post meta —that's the post author, contributor and other post info.
 */
function molongui_the_contributor( $post_id = 0 )
{
    Template::the_contributor( $post_id );
}
function molongui_get_the_contributor( $post_id = 0 )
{
    return Template::get_the_contributor( $post_id );
}
function molongui_get_post_contributors( $post_id = 0 )
{
    return Post::get_contributors( $post_id );
}
function molongui_the_post_meta( $post_id = 0 )
{
    Template::the_meta( $post_id );
}
function molongui_get_the_post_meta( $post_id = 0 )
{
    return Template::get_the_meta( $post_id );
}
function molongui_the_post_bylines( $post_id = 0 )
{
    _deprecated_function( __FUNCTION__, '1.4.0', 'molongui_the_post_byline()' );
    molongui_the_post_byline( $post_id );
}
function molongui_get_the_post_bylines( $post_id = 0 )
{
    _deprecated_function( __FUNCTION__, '1.4.0', 'molongui_get_the_post_byline()' );
    return molongui_get_the_post_byline( $post_id );
}
function molongui_get_the_post_contributors( $post_id = 0 )
{
    _deprecated_function( __FUNCTION__, '1.6.0', 'molongui_get_the_post_byline()' );
    return molongui_get_post_contributors( $post_id );
}
function molongui_the_post_byline( $post_id = 0 )
{
    _deprecated_function( __FUNCTION__, '1.6.0', 'molongui_the_post_meta()' );
    molongui_the_post_meta( $post_id );
}
function molongui_get_the_post_byline( $post_id = 0 )
{
    _deprecated_function( __FUNCTION__, '1.6.0', 'molongui_get_the_post_meta()' );
    return molongui_get_the_post_meta( $post_id );
}