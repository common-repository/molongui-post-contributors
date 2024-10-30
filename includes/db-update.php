<?php

namespace Molongui\Contributors;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class DB_Update
{
	public function db_update_2()
	{
        $settings = get_option( 'molongui_contributors_options', array() );

        if ( !empty( $settings['layout'] ) and in_array( (int)$settings['layout'], array( 4, 5, 6 ) ) )
        {
            $settings['layout']++;
            update_option( 'molongui_contributors_options', $settings, true );
        }
    }

} // class