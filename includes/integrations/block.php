<?php

namespace Molongui\Contributors\Integrations;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Block extends \Molongui\Contributors\Integrations\Theme
{
    public function init()
    {
        /*!
         * FILTER HOOK
         *
         * Allows integration with 'post_author' and 'post-author-name' WP blocks to be disabled.
         *
         * @since 1.4.0
         */
        if ( apply_filters( 'molongui_contributors/integrate_gutenberg', true ) )
        {
            add_filter( 'the_content', array( $this, 'mark_user_inserted_blocks' ), 0, 1 );
            add_filter( 'render_block', array( $this, 'add_contributor' ), PHP_INT_MAX, 3 );
        }
    }
    public function mark_user_inserted_blocks( $content )
    {
        $replaced = preg_replace_callback('/(<!-- wp:(post-author |post-author-name )([^>]*))(\/-->)/', function( $matches )
        {
            $block_start      = $matches[1];
            $block_attributes = $matches[3];
            $block_end        = $matches[4];
            return "$block_start$block_attributes{\"molongui-authorship-user-inserted-block\":true} $block_end";
        }, $content);

        return $replaced;
    }
    public function add_contributor( $block_content, $block, $instance )
    {
        if ( in_array( $block['blockName'], array( 'core/post-author', 'core/post-author-name' ) ) )
        {
            if ( !isset( $block['attrs']['molongui-authorship-user-inserted-block'] ) )
            {
                if ( is_main_query() and in_the_loop() and is_singular() )
                {
                    do_action( 'molongui_contributors/block_theme_before_adding_the_contributor', $block_content, $block, $instance );
                    $block_content = apply_filters( 'molongui_contributors/block_theme_before_adding_the_contributor', $block_content, $block, $instance );

                    $block_content .= $this->get_the_contributor();

                    $block_content = apply_filters( 'molongui_contributors/block_theme_after_adding_the_contributor', $block_content, $block, $instance );
                    do_action( 'molongui_contributors/block_theme_after_adding_the_contributor', $block_content, $block, $instance );
                }
            }
        }

        return $block_content;
    }

} // class