<?php

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Newspaper extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'Newspaper';
    protected $separator = '<span style="margin:0 2px"> - </span>';
    protected $js_target = '.td-module-meta-info';
    public function before_begin_contributor_role( $default )
    {
        return $default;
    }
    public function open_tag_contributor_role( $default )
    {
        return $default;
    }
    public function after_begin_contributor_role( $default )
    {
        return $default;
    }
    public function before_end_contributor_role( $default )
    {
        return $default;
    }
    public function close_tag_contributor_role( $default )
    {
        return $default;
    }
    public function after_end_contributor_role( $default )
    {
        return $default;
    }
    public function before_begin_contributor_name( $default )
    {
        return $default;
    }
    public function open_tag_contributor_name( $default )
    {
        return $default;
    }
    public function before_contributor_name( $default )
    {
        return $default;
    }
    public function before_end_contributor_name( $default )
    {
        return $default;
    }
    public function close_tag_contributor_name( $default )
    {
        return $default;
    }
    public function after_end_contributor_name( $default )
    {
        return $default;
    }
    public function before_begin_the_contributor( $default )
    {
        return $default;
    }
    public function open_tag_the_contributor( $default )
    {
        return $default;
    }
    public function after_begin_the_contributor( $default )
    {
        return $default;
    }
    public function before_end_the_contributor( $default )
    {
        return $default;
    }
    public function close_tag_the_contributor( $default )
    {
        return $default;
    }
    public function after_end_the_contributor( $default )
    {
        return $default;
    }
    public function separator_between_role_and_name( $default )
    {
        return '';
    }
    public function custom_css()
    {
        ob_start();
        ?>
        <style>
            header .td-module-meta-info
            {
                margin-bottom: 34px;
            }
            .molongui-meta-contributor
            {
                position: absolute;
                margin-top: 20px;
                float: left;
                color: #444;
            }
        </style>
        <?php

        echo Helpers::minify_css( ob_get_clean() );
    }
    public function override_template( $template )
    {
        if ( is_singular( 'post' ) )
        {
            $theme_version = defined( 'TD_THEME_VERSION' ) ? TD_THEME_VERSION : false;
            if ( $theme_version )
            {
                if ( version_compare( $theme_version, '12.1', '>=' ) )
                {
                    $file = MOLONGUI_CONTRIBUTORS_DIR . 'includes/integrations/themes/newspaper/12.1/single.php';
                    if ( file_exists( $file ) )
                    {
                        $template = $file;
                    }
                }
            }
        }

        return $template;
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Newspaper();
});