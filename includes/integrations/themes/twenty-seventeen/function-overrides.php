<?php

use Molongui\Contributors\Integrations\Themes\TwentySeventeen;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'twentyseventeen_posted_on' ) )
{
    function twentyseventeen_posted_on()
    {
        $byline = sprintf(
            __( 'by %s', 'twentyseventeen' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a></span>'
        );
        echo '<span class="posted-on">' . twentyseventeen_time_link() . '</span><span class="byline"> ' . $byline . '</span>';
        $the_contributor = TwentySeventeen::instance()->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            echo $the_contributor;
        }
    }
}