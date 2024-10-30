<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Utils\Request;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Wizard extends \Molongui\Contributors\Common\Modules\Setup_Wizard
{
    public function __construct()
    {
        add_filter( 'molongui_contributors/wizard_fallback', array( $this, 'get_fallback_url' ) );
        add_filter( 'molongui_contributors/wizard_settings', array( $this, 'parse_settings' ) );
        add_filter( 'molongui_contributors/wizard_steps', array( __CLASS__, 'get_step_count' ) );

        parent::__construct();
    }
    public function get_fallback_url()
    {
        return '/options-general.php?page=molongui-post-contributors';
    }
    public function parse_settings( $wizard_settings )
    {
        if ( !empty( Request::request( 'default_contributor_roles' ) ) )
        {
            Contributor_Role::add_default_roles();
        }

        if ( !empty( Request::request( 'contributors_display' ) ) )
        {
            switch ( Request::request( 'contributors_display' ) )
            {
                case 'template_override':
                    $wizard_settings['post_template_override'] = true;
                    $wizard_settings['add_to_content'] = false;
                    break;
                case 'add_to_content':
                    $wizard_settings['post_template_override'] = false;
                    $wizard_settings['add_to_content'] = true;
                    break;
                default:
                    $wizard_settings['post_template_override'] = false;
                    $wizard_settings['add_to_content'] = false;
                    break;
            }
        }
        $ignore = array
        (
            'action',
            'nonce',
            'contributors_display',
            'default_contributor_roles',
        );

        foreach ( $_REQUEST as $key => $value )
        {
            if ( in_array( $key, $ignore ) )
            {
                continue;
            }
            $wizard_settings[$key] = sanitize_text_field( $value );
        }

        return $wizard_settings;
    }
    public static function get_step_count()
    {
        $max_steps   = 6;
        $integration = Integrations::current_theme() !== 'none';

        return $max_steps - ( $integration ? 1 : 0 ) - 1;
    }

} // class
new Wizard();