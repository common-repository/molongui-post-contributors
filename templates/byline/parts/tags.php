<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

if ( $is_preview and !has_tag() )
{
    function molongui_contributors_set_preview_tag( $tag_list, $before, $sep, $after, $post_id )
    {
        return __( "Dummy, Tags", 'molongui-post-contributors' );
    }
    add_filter( 'the_tags', 'molongui_contributors_set_preview_tag', 10, 5 );
}

/*!
 * FILTER HOOK
 *
 * Allows filtering the separator between the categories.
 *
 * @param string The text to use as separator between tags. Default ,&nbsp;
 * @since 1.0.0
 */
$tag_separator = apply_filters( 'molongui_contributors/tags_separator', ',&nbsp;' );

?>
<div class="molongui-post-byline__column molongui-post-tags post-tags">
    <span class="screen-reader-text">
        <?php
        /*! // translators: %s : Hidden accessibility text. */
        esc_html_e( "Tags", 'molongui-post-contributors' );
        ?>
    </span>
    <?php the_tags( '', $tag_separator, '' ); ?>
</div>

<?php

if ( $is_preview and !has_tag() )
{
    remove_filter( 'the_tags', 'molongui_contributors_set_preview_tag', 10, 5 );
}