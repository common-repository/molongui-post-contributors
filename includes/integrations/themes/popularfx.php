<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class PopularFX extends \Molongui\Contributors\Integrations\Function_Override
{
    protected $name = 'popularfx';
    protected $separator = '';

} // class
add_action( 'molongui_contributors/init', function()
{
    new PopularFX();
});