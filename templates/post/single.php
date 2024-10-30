<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

get_header();

do_action( 'molongui_contributors/before_the_loop' );

if ( have_posts() )
{
    while ( have_posts() )
    {
        the_post();
        do_action( 'molongui_contributors/before_the_post' );
        do_action( 'molongui_contributors/the_post' );
        do_action( 'molongui_contributors/after_the_post' );
    }
}

do_action( 'molongui_contributors/after_the_loop' );

get_footer();