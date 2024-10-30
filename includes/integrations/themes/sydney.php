<?php

use Molongui\Contributors\Common\Utils\Helpers;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Sydney extends \Molongui\Contributors\Integrations\Javascript
{
    protected $name = 'sydney';
    protected $separator = '<span class="molongui-meta-contributor-separator"></span>';
    protected $js_target = '.entry-meta .byline';
    protected $js_position = 'afterend';
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
        return '<span class="molongui-meta-contributor" itemprop="contributor" itemscope="" itemtype="https://schema.org/Person">';
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
        return '</span>';
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
            .molongui-meta-contributor-separator::after
            {
                content: '';
                background: var(--sydney-headings-color);
                opacity: 0.2;
                width: 4px;
                height: 4px;
                border-radius: 50%;
                display: inline-block;
                vertical-align: middle;
                margin: 0 10px;
                padding: 0;
            }
            }
        </style>
        <?php

        echo Helpers::minify_css( ob_get_clean() );
    }

} // class
add_action( 'after_setup_theme', function()
{
    new Sydney();
});