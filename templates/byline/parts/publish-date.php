<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

$published_on = apply_filters( 'molongui_contributors/published_on', $config['published_on'] );

?>
<div class="molongui-post-byline__column molongui-post-date post-date">
    <?php
    printf( '%s %s',
            /*! // translators: %s: Published on. */
            sprintf( esc_html_x( '%s', 'Published on', 'molongui-post-contributors' ), $published_on ),
            '<time datetime="'. esc_attr( get_the_date( 'c' ) ) . '" itemprop="datePublished">' . esc_html( get_the_date() ) . '</time>'
    );
    ?>
</div>