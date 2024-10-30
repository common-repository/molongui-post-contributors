<?php

use Molongui\Contributors\Integrations\Themes\TwentyTwentyOne;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'twenty_twenty_one_posted_by' ) )
{
    function twenty_twenty_one_posted_by()
    {
        if ( ! get_the_author_meta( 'description' ) && post_type_supports( get_post_type(), 'author' ) )
        {
            echo '<span class="byline">';
            printf(
                esc_html__( 'By %s', 'twentytwentyone' ),
                '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" rel="author">' . esc_html( get_the_author() ) . '</a>'
            );
            echo '</span>';
        }
        $the_contributor = TwentyTwentyOne::instance()->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            echo $the_contributor;
        }
    }
}