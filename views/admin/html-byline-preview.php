<?php

use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

?>

<div id="bylines-preview" style="margin:2em 0 0; padding:1em; border:1px dashed #c1c1c1; border-radius:4px">
    <?php Template::the_meta( $post_id ); ?>
</div>
<div>
    <p style="margin:6px 0; text-align:right; font-family: monospace; font-size:11px; color:gray">
        <?php /*! // translators: %s: The post ID. */ ?>
        <?php printf( esc_html_x( "Byline preview for post %s", 'Plugin settings page', 'molongui-post-contributors' ), esc_attr( $post_id ) ); ?> (<?php echo esc_html( get_the_title( $post_id ) ); ?>)
    </p>
</div>

<!--
<div style="margin:2em -24px -24px; padding:2em; background:#f7f7f7; border-top:1px dashed #ccd0d4">
    <span style="font-size:12px">Pick a post to preview its bylines:&nbsp;&nbsp;</span>
    <select name="post_id" id="post_id" style="font-size:12px; min-height:26px">
        <?php
        $posts = get_posts( array ( 'numberposts' => -1 ) );
        foreach( $posts as $post ) :
            ?>
            <option value="<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></option>
        <?php endforeach; ?>
    </select>
    <script>

    </script>
</div>
-->