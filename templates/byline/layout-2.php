<?php

use Molongui\Contributors\Settings;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
$items = array_diff( $items, array( 'contributor' ) );
?>

<div class="molongui-post-byline" data-generator="<?php echo esc_attr( $generator ); ?>">
    <div class="molongui-post-byline--default-template molongui-post-byline--layout-<?php echo isset( $layout ) ? $layout : Settings::get( 'layout' ); ?>">
        <div class="molongui-post-byline__row">
        <?php
        foreach( $items as $item )
        {
            if ( !empty( $byline_items[$item] ) )
            {
                if ( !empty( $byline_items[$item]['condition'] ) )
                {
                    if ( 0 < $elements )
                    {
                        ?><div class="molongui-post-byline__separator"><?php echo wp_kses_post( $separator ); ?></div><?php
                    }
                    include $byline_items[$item]['template'];
                    ++$elements;
                }
            }
        }
        ?>
        </div>
        <div class="molongui-post-byline__row">
        <?php
        if ( !empty( $byline_items['contributor'] ) )
        {
            if ( !empty( $byline_items['contributor']['condition'] ) )
            {
                include $byline_items['contributor']['template'];
            }
        }
        ?>
        </div>
    </div>
</div>