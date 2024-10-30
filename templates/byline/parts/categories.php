<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

/*!
 * FILTER HOOK
 *
 * Allows filtering the word to place before the category list.
 *
 * @param string The text to add before the categories. Default 'In'.
 * @since 1.0.0
 */
$in = apply_filters( 'molongui_contributors/in', $config['in'] );

/*!
 * FILTER HOOK
 *
 * Allows filtering the separator between the categories.
 *
 * @param string The text to use as separator between categories. Default &bull;
 * @since 1.0.0
 */
$cat_separator = apply_filters( 'molongui_contributors/categories_separator', '&nbsp;&bull;&nbsp;' );

?>
<div class="molongui-post-byline__column molongui-post-categories post-categories">
    <span class="screen-reader-text">
        <?php
        /*! // translators: %s: Hidden accessibility text. */
        esc_html_e( "Categories", 'molongui-post-contributors' );
        ?>
    </span>
    <?php
    printf( '%s %s',
        /*! // translators: %s: In. */
        sprintf( esc_html_x( '%s', 'In', 'molongui-post-contributors' ), $in ),
        get_the_category_list( $cat_separator )
    );
    ?>
</div>