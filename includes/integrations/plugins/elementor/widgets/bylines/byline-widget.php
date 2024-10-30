<?php

namespace Molongui\Contributors\Integrations\Plugins\Elementor\Widgets\Bylines;

use Molongui\Contributors\Common\Modules\Settings;
use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Post;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Byline_Widget
{
    public function __construct()
    {
        add_action( 'elementor/widgets/widgets_registered', array( $this, 'register' ) );
        add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'custom_assets' ) );
        add_action( 'wp_ajax_mpb__elementor_fetch_posts_for_control', array( $this, 'fetch_posts' ) );
    }
    public function add_custom_widget_category( $elements_manager )
    {
        $elements_manager->add_category(
            'custom-widgets',
            array
            (
                'title' => __( "Custom Widgets", 'molongui-post-contributors' ),
                'icon'  => 'font',
            )
        );
    }
    public function register( $widgets_manager )
    {
        global $post;
        if ( Post::is_post_type_enabled() )
        {
            $widgets_manager->register_widget_type( new WidgetExtension() );
        }
    }
    public function custom_assets()
    {
        wp_enqueue_script( 'mpb-elementor-bylines-script', MOLONGUI_CONTRIBUTORS_URL . 'includes/integrations/plugins/elementor/widgets/bylines/widget.6d5f.min.js', array( 'jquery', 'elementor-editor' ), MOLONGUI_CONTRIBUTORS_VERSION, true );
        wp_localize_script( 'mpb-elementor-bylines-script', 'mpbElementorBylines', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        $custom_css = "
        .select2-container .select2-selection--single .clear-selected
        {
            margin-right: 5px;
            font-weight: bold;
            color: var(--e-a-btn-bg-primary, red);
            cursor: pointer;
        }
    ";
        wp_add_inline_style( 'elementor-editor', $custom_css );
    }
    public function fetch_posts()
    {
        $search_query = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        $query_args = array
        (
            's'              => $search_query,
            'post_type'      => 'post',
            'posts_per_page' => -1,
        );

        $query   = new \WP_Query( $query_args );
        $results = array();

        if ( $query->have_posts() )
        {
            while ( $query->have_posts() )
            {
                $query->the_post();
                $results[] = array
                (
                    'id'   => get_the_ID(),
                    'text' => get_the_title(),
                );
            }
        }

        wp_send_json( $results );
    }

} // class
add_action( 'elementor/init', function()
{
    class WidgetExtension extends \Elementor\Widget_Base
    {
        public function get_name()
        {
            return 'mpb_bylines_widget';
        }
        public function get_title()
        {
            return __( "Post Bylines", 'molongui-post-contributors' );
        }
        public function get_icon()
        {
            return 'eicon-align-start-h'; //'eicon-pencil'; 'eicon-chevron-double-right'; 'eicon-filter'; 'eicon-post-content'; 'fa fa-signature'; 'fa fa-grip-lines';
        }
        public function get_categories()
        {
            return array( 'basic', 'general' );
        }
        protected function _register_controls()
        {
            $options = Settings::get();
            $this->start_controls_section(
                'mpb_byline_section',
                array
                (
                    'label' => __( "Byline", 'molongui-post-contributors' ),
                    'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                )
            );
            $layouts = apply_filters( 'molongui_contributors/elementor_byline_layout_options', array
            (
                '1' => __( "Layout 1", 'molongui-post-contributors' ),
                '2' => __( "Layout 2", 'molongui-post-contributors' ),
                '3' => __( "Layout 3", 'molongui-post-contributors' ),
                '4' => __( "Layout 4", 'molongui-post-contributors' ),
                '5' => __( "Layout 5", 'molongui-post-contributors' ),
            ));

            $this->add_control(
                'mpb_layout',
                array
                (
                    'label'   => __( "Layout", 'molongui-post-contributors' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'options' => $layouts,
                    'default' => '1',
                )
            );

            $this->add_control(
                'mpb_byline_section_hr_1',
                array
                (
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                )
            );

            $this->add_control(
                'important_note',
                array
                (
                    'label'      => '',
                    'type'       => \Elementor\Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'separator'  => 'after',
                    /*! // translators: %1$s: The <span> tag with some inline styling. %2$s: </span>. */
                    'raw'        => sprintf( esc_html__( "%1\$sByline information is retrieved for the current post. For preview purposes, some dummy information might be displayed here but it won't be added in the frontend.%2\$s", 'molongui-post-contributors' ), '<span style="line-height:1.4">', '</span>' ),
                )
            );
            $this->add_control(
                'mpb_show_author',
                array
                (
                    'label'        => __( "Author", 'molongui-post-contributors' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( "Show", 'molongui-post-contributors' ),
                    'label_off'    => __( "Hide", 'molongui-post-contributors' ),
                    'return_value' => 'yes', // Return value when it is switched to "on"
                    'default'      => 'yes',
                )
            );
            $this->add_control(
                'mpb_show_contributors',
                array
                (
                    'label'        => __( "Contributors", 'molongui-post-contributors' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( "Show", 'molongui-post-contributors' ),
                    'label_off'    => __( "Hide", 'molongui-post-contributors' ),
                    'return_value' => 'yes', // Return value when it is switched to "on"
                    'default'      => 'yes',
                )
            );
            $this->add_control(
                'mpb_show_publish_date',
                array
                (
                    'label'        => __( "Publish Date", 'molongui-post-contributors' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( "Show", 'molongui-post-contributors' ),
                    'label_off'    => __( "Hide", 'molongui-post-contributors' ),
                    'return_value' => 'yes', // Return value when it is switched to "on"
                    'default'      => 'yes',
                )
            );
            $this->add_control(
                'mpb_show_update_date',
                array
                (
                    'label'        => __( "Update Date", 'molongui-post-contributors' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( "Show", 'molongui-post-contributors' ),
                    'label_off'    => __( "Hide", 'molongui-post-contributors' ),
                    'return_value' => 'yes', // Return value when it is switched to "on"
                    'default'      => '',
                )
            );
            $this->add_control(
                'mpb_show_categories',
                array
                (
                    'label'        => __( "Categories", 'molongui-post-contributors' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( "Show", 'molongui-post-contributors' ),
                    'label_off'    => __( "Hide", 'molongui-post-contributors' ),
                    'return_value' => 'yes', // Return value when it is switched to "on"
                    'default'      => '',
                )
            );
            $this->add_control(
                'mpb_show_tags',
                array
                (
                    'label'        => __( "Tags", 'molongui-post-contributors' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( "Show", 'molongui-post-contributors' ),
                    'label_off'    => __( "Hide", 'molongui-post-contributors' ),
                    'return_value' => 'yes', // Return value when it is switched to "on"
                    'default'      => '',
                )
            );
            $this->add_control(
                'mpb_show_comment_link',
                array
                (
                    'label'        => __( "Comment Link", 'molongui-post-contributors' ),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'     => __( "Show", 'molongui-post-contributors' ),
                    'label_off'    => __( "Hide", 'molongui-post-contributors' ),
                    'return_value' => 'yes', // Return value when it is switched to "on"
                    'default'      => '',
                )
            );

            $this->add_control(
                'mpb_byline_section_hr_2',
                array
                (
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                )
            );
            $this->add_control(
                'mpb_separator',
                array
                (
                    'label'       => esc_html__( "Separator", 'molongui-post-contributors' ),
                    'type'        => \Elementor\Controls_Manager::SELECT,
                    'description' => esc_html__( "The character to use between byline items. You can define a custom string using the option in the plugin settings page and selecting the 'Default' option here.", 'molongui-post-contributors' ),
                    'condition'   => array
                    (
                        'mpb_layout' => [ '1', '2', '3', '4' ],
                    ),
                    'default'     => '&#xFF5C;',
                    'options'     => array
                    (
                        $options['separator']  => esc_html__( "Default", 'molongui-post-contributors' ),
                        '&hybull;' => '&hybull;',
                        '&dash;'   => '&dash;',
                        '&ndash;'  => '&ndash;',
                        '&mdash;'  => '&mdash;',
                        '&bull;'   => '&bull;',
                        '&gt;'     => '&gt;',
                        '&lt;'     => '&lt;',
                        '&raquo;'  => '&raquo;',
                        '&laquo;'  => '&laquo;',
                        ','        => ',',
                        ';'        => ';',
                        '.'        => '.',
                        ':'        => ':',
                        '/'        => '/',
                        '//'       => '//',
                        '&brvbar;' => '&brvbar;',
                        '&#x2758;' => '&#x2758;',
                        '&#x2759;' => '&#x2759;',
                        '&#x275A;' => '&#x275A;',
                        '&#x7c;'   => '&#x7c;',
                        '&#x2016;' => '&#x2016;',
                        '&#xFF5C;' => '&#xFF5C;',
                        '&#x2734;' => '&#x2734;',
                    ),
                )
            );
            $this->end_controls_section();
            $this->start_controls_section(
                'mpb_strings_section',
                array
                (
                    'label' => __( "Strings", 'molongui-post-contributors' ),
                    'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                )
            );
            $this->add_control(
                'mpb_by',
                array
                (
                    'label'       => esc_html__( "Author", 'molongui-post-contributors' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => esc_html__( "Written by", 'molongui-post-contributors' ),
                    'placeholder' => esc_html__( "Written by", 'molongui-post-contributors' ),
                )
            );
            $this->add_control(
                'mpb_published_on',
                array
                (
                    'label'       => esc_html__( "Publish Date", 'molongui-post-contributors' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => esc_html__( "Published on", 'molongui-post-contributors' ),
                    'placeholder' => esc_html__( "Published on", 'molongui-post-contributors' ),
                )
            );
            $this->add_control(
                'mpb_updated_on',
                array
                (
                    'label'       => esc_html__( "Update Date", 'molongui-post-contributors' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => esc_html__( "Updated on", 'molongui-post-contributors' ),
                    'placeholder' => esc_html__( "Updated on", 'molongui-post-contributors' ),
                )
            );
            $this->add_control(
                'mpb_in',
                array
                (
                    'label'       => esc_html__( "Categories Prefix", 'molongui-post-contributors' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'default'     => esc_html__( "In", 'molongui-post-contributors' ),
                    'placeholder' => esc_html__( "In", 'molongui-post-contributors' ),
                )
            );

            $this->end_controls_section();
            $this->start_controls_section(
                'mpb_items_section',
                array
                (
                    'label' => __( "Items", 'molongui-post-contributors' ),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                )
            );
            $this->add_control(
                'mpb_alignment',
                array
                (
                    'label'   => __( "Alignment", 'molongui-post-contributors' ),
                    'type'    => \Elementor\Controls_Manager::CHOOSE,
                    'options' => array
                    (
                        'start' => array
                        (
                            'title' => __( "Left", 'molongui-post-contributors' ),
                            'icon'  => 'eicon-text-align-left',
                        ),
                        'center' => array
                        (
                            'title' => __( "Center", 'molongui-post-contributors' ),
                            'icon'  => 'eicon-text-align-center',
                        ),
                        'end' => array
                        (
                            'title' => __( "Right", 'molongui-post-contributors' ),
                            'icon'  => 'eicon-text-align-right',
                        ),
                    ),
                    'default'   => 'start',
                    'selectors' => array
                    (
                        '{{WRAPPER}} .molongui-post-byline > div' => 'align-items: {{VALUE}}; justify-content: {{VALUE}};',
                    ),
                )
            );

            $this->add_control(
                'mpb_items_section_hr_1',
                array
                (
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                )
            );
            $this->add_control(
                'mpb_text_color',
                array
                (
                    'label'     => __( "Text Color", 'molongui-post-contributors' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#666666', // Default color
                    'selectors' => array
                    (
                        '{{WRAPPER}} .molongui-post-byline > div' => 'color: {{VALUE}};',
                    ),
                )
            );
            $this->add_control(
                'mpb_author_text_color',
                array
                (
                    'label'     => __( "Author Name Color", 'molongui-post-contributors' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'default'   => '#000000', // Default color
                    'selectors' => array
                    (
                        '{{WRAPPER}} .molongui-post-byline .molongui-post-author' => 'color: {{VALUE}};',
                    ),
                    'condition' => array
                    (
                        'mpb_show_author' => 'yes',
                    ),
                )
            );

            $this->add_control(
                'mpb_items_section_hr_2',
                array
                (
                    'type' => \Elementor\Controls_Manager::DIVIDER,
                )
            );
            $this->add_control(
                'mpb_highlight_author',
                array
                (
                    'label'                => __( "Highlight Author", 'molongui-post-contributors' ),
                    'type'                 => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'             => __( "ON", 'molongui-post-contributors' ),
                    'label_off'            => __( "OFF", 'molongui-post-contributors' ),
                    'return_value'         => 'yes', // Return value when it is switched to "on"
                    'default'              => 'yes',
                    'selectors_dictionary' => array
                    (
                        ''    => 'normal',
                        'yes' => 'bold',
                    ),
                    'selectors' => array
                    (
                        '{{WRAPPER}} .molongui-post-byline .molongui-post-author' => 'font-weight: {{VALUE}};',
                    ),
                    'condition' => array
                    (
                        'mpb_show_author' => 'yes',
                    ),
                )
            );
            $this->add_control(
                'mpb_byline_small',
                array
                (
                    'label'                => __( "Byline items smaller", 'molongui-post-contributors' ),
                    'type'                 => \Elementor\Controls_Manager::SWITCHER,
                    'label_on'             => __( "ON", 'molongui-post-contributors' ),
                    'label_off'            => __( "OFF", 'molongui-post-contributors' ),
                    'return_value'         => 'yes', // Return value when it is switched to "on"
                    'default'              => 'yes',
                    'selectors_dictionary' => array
                    (
                        ''    => 'unset',
                        'yes' => 'smaller',
                    ),
                    'selectors' => array
                    (
                        '{{WRAPPER}} .molongui-post-byline .molongui-post-byline__row > div:not(.molongui-post-author)' => 'font-size: {{VALUE}};',
                    ),
                )
            );

            $this->end_controls_section();
        }
        protected function render()
        {
            $settings = $this->get_settings_for_display();
            if ( !isset( $settings['post_id'] ) )
            {
                global $post;
                $settings['post_id'] = $post->ID;
            }
            add_filter( 'molongui_contributors/byline_generator', function()
            {
                return 'elementor';
            });

            if ( class_exists('\Molongui\Contributors\Pro\Template' ) )
            {
                \Molongui\Contributors\Pro\Template::the_meta( $settings['post_id'], $settings['mpb_layout'], $settings );
            }
            else
            {
                \Molongui\Contributors\Template::the_meta( $settings['post_id'], $settings['mpb_layout'], $settings );
            }
        }
        protected function _content_template()
        {
        }
    }
});
