<?php

namespace Molongui\Contributors\Integrations\Plugins;

use Molongui\Contributors\Integrations\Plugins\Elementor\Widgets\Bylines\Byline_Widget;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Elementor
{
    public function __construct()
    {
        if ( apply_filters( 'molongui_contributors/integrate_elementor', true ) )
        {
            new Byline_Widget();
        }
    }

} // class
new Elementor();