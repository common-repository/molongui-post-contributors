<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Modules\PointerPlus;

defined('ABSPATH') or exit; // Exit if accessed directly
class Pointers
{
    public function __construct()
    {
        $pointerplus = new PointerPlus( array( 'prefix' => 'molongui-contributors' ) );

        add_filter( 'molongui-contributors-pointerplus_list', array( $this, 'add_pointers' ), 10, 2 );
    }
    public function add_pointers( $pointers, $prefix )
    {
        if ( apply_filters( 'molongui_contributors/show_pointers', true ) )
        {
            $pointers = array_merge( $pointers, array
            (
                $prefix . '_settings' => array
                (
                    'selector'   => '#menu-settings',
                    'title'      => __( "Molongui Post Contributors", 'molongui-post-contributors' ),
                    /*! // translators: %1$s: <strong><code>. %2$s: </code></strong>. */
                    'text'       => sprintf( __( 'In %1$sSettings > Post Contributors%2$s, you will find all the options you need to configure the plugin to your preferences.', 'molongui-post-contributors' ), '<strong><code>', '</code></strong>'),
                    'icon_class' => 'dashicons-admin-settings',
                    'width'      => 300,
                    'next'       => '',
                ),
            ));
        }

        return $pointers;
    }

} // class
new Pointers();