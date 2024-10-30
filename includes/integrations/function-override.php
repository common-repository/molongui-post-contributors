<?php

namespace Molongui\Contributors\Integrations;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Function_Override extends \Molongui\Contributors\Integrations\Theme
{
    public function __construct()
    {
        parent::__construct();
    }
    public function init()
    {
        add_action( 'setup_theme', array( $this, 'overrides' ), 0 );
    }
    public function overrides()
    {
        $file = MOLONGUI_CONTRIBUTORS_DIR . 'includes/integrations/themes/' . $this->name . '/function-overrides.php';

        if ( file_exists( $file ) )
        {
            require_once $file;
        }
    }

} // class