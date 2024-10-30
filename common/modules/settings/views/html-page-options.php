<?php

use Molongui\Contributors\Common\Modules\Settings;
use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

$logo = file_exists( MOLONGUI_CONTRIBUTORS_DIR . 'assets/img/plugin_logo.png' ) ? MOLONGUI_CONTRIBUTORS_URL . 'assets/img/plugin_logo.png' : MOLONGUI_CONTRIBUTORS_URL . 'assets/img/common/masthead_logo.png';

?>

<div id="molongui-options">

    <?php do_action( 'molongui_contributors/options/before_masthead' ); ?>

    <!-- Page Header -->
    <div class="m-page-masthead">
        <div class="m-page-masthead__inside_container">
            <div class="m-page-masthead__logo-container">
                <a class="m-page-masthead__logo-link" href="<?php echo esc_url( MOLONGUI_CONTRIBUTORS_WEB ); ?>">
                    <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( MOLONGUI_CONTRIBUTORS_TITLE ); ?>" height="32">
                </a>
            </div>
            <div class="m-page-masthead__nav">
            <span class="m-buttons">
                <a id="m-button-save" class="m-button m-button-save is-compact is-primary" type="button"><?php echo esc_html__( "Save Settings", 'molongui-post-contributors' ); ?></a>
            </span>
            </div>
        </div><!-- !m-page-masthead -->
    </div><!-- !m-page-masthead -->

    <?php do_action( 'molongui_contributors/options/after_masthead' ); ?>

    <!-- Page Content -->
    <div class="m-page-content">

        <!-- Nav -->
        <div id="m-navigation" class="m-navigation">
            <div class="m-section-nav <?php echo ( empty( $tabs ) ? 'is-empty' : 'has-pinned-items' ); ?>">

                <div class="m-section-nav__mobile-header" role="button" tabindex="0">
                    <?php echo esc_html( $tabs[$current_tab]['name'] ); ?>
                </div>

                <div class="m-section-nav__panel">
                    <div class="m-section-nav-group">
                        <div class="m-section-nav-tabs">
                            <ul class="m-section-nav-tabs__list" role="menu">
                                <?php echo $nav_items; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Tabs -->
        <?php echo $div_contents; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <!-- Other stuff -->
        <?php wp_nonce_field( 'mfw_save_options_nonce', 'mfw_save_options_nonce' ); ?>

    </div><!-- !m-page-content -->

    <?php do_action( 'molongui_contributors/options/before_footer' ); ?>

    <?php
        $plugin_url    = MOLONGUI_CONTRIBUTORS_WEB;
        $help_url      = 'https://www.molongui.com/help/';
        $support_url   = $help_url . 'support/';
        $docs_url      = $help_url . 'docs/';
        $changelog_url = $help_url . MOLONGUI_CONTRIBUTORS_NAME . ( did_action( 'molongui_contributors_pro/loaded' ) ? '-pro' : '' ) . '-changelog/';
        $demo_url      = MOLONGUI_CONTRIBUTORS_DEMO;

        $args = array
        (
            'links' => array
            (
                array
                (
                    'label'   => __( "Pro", 'molongui-post-contributors' ) . " " . ( defined( 'MOLONGUI_CONTRIBUTORS_PRO_VERSION' ) ? MOLONGUI_CONTRIBUTORS_PRO_VERSION : '0.0.0' ),
                    'prefix'  => '<span class="m-page-footer__version">',
                    'suffix'  => '</span>',
                    'href'    => $plugin_url,
                    'display' => did_action( 'molongui_contributors_pro/loaded' ),
                ),
                array
                (
                    'label'   => __( "Free", 'molongui-post-contributors' ) . " " . MOLONGUI_CONTRIBUTORS_VERSION,
                    'prefix'  => '<span class="m-page-footer__version">',
                    'suffix'  => '</span>',
                    'href'    => $plugin_url,
                    'display' => true,
                ),
                array
                (
                    'label'   => __( "Changelog", 'molongui-post-contributors' ),
                    'prefix'  => '',
                    'suffix'  => '',
                    'href'    => $changelog_url,
                    'display' => true,
                ),
                array
                (
                    'label'   => __( "Docs", 'molongui-post-contributors' ),
                    'prefix'  => '',
                    'suffix'  => '',
                    'href'    => $help_url . MOLONGUI_CONTRIBUTORS_ID,
                    'display' => true,
                ),
                array
                (
                    'label'   => __( "Support", 'molongui-post-contributors' ),
                    'prefix'  => '',
                    'suffix'  => '',
                    'href'    => $support_url,
                    'display' => true,
                ),
                array
                (
                    'label'   => __( "Try Pro", 'molongui-post-contributors' ),
                    'prefix'  => '',
                    'suffix'  => '',
                    'href'    => $demo_url,
                    'display' => !did_action( 'molongui_contributors_pro/loaded' ),
                ),
                array
                (
                    'label'   => __( "Upgrade", 'molongui-post-contributors' ),
                    'prefix'  => '',
                    'suffix'  => '',
                    'href'    => $plugin_url.'pricing/',
                    'display' => !did_action( 'molongui_contributors_pro/loaded' ),
                ),
            ),
        );
    ?>

    <!-- Page Footer -->
    <div class="m-page-footer">

        <div class="m-page-footer__a8c-attr-container">
            <a href="<?php echo esc_url( MOLONGUI_CONTRIBUTORS_WEB ); ?>">
                <img src="<?php echo esc_url( MOLONGUI_CONTRIBUTORS_URL . 'common/assets/img/footer_logo.png' ); ?>" alt="Molongui" width="152" height="32">
            </a>
        </div>

        <?php if ( !empty( $args['links'] ) ) : ?>
            <ul class="m-page-footer__links">
                <?php foreach( $args['links'] as $link ) : ?>
                    <?php if ( $link['display'] ) : ?>
                        <li class="m-page-footer__link-item">
                            <a rel="noopener noreferrer" class="m-page-footer__link"
                               target="<?php echo empty( $link['target'] ) ? '_blank' : esc_attr( $link['target'] ); ?>"
                               title="<?php echo empty( $link['tip'] ) ? '' : esc_attr( $link['tip'] ); ?>"
                               href="<?php echo esc_url( $link['href'] ); ?>">
                                <?php echo wp_kses_post( $link['prefix'] ); ?>
                                <?php echo esc_html( $link['label'] ); ?>
                                <?php echo wp_kses_post( $link['suffix'] ); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div><!-- !m-page-footer -->

    <?php Settings::enqueue_scripts(); ?>
    <?php Settings::enqueue_styles();  ?>
    <?php Helpers::load_tidio(); ?>
    <?php do_action( 'molongui_contributors/options/after_footer' ); ?>

</div> <!-- #molongui-options -->

<div id="m-options-saving"><div class="m-loader"><div></div><div></div><div></div><div></div></div></div>
<div id="m-options-saved"><span class="dashicons dashicons-yes"></span><strong><?php esc_html_e( 'Saved', 'molongui-post-contributors' ); ?></strong></div>
<div id="m-options-error"><span class="dashicons dashicons-no"></span><strong><?php esc_html_e( 'Error', 'molongui-post-contributors' ); ?></strong></div>