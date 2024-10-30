<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class TwentyTwentyOne extends \Molongui\Contributors\Integrations\Function_Override
{
    protected $name = 'twenty-twenty-one';
    protected $separator = ', ';

} // class
add_action( 'molongui_contributors/init', function()
{
    new TwentyTwentyOne();
});