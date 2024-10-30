<?php

use Molongui\Contributors\Integrations\Themes\PopularFx;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'popularfx_posted_by' ) )
{
    function popularfx_posted_by()
    {
        $byline = sprintf(
            esc_html_x( 'by %s', 'post author', 'popularfx' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $the_contributor = PopularFx::instance()->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            echo $the_contributor;
        }
    }
}