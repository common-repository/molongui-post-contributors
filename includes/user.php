<?php

namespace Molongui\Contributors;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class User extends \Molongui\Contributors\Common\Utils\User
{
    public static function current_user_can_set_contributors()
    {
        $current_user = wp_get_current_user();
        if ( !$current_user )
        {
            return false;
        }
        if ( function_exists( 'is_super_admin' ) and is_super_admin() )
        {
            return true;
        }
        $can_set_authors = isset( $current_user->allcaps['edit_others_posts'] ) and $current_user->allcaps['edit_others_posts'];

        return apply_filters( 'molongui_contributors/can_set_contributors', $can_set_authors );
    }

} // class