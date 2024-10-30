<?php

use Molongui\Contributors\Common\Utils\Icon;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
?>

<style>
    .molongui-post-share
    {
        clear: both;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 5px;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        line-height: 40px;
    }
    .molongui-post-share span
    {
        font-weight: 700;
        font-size: 13px;
    }
    .molongui-post-share div
    {
        display: flex;
        align-self: stretch;
    }
    .molongui-post-share a
    {
        min-width: 36px;
        display: flex;
        align-self: stretch;
        justify-content: center;
        align-items: center;
        text-align: center;
        border-radius: 0;
        border-color: transparent !important;
        color: #747474;
    }
</style>
<div class="<?php echo esc_attr( apply_filters( 'molongui_contributors/post_share_class', 'molongui-post-share' ) ); ?>">
    <span><?php echo esc_html( apply_filters( 'molongui_contributors/post_share_label', __( "Share this post", 'molongui-post-contributors' ) ) ); ?></span>
    <div>
        <?php
        add_filter( 'molongui_contributors/svg_icon_fill', function()
        {
            return 'fill=#747474';
        });
        foreach ( $networks as $id => $network )
        {
            ?>
            <style>
                .molongui-post-share .molongui-social-icon.<?php echo esc_attr( $id ); ?>:hover { <?php echo 'background-color:' . esc_html( $network['color'] ) . ';'; ?> }
                .molongui-post-share .molongui-social-icon.<?php echo esc_attr( $id ); ?>:hover path { fill:white; }
            </style>
            <?php
            printf(
                '<a href="%s" class="molongui-social-icon %s" data-toggle="tooltip" data-placement="top" title="%s" target="_blank">%s</a>'
                , esc_url( $network['url'] )
                , esc_attr( $id )
                , esc_html( $network['title'] )
                , Icon::get_svg( $network ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            );
        }
        ?>
    </div>
</div>