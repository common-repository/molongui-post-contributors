<?php

use Molongui\Contributors\Integrations\Themes\Divi;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
if ( !function_exists( 'et_pb_postinfo_meta' ) )
{
    function et_pb_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more )
    {
        $postinfo_meta = array();

        if ( in_array( 'author', $postinfo, true ) ) {
            $postinfo_meta[] = ' ' . esc_html__( 'by', 'et_builder' ) . ' <span class="author vcard">' . et_pb_get_the_author_posts_link() . '</span>';
        }


        if ( in_array( 'date', $postinfo, true ) ) {
            $postinfo_meta[] = '<span class="published">' . esc_html( get_the_time( $date_format ) ) . '</span>';
        }

        if ( in_array( 'categories', $postinfo, true ) ) {
            $categories_list = get_the_category_list( ', ' );
            if ( '' !== $categories_list ) {
                $postinfo_meta[] = $categories_list;
            }
        }

        if ( in_array( 'comments', $postinfo, true ) ) {
            $postinfo_meta[] = et_pb_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );
        }
        $the_contributor = Divi::instance()->get_the_contributor();
        if ( !empty( $the_contributor ) )
        {
            $postinfo_meta[] = $the_contributor;
        }

        return implode( ' | ', array_filter( $postinfo_meta ) );
    }
}