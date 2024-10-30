<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

?>
<div class="molongui-post-byline__column molongui-post-date post-date">
    <?php
    printf( '%s %s',
        /*! // translators: %s: Updated on. */
        sprintf( esc_html_x( '%s', 'Updated on', 'molongui-post-contributors' ), apply_filters( 'molongui_contributors/updated_on', $config['updated_on'] ) ),
        '<time datetime="'. esc_attr( get_the_modified_date( 'c' ) ) . '" itemprop="dateModified">' . esc_html( get_the_modified_date() ) . '</time>'
    );
    ?>
</div>