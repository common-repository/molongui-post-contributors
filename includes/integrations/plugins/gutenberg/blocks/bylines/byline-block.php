<?php

namespace Molongui\Contributors\Integrations\Plugins\Gutenberg\Blocks\Bylines;

use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Post;
use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Byline_Block
{
    public function __construct()
    {
        add_action( 'init', array( $this, 'register' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
        add_action( 'enqueue_block_assets', array( $this, 'enqueue_assets' ) );
    }
    public function register()
    {
        $r = register_block_type( 'molongui-contributors/post-bylines', array
        (
            'editor_script'   => 'bylines-block-editor-script',
            'editor_style'    => 'bylines-block-editor-style',
            'style'           => 'bylines-block-style',
            'render_callback' => array( $this, 'render' ),
            'attributes'      => array
            (
                'layout' => array
                (
                    'type'    => 'string',
                    'default' => '1',
                ),
                'align' => array
                (
                    'type'    => 'string',
                    'default' => 'start',
                ),
                'highlightAuthor' => array
                (
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'bylineSmall' => array
                (
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'showAuthor' => array
                (
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'showContributors' => array
                (
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'showPublishDate' => array
                (
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'showUpdateDate' => array
                (
                    'type'    => 'boolean',
                    'default' => false,
                ),
                'showCategories' => array
                (
                    'type'    => 'boolean',
                    'default' => false,
                ),
                'showTags' => array
                (
                    'type'    => 'boolean',
                    'default' => false,
                ),
                'showCommentLink' => array
                (
                    'type'    => 'boolean',
                    'default' => false,
                ),
            ),
        ));

        if ( $r )
        {
            Debug::console_log( null, 'Custom WP block registered.' );
        }
        else
        {
            Debug::console_log( null, '[ERROR] Custom WP block not registered.' );
        }
    }
    public function render( $attributes )
    {
        $layout            = isset( $attributes['layout'] ) ? 'layout="'.$attributes['layout'].'"' : 'layout="1"';
        $alignment         = isset( $attributes['align'] ) ? 'alignment="'.$attributes['align'].'"' : 'alignment="start"';
        $highlight_author  = ( isset( $attributes['highlightAuthor'] ) and $attributes['highlightAuthor'] ) ? 'highlight_author="yes"' : 'highlight_author="no"';
        $byline_small      = ( isset( $attributes['bylineSmall'] ) and $attributes['bylineSmall'] ) ? 'byline_small="yes"' : 'byline_small="no"';

        $show_author       = ( isset( $attributes['showAuthor'] ) and $attributes['showAuthor'] ) ? 'show_author="yes"' : 'show_author="no"' ;
        $show_contributors = ( isset( $attributes['showContributors'] ) and $attributes['showContributors'] ) ? 'show_contributors="yes"' : 'show_contributors="no"' ;
        $show_publish_date = ( isset( $attributes['showPublishDate'] ) and $attributes['showPublishDate'] ) ? 'show_publish_date="yes"' : 'show_publish_date="no"' ;
        $show_update_date  = ( isset( $attributes['showUpdateDate'] ) and $attributes['showUpdateDate'] ) ? 'show_update_date="yes"' : 'show_update_date="no"' ;
        $show_categories   = ( isset( $attributes['showCategories'] ) and $attributes['showCategories'] ) ? 'show_categories="yes"' : 'show_categories="no"' ;
        $show_tags         = ( isset( $attributes['showTags'] ) and $attributes['showTags'] ) ? 'show_tags="yes"' : 'show_tags="no"' ;
        $show_comment_link = ( isset( $attributes['showCommentLink'] ) and $attributes['showCommentLink'] ) ? 'show_comment_link="yes"' : 'show_comment_link="no"' ;

        $is_block_editor   = 'is_editor=yes';
        add_filter( 'molongui_contributors/byline_generator', function()
        {
            return 'wp-block';
        });
        return do_shortcode( '[molongui_post_meta ' . $layout . ' ' . $alignment . ' ' . $highlight_author . ' ' . $byline_small . ' ' . $show_author . ' ' . $show_contributors . ' ' . $show_publish_date . ' ' . $show_update_date . ' ' . $show_categories . ' ' . $show_tags . ' ' . $show_comment_link .  ' ' . $is_block_editor . ']' );
    }
    public function enqueue_editor_assets()
    {
        $folder = 'includes/integrations/plugins/gutenberg/blocks/bylines/';
        $dir    = MOLONGUI_CONTRIBUTORS_DIR . $folder;
        $path   = MOLONGUI_CONTRIBUTORS_URL . $folder;

        $filename = 'block-editor.d8d6.min.js';
        $file     = $dir . $filename;
        if ( file_exists( $file ) and filesize( $file ) )
        {
            wp_enqueue_script(
                'bylines-block-editor-script',
                $path . $filename,
                array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-i18n', 'wp-block-editor', 'wp-server-side-render' ),
                MOLONGUI_CONTRIBUTORS_VERSION,
                array( 'strategy' => 'defer', 'in_footer' => true )
            );
            $byline_layout_options = array
            (
                array( 'label' => __( "Layout 1", 'molongui-post-contributors' ), 'value' => '1' ),
                array( 'label' => __( "Layout 2", 'molongui-post-contributors' ), 'value' => '2' ),
                array( 'label' => __( "Layout 3", 'molongui-post-contributors' ), 'value' => '3' ),
                array( 'label' => __( "Layout 4", 'molongui-post-contributors' ), 'value' => '4' ),
                array( 'label' => __( "Layout 5", 'molongui-post-contributors' ), 'value' => '5' ),
            );
            wp_localize_script(
                'bylines-block-editor-script',
                'molongui_contributors_byline_layout_options',
                apply_filters( 'molongui_contributors/gutenberg_byline_layout_options', $byline_layout_options )
            );
        }

        $filename = is_rtl() ? 'block-editor-rtl.d08b.min.css' : 'block-editor.d08b.min.css';
        $file     = $dir . $filename;
        if ( file_exists( $file ) and filesize( $file ) )
        {
            wp_enqueue_style(
                'bylines-block-editor-style',
                $path . $filename,
                array(),
                MOLONGUI_CONTRIBUTORS_VERSION
            );
        }
    }
    public function enqueue_assets()
    {
        $folder = 'includes/integrations/plugins/gutenberg/blocks/bylines/';
        $dir    = MOLONGUI_CONTRIBUTORS_DIR . $folder;
        $path   = MOLONGUI_CONTRIBUTORS_URL . $folder;

        $filename = 'block.xxxx.min.js';
        $file = $dir . $filename;
        if ( file_exists( $file ) and filesize( $file ) )
        {
            wp_enqueue_script(
                'bylines-block-script',
                $path . $filename,
                array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-block-editor', 'wp-server-side-render' ),
                MOLONGUI_CONTRIBUTORS_VERSION,
                array( 'strategy' => 'defer', 'in_footer' => true )
            );
        }

        $filename = 'block.xxxx.min.css';
        $file = $dir . $filename;
        if ( file_exists( $file ) and filesize( $file ) )
        {
            wp_enqueue_style(
                'bylines-block-style',
                $path . $filename,
                array(),
                MOLONGUI_CONTRIBUTORS_VERSION
            );
        }
    }

} // class