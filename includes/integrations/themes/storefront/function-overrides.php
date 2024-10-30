<?php

use Molongui\Contributors\Integrations\Themes\Storefront;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'storefront_post_meta' ) )
{
    function storefront_post_meta()
    {
        if ( 'post' !== get_post_type() ) {
            return;
        }
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( 'c' ) ),
            esc_html( get_the_modified_date() )
        );

        $output_time_string = sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>', esc_url( get_permalink() ), $time_string );

        $posted_on = '
			<span class="posted-on">' .
            sprintf( __( 'Posted on %s', 'storefront' ), $output_time_string ) .
            '</span>';
        $author = sprintf(
            '<span class="post-author">%1$s <a href="%2$s" class="url fn" rel="author">%3$s</a></span>',
            __( 'by', 'storefront' ),
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_html( get_the_author() )
        );
        $comments = '';

        if ( ! post_password_required() && ( comments_open() || 0 !== intval( get_comments_number() ) ) ) {
            $comments_number = get_comments_number_text( __( 'Leave a comment', 'storefront' ), __( '1 Comment', 'storefront' ), __( '% Comments', 'storefront' ) );

            $comments = sprintf(
                '<span class="post-comments">&mdash; <a href="%1$s">%2$s</a></span>',
                esc_url( get_comments_link() ),
                $comments_number
            );
        }
        $the_contributor = Storefront::instance()->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            $the_contributor = '<span class="molongui-meta-contributor post-author"> — ' . $the_contributor . ' </span>';
        }

        echo wp_kses(
            sprintf( '%1$s %2$s%3$s%4$s', $posted_on, $author, $the_contributor, $comments ),
            array(
                'span' => array(
                    'class' => array(),
                ),
                'a'    => array(
                    'href'  => array(),
                    'title' => array(),
                    'rel'   => array(),
                ),
                'time' => array(
                    'datetime' => array(),
                    'class'    => array(),
                ),
            )
        );
    }
}