<?php

use Molongui\Contributors\Settings;
use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

$options = Settings::get();

$items = apply_filters( 'molongui_contributors/single_content', array
(
    'header',
    'thumbnail',
    'content',
));

?>

<style>
    @media (min-width: 922px)
    {
        .molongui-content-wrap
        {
            display: flex;
            justify-content: center;
            column-gap: var(--molongui-post-template__content-wrap--column-gap, 3em );
            margin: 0 auto;
            padding: var(--molongui-post-template__content-wrap--padding, 3em );
        }
        .molongui-content-area
        {
            max-width: var(--molongui-post-template__content-area--max-width, 800px );
        }
    }

    .molongui-post-wrap
    {
        display: flex;
        flex-direction: column;
        row-gap: var(--molongui-post-template__post-wrap--row-gap, 2em );
        padding: var(--molongui-post-template__post-wrap--padding, 0 );
    }
    .molongui-post-content p
    {
        margin: 0 0 1.6em;
        line-height: 1.65em;
    }
    .molongui-post-navigation .nav-links
    {
        display: flex;
        justify-content: space-between;
    }
    .molongui-post-navigation .nav-previous span,
    .molongui-post-navigation .nav-next span
    {
        display: inline-block;
        text-decoration: none;
        font-size: 12px;
        color: #c1c1c1;
    }
    .molongui-post-navigation .nav-next
    {
        text-align: right;
    }
    .molongui-post-wrap .molongui-post-byline,
    .molongui-post-wrap .molongui-post-thumbnail,
    .molongui-post-wrap .molongui-post-thumbnail img
    {
        margin: 0;
    }
</style>

<div class="<?php echo apply_filters( 'molongui_contributors/content_wrap_class', 'molongui-content-wrap' ); ?>">
    <?php if ( 'left' === $options['post_sidebar'] ) : ?>
        <?php get_sidebar(); ?>
    <?php endif ?>

    <div class="<?php echo apply_filters( 'molongui_contributors/content_area_class', 'molongui-content-area' ); ?>">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemtype="https://schema.org/CreativeWork" itemscope="itemscope">
            <div class="<?php echo esc_attr( apply_filters( 'molongui_contributors/post_wrap_class', 'molongui-post-wrap' ) ); ?>">
                <?php
                foreach ( $items as $item )
                {
                    switch( $item )
                    {
                        case 'header':
                            Template::the_header();
                            break;

                        case 'footer':
                            Template::the_footer();
                            break;

                        case 'thumbnail':
                            if ( !empty( $options['post_thumbnail'] ) )
                            {
                                Template::the_thumbnail();
                            }
                            break;

                        case 'title':
                            if ( !empty( $options['post_title'] ) )
                            {
                                Template::the_title();
                            }
                            break;

                        case 'byline':
                            Template::the_meta();
                            break;

                        case 'content':
                            Template::the_content();
                            break;

                        case 'taxonomies':
                            if ( !empty( $options['post_categories'] ) )
                            {
                                Template::the_taxonomies();
                            }
                            break;

                        case 'sharing':
                            if ( !empty( $options['post_share'] ) )
                            {
                                Template::the_sharing();
                            }
                            break;

                        case 'navigation':
                            if ( !empty( $options['post_navigation'] ) )
                            {
                                Template::the_navigation();
                            }
                            break;

                        case 'related':
                            if ( !empty( $options['post_related'] ) )
                            {
                                Template::the_related();
                            }
                            break;

                        case 'comments':
                            if ( !empty( $options['post_comments'] ) )
                            {
                                Template::the_comments();
                            }
                            break;
                    }
                }
                ?>
            </div>
        </article>
    </div>

    <?php if ( 'right' === $options['post_sidebar'] ) : ?>
        <?php get_sidebar(); ?>
    <?php endif ?>
</div>