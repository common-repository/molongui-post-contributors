<?php

use Molongui\Contributors\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
?>

<div class="molongui-post-byline" data-generator="<?php echo esc_attr( $generator ); ?>">
    <div class="molongui-post-byline--default-template molongui-post-byline--layout-<?php echo isset( $layout ) ? $layout : Settings::get( 'layout' ); ?>">
        <?php
        foreach( $items as $item )
        {
            if ( !empty( $byline_items[$item] ) )
            {
                if ( !empty( $byline_items[$item]['condition'] ) ) : ?>
                    <div class="molongui-post-byline__row molongui-post-byline__row--<?php echo $item; ?>">
                    <?php include $byline_items[$item]['template']; ?>
                    </div>
                <?php endif;
            }
        }
        ?>
    </div>
</div>