<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Modules\Settings;
use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Template
{
    public function __construct()
    {
        add_filter( 'template_include', array( __CLASS__, 'override' ), PHP_INT_MAX );
        add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
        add_action( 'wp_head', array( __CLASS__, 'css_custom_properties' ) );

        add_filter( 'molongui_contributors/link_author_name_to_archive', array( $this, 'link_author' ) );
        add_filter( 'molongui_contributors/link_contributor_name_to_archive', array( $this, 'link_contributor' ) );

        add_action( 'molongui_contributors/after_post_content', array( __CLASS__, 'the_taxonomies' ), 11 );
        add_action( 'molongui_contributors/after_post_content', array( __CLASS__, 'the_sharing'    ), 12 );
        add_action( 'molongui_contributors/after_post_content', array( __CLASS__, 'the_navigation' ), 13 );
        add_action( 'molongui_contributors/after_post_content', array( __CLASS__, 'the_related'    ), 14 );
        add_action( 'molongui_contributors/after_post_content', array( __CLASS__, 'the_comments'   ), 15 );
    }
    public static function override( $template )
    {
        if ( is_singular('post' ) )
        {
            $options = Settings::get();
            if ( !empty( $options['post_template_override'] ) and apply_filters( 'molongui_contributors/override_theme_template', true ) )
            {
                $plugin_template = MOLONGUI_CONTRIBUTORS_DIR . 'templates/post/single.php';

                /*!
                 * FILTER HOOK
                 *
                 * Allows filtering the post template.
                 *
                 * @param string  $plugin_template  The path of the template to include.
                 * @param string  $template         The path of the default template to include.
                 * @since 1.0.0
                 */
                $custom_template = apply_filters( 'molongui_contributors/post_template', $plugin_template, $template );

                if ( !file_exists( $custom_template ) )
                {
                    if ( $custom_template !== $plugin_template )
                    {
                        if ( !file_exists( $plugin_template ) )
                        {
                            Debug::console_log( null, "Plugin post template (templates/post/single.php) not found. Loading theme's default. Reinstall plugin?" );
                            return $template;
                        }
                        else
                        {
                            Debug::console_log( null, sprintf( "Provided post template (%s) not found. Loading plugin's default.", $custom_template ) );
                            $custom_template = $plugin_template;
                        }
                    }
                    else
                    {
                        Debug::console_log( null, "Plugin post template (templates/post/single.php) not found. Loading theme's default. Reinstall plugin?" );
                        return $template;
                    }
                }

                $post_template_id = !empty( $options['post_template'] ) ? $options['post_template'] : '1';
                $post_template    = MOLONGUI_CONTRIBUTORS_DIR . 'templates/post/single-content-' . $post_template_id . '.php';

                if ( file_exists( $post_template ) )
                {
                    add_action( 'molongui_contributors/the_post', function() use ( $post_template )
                    {
                        require $post_template;
                    });
                    Debug::console_log( null, sprintf( "Loading the '%s' template for this post.", $post_template ) );

                    $template = $custom_template;
                }
            }
            else
            {
                Debug::console_log( null, "Post template override disabled. Loading default theme's template for this post." );
            }
        }

        return $template;
    }
    public static function body_class( $classes )
    {
        $options = Settings::get();

        if ( 'left' === $options['post_sidebar'] )
        {
            $classes[] = 'left-sidebar';
        }
        elseif ( 'right' === $options['post_sidebar'] )
        {
            $classes[] = 'right-sidebar';
        }

        return $classes;
    }
    public static function css_custom_properties()
    {
        ?>
            <style>
                .molongui-content-wrap
                {
                    --molongui-post-template__content-wrap--column-gap: <?php echo esc_attr( Settings::get( 'content-wrap-column-gap', 3 ) ); ?>em;
                    --molongui-post-template__content-wrap--padding: <?php echo esc_attr( Settings::get( 'content-wrap-padding', 3 ) ); ?>em;
                }
                .molongui-content-area
                {
                    --molongui-post-template__content-area--max-width: <?php echo esc_attr( Settings::get( 'content-area-max-width', 800 ) ); ?>px;
                }
                .molongui-post-wrap
                {
                    --molongui-post-template__post-wrap--row-gap: <?php echo esc_attr( Settings::get( 'post-wrap-row-gap', 2 ) ); ?>em;
                    --molongui-post-template__post-wrap--padding: <?php echo esc_attr( Settings::get( 'post-wrap-padding', 0 ) ); ?>em;
                }
            </style>
        <?php
    }
    public static function link_author()
    {
        return Settings::get( 'link_author', true );
    }
    public static function link_contributor()
    {
        return Settings::get( 'link_contributor', true );
    }
    public static function get_the_header()
    {
        ob_start();

        do_action( 'molongui_contributors/before_post_header' ); ?>

        <header class="<?php echo esc_attr( apply_filters( 'molongui_contributors/post_header_class', 'molongui-post-header' ) ); ?>">
            <?php self::the_title(); ?>
            <?php self::the_meta(); ?>
        </header>

        <?php do_action( 'molongui_contributors/after_post_header' );

        $the_header = ob_get_clean();

        /*!
         * FILTER HOOK
         *
         * Allows filtering the post header to return.
         *
         * @param string $the_header The post header to return.
         * @since 1.6.0
         */
        return apply_filters( 'molongui_contributors/get_the_header', $the_header );
    }
    public static function the_header()
    {
        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup to display the post header.
         *
         * @param string $the_header The post header to display.
         * @since 1.6.0
         */
        echo wp_kses_post( apply_filters( 'molongui_contributors/the_header', self::get_the_header() ) );
    }
    public static function get_the_footer()
    {
        ob_start();

        do_action( 'molongui_contributors/before_post_footer' ); ?>

        <footer class="<?php echo esc_attr( apply_filters( 'molongui_contributors/post_footer_class', 'molongui-post-footer' ) ); ?>">
            <?php // todo... ?>
        </footer>

        <?php do_action( 'molongui_contributors/after_post_footer' );

        $the_footer = ob_get_clean();

        /*!
         * FILTER HOOK
         *
         * Allows filtering the post footer to return.
         *
         * @param string $the_footer The post footer to return.
         * @since 1.6.0
         */
        return apply_filters( 'molongui_contributors/get_the_footer', $the_footer );
    }
    public static function the_footer()
    {
        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup to display the post footer.
         *
         * @param string $the_footer The post footer to display.
         * @since 1.6.0
         */
        echo wp_kses_post( apply_filters( 'molongui_contributors/the_footer', self::get_the_footer() ) );
    }
    public static function get_the_thumbnail()
    {
        ob_start();

        do_action( 'molongui_contributors/before_post_thumbnail' );

        if ( has_post_thumbnail() and !post_password_required() )
        {
            ?>
            <div class="<?php echo esc_attr( apply_filters( 'molongui_contributors/post_thumbnail_class', 'molongui-post-thumbnail' ) ); ?>">
                <?php
                the_post_thumbnail(
                    apply_filters( 'molongui_contributors/the_post_thumbnail_size', 'full' ),
                    array
                    (
                        'itemprop' => 'image',
                        'class'    => apply_filters( 'molongui_contributors/the_post_thumbnail_class', 'molongui-post-thumb' ),
                    )
                );
                ?>
            </div>
            <?php
        }

        do_action( 'molongui_contributors/after_post_thumbnail' );

        $the_thumbnail = ob_get_clean();

        /*!
         * FILTER HOOK
         *
         * Allows filtering the post thumbnail to return.
         *
         * @param string $the_thumbnail The post thumbnail to return.
         * @since 1.6.0
         */
        return apply_filters( 'molongui_contributors/get_the_thumbnail', $the_thumbnail );
    }
    public static function the_thumbnail()
    {
        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup to display the post thumbnail.
         *
         * @param string $the_thumbnail The post thumbnail to display.
         * @since 1.6.0
         */
        echo wp_kses_post( apply_filters( 'molongui_contributors/the_thumbnail', self::get_the_thumbnail() ) );
    }
    public static function get_the_title()
    {
        $class = apply_filters( 'molongui_contributors/post_title_class', 'molongui-post-title' );

        $the_title = '<h1 class="' . esc_attr( $class ) . '">' . esc_html( get_the_title() ) . '</h1>';

        /*!
         * FILTER HOOK
         *
         * Allows filtering the post title to return.
         *
         * @param string $the_title The post title to return.
         * @since 1.6.0
         */
        return apply_filters( 'molongui_contributors/get_the_title', $the_title );
    }
    public static function the_title()
    {
        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup to display the post title.
         *
         * @param string $the_title The post title to display.
         * @since 1.6.0
         */
        echo wp_kses_post( apply_filters( 'molongui_contributors/the_title', self::get_the_title() ) );
    }
    public static function get_the_byline( $post_id = null, $by = null )
    {
        if ( !isset( $post_id ) )
        {
            $post_id = Post::get_id();
        }
        $post_id = (int) $post_id;

        $by      = ( isset( $by ) and is_string( $by ) ) ? $by : Post::get_byline_prefix();
        $link    = apply_filters( 'molongui_contributors/link_author_name_to_archive', true );
        if ( function_exists( 'authorship_get_byline' ) )
        {
            $the_byline  = '<span class="molongui-post-author__by">';
            $the_byline .= $by;
            $the_byline .= '</span>';
            $the_byline .= '&nbsp;';
            $the_byline .= '<span class="molongui-post-author__name" itemprop="author" itemtype="https://schema.org/Person" itemscope="">';
            $the_byline .= authorship_get_byline( $post_id, null, null, $link );
            $the_byline .= '</span>';
        }
        elseif ( $link and function_exists( 'coauthors_posts_links' ) )
        {
            $the_byline = coauthors_posts_links( null, null, null, null, false );
        }
        elseif ( function_exists( 'coauthors' ) )
        {
            $the_byline = coauthors( null, null, null, null, false );
        }
        else
        {
            $the_byline  = '<span class="molongui-post-author__by">';
            $the_byline .= $by;
            $the_byline .= '</span>';
            $the_byline .= '&nbsp;';
            $the_byline .= '<span class="molongui-post-author__name" itemprop="author" itemtype="https://schema.org/Person" itemscope="">';
            $the_byline .= /*$link;//*/( $link ? get_the_author_posts_link() : get_the_author() );
            $the_byline .= '</span>';
        }

        /*!
         * FILTER HOOK
         *
         * Allows filtering the post byline.
         *
         * @param string $the_byline The markup to display the post byline.
         * @param int    $post_id    The post id.
         * @since 1.6.0
         */
        return apply_filters( 'molongui_contributors/get_the_byline', $the_byline, $post_id );
    }
    public static function the_byline( $post_id = null, $by = null )
    {
        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup to display the post byline.
         *
         * @param string $the_byline The post byline to display.
         * @since 1.6.0
         */
        echo wp_kses_post( apply_filters( 'molongui_contributors/the_byline', self::get_the_byline( $post_id, $by ) ) );
    }
    public static function get_the_contributor( $post_id = 0 )
    {
        $post_contributor = Post::get_contributors( $post_id );
        if ( empty( $post_contributor ) )
        {
            return '';
        }

        /*!
         * FILTER HOOK
         *
         * Allows the markup displaying the contributor for a post to be returned early.
         *
         * Returning a non-null value will effectively short-circuit this function, passing the value through the
         * {@see 'molongui_contributors/pre_get_the_contributor'} filter and returning early.
         *
         * @param string|null $the_contributor  The contributor string. Default null.
         * @param object      $post_contributor The post contributor.
         * @param int         $post_id          The post ID.
         * @since 1.6.0
         */
        $the_contributor = apply_filters( 'molongui_contributors/pre_get_the_contributor', null, $post_contributor, $post_id );

        if ( !is_null( $the_contributor ) )
        {
            /*! This filter is documented at the end of this function. */
            return apply_filters( 'molongui_contributors/get_the_contributor', $the_contributor, $post_contributor );
        }
        $before_begin_markup = apply_filters( 'molongui_contributors/before_begin_the_contributor', '' );
        $open_tag_markup     = apply_filters( 'molongui_contributors/open_tag_the_contributor', '<div class="molongui-meta-contributor" itemprop="contributor" itemscope="" itemtype="https://schema.org/Person">' );
        $after_begin_markup  = apply_filters( 'molongui_contributors/after_begin_the_contributor', '' );
        $before_end_markup   = apply_filters( 'molongui_contributors/before_end_the_contributor', '' );
        $close_tag_markup    = apply_filters( 'molongui_contributors/close_tag_the_contributor', '</div>' );
        $after_end_markup    = apply_filters( 'molongui_contributors/after_end_the_contributor', '' );
        $separator           = apply_filters( 'molongui_contributors/separator_between_role_and_name', '&nbsp;' );

        $role_markup = self::get_the_contributor_role( $post_contributor->post_role_name );
        $name_markup = self::get_the_contributor_name( $post_contributor );

        $the_contributor = $before_begin_markup . $open_tag_markup . $after_begin_markup . $role_markup . $separator . $name_markup . $before_end_markup . $close_tag_markup . $after_end_markup;

        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup displaying the contributor to the post.
         *
         * @param string $the_contributor  The contributor markup.
         * @param object $post_contributor The post contributor.
         * @param int    $post_id          The post ID.
         * @since 1.6.0
         */
        return apply_filters( 'molongui_contributors/get_the_contributor', $the_contributor, $post_contributor, $post_id );
    }
    public static function the_contributor( $post_id = 0 )
    {
        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup to display the post contributor.
         *
         * @param string $the_contributor The post contributor to display.
         * @since 1.6.0
         */
        echo wp_kses_post( apply_filters( 'molongui_contributors/the_contributor', self::get_the_contributor( $post_id ) ) );
    }
    public static function get_the_contributor_role( $role )
    {
        $before_begin_role = apply_filters( 'molongui_contributors/before_begin_contributor_role', '', $role );
        $open_tag_role     = apply_filters( 'molongui_contributors/open_tag_contributor_role', '<span class="molongui-post-contributor__by">', $role );
        $after_begin_role  = apply_filters( 'molongui_contributors/after_begin_contributor_role', '', $role );
        $before_end_role   = apply_filters( 'molongui_contributors/before_end_contributor_role', '&nbsp;', $role );
        $close_tag_role    = apply_filters( 'molongui_contributors/close_tag_contributor_role', '</span>', $role );
        $after_end_role    = apply_filters( 'molongui_contributors/after_end_contributor_role', '', $role );

        /*! // translators: %s: The contributor role name. */
        $contributor_role = sprintf( _x( '%s', 'The contributor role', 'molongui-post-contributors' ), Contributor_Role::get_role_leading_phrase( $role ) );

        return $before_begin_role . $open_tag_role . $after_begin_role . $contributor_role . $before_end_role . $close_tag_role . $after_end_role;
    }
    public static function the_contributor_role( $role )
    {
        echo wp_kses_post( self::get_the_contributor_role( $role ) );
    }
    public static function get_the_contributor_name( $contributor )
    {
        $before_begin_name = apply_filters( 'molongui_contributors/before_begin_contributor_name', '' );
        $open_tag_name     = apply_filters( 'molongui_contributors/open_tag_contributor_name', '<span class="molongui-post-contributor__name" itemprop="name">' );
        $after_begin_name  = apply_filters( 'molongui_contributors/before_contributor_name', '' );
        $before_end_name   = apply_filters( 'molongui_contributors/before_end_contributor_name', '' );
        $close_tag_name    = apply_filters( 'molongui_contributors/close_tag_contributor_name', '</span>' );
        $after_end_name    = apply_filters( 'molongui_contributors/after_end_contributor_name', '' );

        if ( 'wpuser' === $contributor->type and apply_filters( 'molongui_contributors/link_contributor_name_to_archive', true ) )
        {
            $display_name = '<a href="' . esc_url( get_author_posts_url( $contributor->ID, $contributor->user_nicename ) ) . '" title="' . sprintf( esc_html__( 'Posts by %s' ), $contributor->display_name ) . '" itemprop="url">' . $contributor->display_name . '</a>';
        }
        else
        {
            $display_name = $contributor->display_name;
        }

        return $before_begin_name . $open_tag_name . $after_begin_name . $display_name . $before_end_name . $close_tag_name . $after_end_name;
    }
    public static function the_contributor_name( $contributor )
    {
        echo wp_kses_post( self::get_the_contributor_name( $contributor ) );
    }
    public static function get_the_meta( $post_id = null, $layout = null, $settings = array() )
    {
        if ( !Post::is_post_type_enabled() )
        {
            return '';
        }
        $post_id = (int) $post_id;
        if ( empty( $post_id ) )
        {
            $post_id = Post::get_id();

            if ( empty( $post_id ) )
            {
                return '';
            }
        }

        $is_preview = Helpers::is_edit_mode();
        $options    = Settings::get();
        $defaults   = Settings::get_defaults();
        $config     = array();
        foreach ( $defaults as $key => $value )
        {
            if ( isset( $settings['mpb_'.$key] )  )
            {
                $config[$key] = $settings['mpb_'.$key];
            }
            elseif ( isset( $options[$key] ) )
            {
                $config[$key] = $options[$key];
            }
            else
            {
                $config[$key] = $value;
            }
        }
        add_filter( 'molongui_contributors/byline_settings', function() use ( $config )
        {
            return $config;
        });
        Post::enqueue_byline_styles();
        if ( empty( $layout ) or !is_numeric( $layout ) )
        {
            $layout = !empty( $options['layout'] ) ? $options['layout'] : '1';
        }

        /*!
         * FILTER HOOK
         *
         * Allows filtering the post bylines layout.
         *
         * @param string $file   The file with the HTML markup to use to display the post bylines.
         * @param string $layout The layout to use. Default 1.
         * @since 1.0.0
         */
        $file = apply_filters( 'molongui_contributors/layout', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/layout-'.$layout.'.php', $layout );
        if ( !file_exists( $file ) )
        {
            $file = MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/layout-1.php';
        }
        Debug::console_log( $file, "Post byline layout being used is:" );

        /*!
         * FILTER HOOK
         *
         * Allows filtering the byline items to display and order.
         *
         * Depending on the layout, some items are displayed in a default location and cannot be relocated.
         * i.e.:
         *   layout-2: Post author and meta are displayed on the first line. The contributor on the second.
         *   layout-3: The author and the contributor are always displayed on the first line. Post meta, on the second.
         *
         * @param array $items   The byline items to display by default.
         * @param int   $post_id The post ID.
         * @param int   $layout  The layout to use.
         * @since 1.0.0
         */
        $items = apply_filters( 'molongui_contributors/items', array
        (
            'author',
            'contributor',
            'publish-date',
            'update-date',
            'categories',
            'tags',
            'comment-link',
        ), $post_id, $layout );
        $byline_items = array
        (
            'author' => array
            (
                'condition' => $config['show_author'],
                'template'  => apply_filters( 'molongui_contributors/post_byline_author_template', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/parts/author.php' ),
            ),
            'contributor' => array
            (
                'condition' => !empty( $config['show_contributors'] ) and ( Post::has_contributors() or $is_preview ),
                'template'  => apply_filters( 'molongui_contributors/post_byline_contributor_template', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/parts/contributor.php' ),
            ),
            'publish-date' => array
            (
                'condition' => !empty( $config['show_publish_date'] ),
                'template'  => apply_filters( 'molongui_contributors/post_byline_publish_date_template', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/parts/publish-date.php' ),
            ),
            'update-date' => array
            (
                'condition' => !empty( $config['show_update_date'] ),
                'template'  => apply_filters( 'molongui_contributors/post_byline_update_date_template', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/parts/update-date.php' ),
            ),
            'categories' => array
            (
                'condition' => !empty( $config['show_categories'] ) and ( has_category() or $is_preview ),
                'template'  => apply_filters( 'molongui_contributors/post_byline_categories_template', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/parts/categories.php' ),
            ),
            'tags' => array
            (
                'condition' => !empty( $config['show_tags'] ) and ( has_tag() or $is_preview ),
                'template'  => apply_filters( 'molongui_contributors/post_byline_tags_template', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/parts/tags.php' ),
            ),
            'comment-link' => array
            (
                'condition' => !empty( $config['show_comment_link'] ) and ( ( !post_password_required() and ( comments_open() or get_comments_number() ) ) or $is_preview ) ,
                'template'  => apply_filters( 'molongui_contributors/post_byline_comment_link_template', MOLONGUI_CONTRIBUTORS_DIR . 'templates/byline/parts/comment-link.php' ),
            ),
        );
        $elements     = 0;
        $separator    = apply_filters( 'molongui_contributors/separator', Helpers::space_to_nbsp( $config['separator'] ) );
        $byline_align = apply_filters( 'molongui_contributors/alignment', $config['alignment'] );
        $byline_color = apply_filters( 'molongui_contributors/text_color', $config['text_color'] );
        $byline_small = apply_filters( 'molongui_contributors/smaller_text', !empty( $config['byline_small'] ) ? 'smaller' : 'inherit;' );
        $author_color = apply_filters( 'molongui_contributors/author_text_color', $config['author_text_color'] );
        $author_bold  = apply_filters( 'molongui_contributors/author_bold', !empty( $config['highlight_author'] ) ? 'bold' : 'inherit;' );
        $generator    = apply_filters( 'molongui_contributors/byline_generator', '' );

        remove_all_filters( 'molongui_contributors/byline_generator' );

        ob_start();
        include $file;
        if ( $is_preview )
        {
            ?>
            <style>
                <?php //echo wp_strip_all_tags( Post::byline_extra_styles() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php echo wp_strip_all_tags( apply_filters( 'molongui_contributors/byline_extra_styles', '' ) ); ?>
            </style>
            <?php
            Debug::console_log( null, "Custom CSS inlined for the visual editor." );
        }
        $the_meta = ob_get_clean();

        /*!
         * FILTER HOOK
         *
         * Allows filtering the post meta.
         *
         * @param string $the_meta The post meta to return.
         * @param int    $post_id  The post id.
         * @param int    $layout   The layout to use. Default 1.
         * @param array  $config   Global plugin settings overrides. Each setting must be prefixed by 'mpb_'. Default empty array.
         * @since 1.6.0
         */
        return apply_filters( 'molongui_contributors/get_the_meta', $the_meta, $post_id, $layout, $config );
    }
    public static function the_meta( $post_id = null, $layout = null, $settings = array() )
    {
        add_filter( 'wp_kses_allowed_html', array( __CLASS__, 'custom_wp_kses_allowed_html' ) );

        /*!
         * FILTER HOOK
         *
         * Allows filtering the markup to display the post meta.
         *
         * @param string $the_meta The post meta to display.
         * @since 1.6.0
         */
        echo wp_kses_post( apply_filters( 'molongui_contributors/the_meta', self::get_the_meta( $post_id, $layout, $settings ) ) );

        remove_filter( 'wp_kses_allowed_html', array( __CLASS__, 'custom_wp_kses_allowed_html' ) );
    }
    public static function the_content()
    {
        do_action( 'molongui_contributors/before_post_content' ); ?>

        <div class="<?php echo esc_attr( apply_filters( 'molongui_contributors/post_content_class', 'molongui-post-content' ) ); ?>">
            <?php do_action( 'molongui_contributors/before_the_content' ); ?>
            <?php the_content(); ?>
            <?php do_action( 'molongui_contributors/after_the_content' ); ?>
        </div>

        <?php do_action( 'molongui_contributors/after_post_content' );
    }
    public static function the_taxonomies()
    {
        /*!
         * Allows the taxonomies markup for a post to be returned early.
         *
         * Returning a non-null value will effectively short-circuit Template::the_post_taxonomies(), displaying the
         * sanitized markup and returning early.
         *
         * @since 1.0.0
         */
        $output = apply_filters( 'molongui_contributors/pre_the_post_taxonomies', null );

        if ( !is_null( $output ) )
        {
            echo wp_kses_post( $output );
            return;
        }

        $options = Settings::get();

        if ( !empty( $options['post_categories'] ) or !empty( $options['post_tags'] ) )
        {
            echo '<div class="' . esc_attr( apply_filters( 'molongui_contributors/post_taxonomies_class', 'molongui-post-taxonomies' ) ) . '">';

            if ( !empty( $options['post_categories'] ) )
            {
                $categories_list = get_the_category_list( wp_get_list_item_separator() );
                if ( $categories_list )
                {
                    printf(
                        /*! // translators: %s: List of categories. */
                        '<span class="cat-links">' . esc_html__( "Categorized as %s", 'molongui-post-contributors' ) . ' </span>',
                        wp_kses_post( $categories_list )
                    );
                }
            }

            if ( !empty( $options['post_tags'] ) )
            {
                $tags_list = get_the_tag_list( '', wp_get_list_item_separator() );
                if ( $tags_list )
                {
                    printf(
                        /*! // translators: %s: List of tags. */
                        '<span class="tags-links">' . esc_html__( "Tagged %s", 'molongui-post-contributors' ) . '</span>',
                        wp_kses_post( $tags_list )
                    );
                }
            }

            echo '</div>';
        }
    }
    public static function the_sharing()
    {
        $networks = apply_filters( 'molongui_contributors/social_share_networks', array
        (
            'facebook' => array
            (
                'title'  => __( "Facebook", 'molongui-post-contributors' ),
                'url'    => sprintf( 'https://www.facebook.com/sharer/sharer.php?u=%s', get_permalink() ),
                'paths'  => array( 'M17.12 0.224v4.704h-2.784q-1.536 0-2.080 0.64t-0.544 1.92v3.392h5.248l-0.704 5.28h-4.544v13.568h-5.472v-13.568h-4.544v-5.28h4.544v-3.904q0-3.328 1.856-5.152t4.96-1.824q2.624 0 4.064 0.224z' ),
                'width'  => 18,
                'height' => 32,
                'color'  => '#3b5998',
            ),
            'x' => array
            (
                'title'  => __( "X", 'molongui-post-contributors' ),
                'url'    => sprintf( 'https://twitter.com/intent/tweet?url=%s', get_permalink() ),
                'paths'  => array( 'M30.3 29.7L18.5 12.4l0 0L29.2 0h-3.6l-8.7 10.1L10 0H0.6l11.1 16.1l0 0L0 29.7h3.6l9.7-11.2L21 29.7H30.3z M8.6 2.7 L25.2 27h-2.8L5.7 2.7H8.6z' ),
                'width'  => 32,
                'height' => 30,
                'color'  => 'black',
            ),
            'pinterest' => array
            (
                'title'  => __( "Pinterest", 'molongui-post-contributors' ),
                'url'    => sprintf( 'http://pinterest.com/pin/create/button/?url=%s&media=%s',
                    get_permalink(),
                    get_the_post_thumbnail_url()
                ),
                'paths'  => array( 'M0 10.656q0-1.92 0.672-3.616t1.856-2.976 2.72-2.208 3.296-1.408 3.616-0.448q2.816 0 5.248 1.184t3.936 3.456 1.504 5.12q0 1.728-0.32 3.36t-1.088 3.168-1.792 2.656-2.56 1.856-3.392 0.672q-1.216 0-2.4-0.576t-1.728-1.568q-0.16 0.704-0.48 2.016t-0.448 1.696-0.352 1.28-0.48 1.248-0.544 1.12-0.832 1.408-1.12 1.536l-0.224 0.096-0.16-0.192q-0.288-2.816-0.288-3.36 0-1.632 0.384-3.68t1.184-5.152 0.928-3.616q-0.576-1.152-0.576-3.008 0-1.504 0.928-2.784t2.368-1.312q1.088 0 1.696 0.736t0.608 1.824q0 1.184-0.768 3.392t-0.8 3.36q0 1.12 0.8 1.856t1.952 0.736q0.992 0 1.824-0.448t1.408-1.216 0.992-1.696 0.672-1.952 0.352-1.984 0.128-1.792q0-3.072-1.952-4.8t-5.12-1.728q-3.552 0-5.952 2.304t-2.4 5.856q0 0.8 0.224 1.536t0.48 1.152 0.48 0.832 0.224 0.544q0 0.48-0.256 1.28t-0.672 0.8q-0.032 0-0.288-0.032-0.928-0.288-1.632-0.992t-1.088-1.696-0.576-1.92-0.192-1.92z' ),
                'width'  => 23,
                'height' => 32,
                'color'  => '#C8232C',
            ),
            'linkedin' => array
            (
                'title'  => __( "LinkeIn", 'molongui-post-contributors' ),
                'url'    => sprintf( 'http://www.linkedin.com/shareArticle?mini=true&url=%s', get_permalink() ),
                'paths'  => array( 'M6.24 11.168v17.696h-5.888v-17.696h5.888zM6.624 5.696q0 1.312-0.928 2.176t-2.4 0.864h-0.032q-1.472 0-2.368-0.864t-0.896-2.176 0.928-2.176 2.4-0.864 2.368 0.864 0.928 2.176zM27.424 18.72v10.144h-5.856v-9.472q0-1.888-0.736-2.944t-2.272-1.056q-1.12 0-1.856 0.608t-1.152 1.536q-0.192 0.544-0.192 1.44v9.888h-5.888q0.032-7.136 0.032-11.552t0-5.28l-0.032-0.864h5.888v2.56h-0.032q0.352-0.576 0.736-0.992t0.992-0.928 1.568-0.768 2.048-0.288q3.040 0 4.896 2.016t1.856 5.952z' ),
                'width'  => 27,
                'height' => 32,
                'color'  => '#0E76A8',
            ),
            'rss' => array
            (
                'title'  => __( "RSS", 'molongui-post-contributors' ),
                'url'    => sprintf( '%s?feed=rss2&withoutcomments=1', get_permalink() ),
                'paths'  => array( 'M5.5 12a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-3-8.5a1 1 0 0 1 1-1c5.523 0 10 4.477 10 10a1 1 0 1 1-2 0 8 8 0 0 0-8-8 1 1 0 0 1-1-1m0 4a1 1 0 0 1 1-1 6 6 0 0 1 6 6 1 1 0 1 1-2 0 4 4 0 0 0-4-4 1 1 0 0 1-1-1' ),
                'width'  => 16,
                'height' => 16,
                'color'  => '#EE802F',
            ),
            'email' => array
            (
                'title'  => __( "Email", 'molongui-post-contributors' ),
                'url'    => sprintf( 'mailto:?&subject=%s&body=%s', rawurlencode( get_the_title() ), get_permalink() ),
                'paths'  => array( 'M18.56 17.408l8.256 8.544h-25.248l8.288-8.448 4.32 4.064zM2.016 6.048h24.32l-12.16 11.584zM20.128 15.936l8.224-7.744v16.256zM0 24.448v-16.256l8.288 7.776z' ),
                'width'  => 28,
                'height' => 32,
                'color'  => '#0da20d',
            ),
        ));
        $layout = 1;
        $file   = apply_filters( 'molongui_contributors/social_share_layout', MOLONGUI_CONTRIBUTORS_DIR . 'templates/post/parts/social-share/layout-'.$layout.'.php', $layout );
        if ( file_exists( $file ) )
        {
            include $file;
        }
    }
    public static function the_navigation()
    {
        $prev_text = apply_filters( 'molongui_contributors/previous_post', _x( "Previous post", 'Post navigation', 'molongui-post-contributors' ) );
        $next_text = apply_filters( 'molongui_contributors/next_post', _x( "Next post", 'Post navigation', 'molongui-post-contributors' ) );

        $prev_text = !empty( $prev_text ) ? '<span>' . $prev_text . '</span><br>' : '';
        $next_text = !empty( $next_text ) ? '<span>' . $next_text . '</span><br>' : '';

        the_post_navigation( array
        (
            'class'     => 'molongui-post-navigation',
            'prev_text' => $prev_text . '%title',
            'next_text' => $next_text . '%title',
        ) );
    }
    public static function the_related()
    {
    }
    public static function the_comments()
    {
        if ( ( comments_open() or get_comments_number() ) and !post_password_required() )
        {
            comments_template();
        }
    }
    public static function the_sidebar()
    {
        $class[] = 'sidebar widget-area';
        $class = apply_filters( 'molongui_contributors/sidebar_class', $class );
        ?>

        <aside id="secondary" class="<?php echo esc_attr( implode( ' ', $class ) ); ?>" role="complementary">
            <?php
            if ( !function_exists( 'elementor_theme_do_location' ) or !elementor_theme_do_location( 'main-sidebar' ) )
            {
                $sidebar = apply_filters( 'molongui_contributors/sidebar_index', 'sidebar-1' );

                do_action( 'molongui_contributors/before_sidebar' );

                if ( is_active_sidebar( $sidebar ) )
                {
                    dynamic_sidebar( $sidebar );
                }

                do_action( 'molongui_contributors/after_sidebar' );
            }
            ?>
        </aside>
        <?php
    }
    public static function custom_wp_kses_allowed_html( $allowedtags )
    {
        $allowedtags['style'] = array();
        $allowedtags['time'] = array
        (
            'class'    => true,
            'datetime' => true,
            'itemprop' => true,
        );

        return $allowedtags;
    }

} // class
new Template();