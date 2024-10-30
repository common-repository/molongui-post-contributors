<?php

namespace Molongui\Contributors\Integrations\Plugins;

use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Integrations\Plugins\Gutenberg\Blocks\Bylines\Byline_Block;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Gutenberg
{
    public function __construct()
    {
        /*!
         * FILTER HOOK
         *
         * Allows you to prevent the custom block from being added.
         *
         * @since 1.4.0
         */
        if ( apply_filters( 'molongui_contributors/add_gutenberg_block', true ) )
        {
            new Byline_Block();
        }
    }

} // class
new Gutenberg();
