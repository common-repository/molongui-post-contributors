<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Storefront extends \Molongui\Contributors\Integrations\Function_Override
{
    protected $name = 'storefront';
    protected $separator = ' — ';
    public function separator_between_role_and_name( $default )
    {
        return '';
    }

} // class
add_action( 'molongui_contributors/init', function()
{
    new Storefront();
});