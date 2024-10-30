<?php

namespace Molongui\Contributors\Integrations\Themes;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Divi extends \Molongui\Contributors\Integrations\Function_Override
{
    protected $name = 'divi';
    public function __construct()
    {
        parent::__construct();
    }

} // class
add_action( 'molongui_contributors/init', function()
{
    new Divi();
});