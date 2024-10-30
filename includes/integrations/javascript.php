<?php

namespace Molongui\Contributors\Integrations;

use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Javascript extends \Molongui\Contributors\Integrations\Theme
{
    protected $js_target = '';
    protected $js_position = 'beforeend';
    public function init()
    {
        add_action( 'wp_head', array( $this, 'custom_css' ) );
        add_action( 'wp_footer', array( $this, 'add_contributor' ) );
    }
    public function get_js_target()
    {
        return apply_filters( 'molongui_contributors/theme_javascript_target', $this->js_target );
    }
    public function get_js_position()
    {
        return apply_filters( 'molongui_contributors/theme_javascript_target', $this->js_position );
    }
    public function add_contributor()
    {
        if ( !is_singular( 'post' ) )
        {
            return;
        }
        if ( empty( $this->get_js_target() ) )
        {
            Debug::console_log( "No target DOM element provided" );
            return;
        }

        ob_start();
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function()
            {
                const targetNode = document.querySelector('<?php echo $this->get_js_target(); ?>');

                if ( null != targetNode )
                {

                    const contributor = '<?php echo $this->get_the_contributor(); ?>';
                    const position    = '<?php echo $this->get_js_position(); ?>';

                    switch ( position )
                    {
                        case 'before':
                        case 'beforebegin':
                            targetNode.insertAdjacentHTML('beforebegin', contributor);
                            break;
                        case 'afterbegin':
                            targetNode.insertAdjacentHTML('afterbegin', contributor);
                            break;
                        case 'inner':
                        case 'beforeend':
                            targetNode.insertAdjacentHTML('beforeend', contributor);
                            break;
                        case 'after':
                        case 'afterend':
                            targetNode.insertAdjacentHTML('afterend', contributor);
                            break;
                    }
                }
                else
                {
                    console.log( "Targeted DOM element not found" );
                }
            });
        </script>
        <?php

        echo Helpers::minify_js( ob_get_clean() );
    }

} // class