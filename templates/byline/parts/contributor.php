<?php

use Molongui\Contributors\Post;
use Molongui\Contributors\Contributor_Role;
use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

$contributors = Post::get_contributors( $post_id );
if ( empty( $contributors ) )
{
    if ( $is_preview )
    {
        $contributors = new stdClass();
        $contributors->display_name = 'John Doe';
        $contributor->ID            = 0;  // To avoid issues when previewing a post with no contributor
        $contributor->type          = ''; // To avoid issues when previewing a post with no contributor
        $roles = Contributor_Role::get_contributor_roles(); $roles = array();
        if ( !empty( $roles ) )
        {
            $role = $roles['0'];
            $contributors->post_role_name = $role->name;
        }
        else
        {
            $contributors->post_role_name = 'reviewer';
            add_filter( 'molongui_contributors/leading_phrase', function()
            {
                return "Reviewed by";
            });
        }
        $contributors->is_preview = true;
    }
    else
    {
        return;
    }
}

?>

<div class="molongui-post-byline__column molongui-post-contributor" itemprop="<?php echo esc_attr( apply_filters( 'molongui_contributors/contributor_itemprop', 'contributor', $post_id, $contributors ) ); ?>" itemscope itemtype="https://schema.org/Person">
    <?php
    echo wp_kses_post( sprintf( '%s%s',
        Template::get_the_contributor_role( $contributors->post_role_name ),
        Template::get_the_contributor_name( $contributors )
    ));
    ?>
</div>