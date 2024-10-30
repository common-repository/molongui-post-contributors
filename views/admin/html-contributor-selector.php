<?php

use Molongui\Contributors\Contributor_Role;
use Molongui\Contributors\Post;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

$post = Post::get( $post );
if ( !$post )
{
    return;
}

$contributor = Post::get_contributors( $post->ID );
$contributor_roles = Contributor_Role::get_contributor_roles();
$tip = __( "Search for a contributor and select their role in this post.", 'molongui-post-contributors' );

?>

<style>
    #molongui-post-contributors { /*width: 100%;*/ max-width: 25rem; }
    #molongui-post-contributors p.molongui-post-contributors__tip { line-height: 18px; font-size: 12px; color: #757575; }
    .molongui-post-contributors__tip--quick { display: none; margin-top: 6px; }
    .molongui-post-contributors__selector {  }
    .molongui-post-contributors__controls { height: 40px; display: flex; justify-content: space-between; }
    .molongui-post-contributors__add-form { display: flex; flex-direction: column; row-gap: 10px; margin-top: 1em; padding: 10px; background: #f6f7f7; border: 1px dashed lightgray; border-radius: 3px; }
    .molongui-post-contributors__add-form label { font-size: 12px; font-weight: 600; margin-bottom: -8px; }
    .molongui-post-contributors__add-form button.button { margin-top: 5px; height: 40px; }
    .molongui-post-contributors__search { flex-grow: 2; }
    input.molongui-post-contributors__input
    {
        display: block;
        width: 100%;
        max-width: 25rem; /*max-width: none;*/
        height: 40px;
        min-height: 40px;
        margin: 0;
        padding: 0 24px 0 8px; /*padding: 0px 34px 0px 16px; */
        appearance: none;
        -webkit-appearance: none;
        border-color: #949494; /*#8c8f94;*/
        border-radius: 2px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        box-sizing: border-box;
        box-shadow: none !important;
        line-height: 2;
        font-family: inherit;
        color: rgb(30, 30, 30); /*color: #2c3338;*/
        cursor: pointer;
        vertical-align: middle;
    }
    input.molongui-post-contributors__input:disabled { background: #f4f4f4; }
    input.molongui-post-contributors__input:disabled::placeholder { color: #b7b7b7; }
    .molongui-post-contributors__spinner { position: relative; top: -30px; float: right; margin: 0 10px; }
    .molongui-post-contributors__controls .molongui-post-contributors__add-new { margin-left: 1em; padding: 0 9px; flex-shrink: 0; align-content: center; line-height: 1; cursor: pointer; }
    .molongui-post-contributors__controls .molongui-post-contributors__add-new .dashicons { font-size: 14px; height: 14px; }
    .molongui-post-contributors__list { margin-top: 1em; }
    .molongui-post-contributors__list .molongui-post-contributors__list-title { display: block; margin-bottom: 1em; font-size: 11px; font-weight: 500; text-transform: uppercase; /*color: #1e1e1e;*/ }
    .molongui-post-contributors__none { font-family: monospace; font-size: 12px; color: #9d9d9d; }
    .molongui-post-contributors__none:has(+ .molongui-post-contributors__item) { display: none; }
    .molongui-post-contributors__item { margin-bottom: 1em; /*border: 1px solid lightgray; border-left: 0; border-right: 0;*/ }
    .molongui-post-contributors__item:not(:only-of-type) { cursor: move; }
    .molongui-post-contributors__row { display: flex; align-items: stretch; line-height: 1; /* reset */ }
    .molongui-post-contributors__row { padding: 6px; background: #fafafa; border: 1px solid #eee; border-radius: 3px; }
    .molongui-post-contributors__row:hover { background: #f7f7f7; border: 1px dashed #aaa; /*box-shadow: 0 0 5px #ddd;*/ }
    .molongui-post-contributors__column { display: flex; flex-direction: column; /*align-self: center;*/ flex-grow: 2; overflow: hidden; }
    .molongui-post-contributors__actions { display: flex; align-items: center; flex-shrink: 0; margin-left: 10px; padding-left: 5px; border-left: 1px solid #ccc; }
    .molongui-post-contributors__item .dashicons { /*padding-right: 5px; color: red;*/ vertical-align: middle; color: gray; cursor: pointer; }
    .molongui-post-contributors__item .dashicons:hover { background: #f0f0f0; }
    .molongui-post-contributors__item .molongui-post-contributors__delete { color: red; }
    .molongui-post-contributors__item .molongui-post-contributors__delete:hover { background: red; color: white; }
    .molongui-post-contributors__item:only-child .dashicons:not(.dashicons-no-alt) { color: #ccc; cursor: not-allowed; }
    .molongui-post-contributors__item:only-child .dashicons:not(.dashicons-no-alt):hover { background: transparent !important; }
    .molongui-post-contributors__item:first-of-type .molongui-post-contributors__up { color: #ccc; cursor: not-allowed; }
    .molongui-post-contributors__item:first-of-type .molongui-post-contributors__up:hover { background: transparent; }
    .molongui-post-contributors__item:last-of-type .molongui-post-contributors__down { color: #ccc; cursor: not-allowed; }
    .molongui-post-contributors__item:last-of-type .molongui-post-contributors__down:hover { background: transparent; }
    .molongui-post-contributors__avatar { flex-shrink: 0; align-self: center; margin-right: 12px; }
    .molongui-post-contributors__avatar img { width: 36px; height: 36px; border-radius: 100%; }
    .molongui-post-contributors__name { flex-grow: 2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1; font-weight: 600; }
    select.molongui-post-contributors__role
    {
        height: 1.5em;
        min-height: 1.5em;
        margin: 0;
        padding: 0 0 0 16px;
        background: transparent no-repeat left 0 top 55% url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 25 25'%3E%3Cdefs%3E%3Cstyle%3E.cls-1%7Bfill:%23231f20%7D%3C/style%3E%3C/defs%3E%3Cg id='pencil'%3E%3Cpath class='cls-1' d='m21.84 5-4.31-3.62a1.17 1.17 0 0 0-.87-.27 1.15 1.15 0 0 0-.8.42L3.1 16.65a.54.54 0 0 0-.11.26l-.84 6.44a.5.5 0 0 0 .49.56h.15L9 22a.47.47 0 0 0 .24-.15L22 6.69A1.19 1.19 0 0 0 21.84 5zm-.62 1L8.56 21l-5.32 1.7.76-5.51 12.66-15a.21.21 0 0 1 .27 0l4.27 3.59a.19.19 0 0 1 .02.27z'/%3E%3Cpath class='cls-1' d='M15 5.75a.51.51 0 0 0-.71.06.49.49 0 0 0 .06.7l3.05 2.61a.49.49 0 0 0 .32.12.52.52 0 0 0 .38-.17.51.51 0 0 0-.1-.71z'/%3E%3C/g%3E%3C/svg%3E");
        background-size: 13px;
        border: none;
        line-height: 1.5;
        font-size: 0.9231em;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    select.molongui-post-contributors__role:focus { box-shadow: none; border-color: transparent; }
    .molongui-post-contributors__roles { margin: 2em 0 0; padding: 1em 0 0; border-top: 1px dotted #a8a8a8; }
    .molongui-post-contributors__roles a { font-size: 12px; text-decoration: none; }
    .molongui-post-contributors__roles a span { font-size: 18px; width: 18px; height: 18px; }
    @keyframes molongui-post-contributors-spinner
    {
        to { transform: rotate(360deg); }
    }
    .molongui-post-contributors-spinner:before
    {
        content: '';
        box-sizing: border-box;
        position: absolute;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border-radius: 50%;
        border: 2px solid #ccc;
        border-top-color: #07d;
        animation: molongui-post-contributors-spinner .6s linear infinite;
    }
    .edit-post-meta-boxes-area.is-side #molongui-post-contributors-metabox h2.hndle.ui-sortable-handle { padding: 0 16px; font-weight: 500; font-size: 13px; }
    .edit-post-meta-boxes-area.is-side #molongui-post-contributors-metabox .inside { padding: 0 16px 16px; }
    .inline-edit-col #molongui-post-contributors { margin-left: 6em; }
    .inline-edit-col .molongui-post-contributors__list { margin-top: 0; }
    .inline-edit-col .molongui-post-contributors__tip { display: none; }
    .inline-edit-col .molongui-post-contributors__tip--quick { display: block; }

</style>

<div id="molongui-post-contributors" class="hide-if-no-js">

        <div class="molongui-post-contributors__selector">

            <?php if ( apply_filters( 'molongui_contributors/contributor_selector/show_description', true ) ) : ?>
            <p class="molongui-post-contributors__tip"><?php echo wp_kses_post( $tip ); ?></p>
            <?php endif; ?>

            <div class="molongui-post-contributors__controls">
                <div class="molongui-post-contributors__search">
                    <input class="molongui-post-contributors__input" type="text" placeholder="<?php esc_attr_e( "Search for a contributor", 'molongui-post-contributors' ); ?>" />
                    <span id="contributors-loading" class="molongui-post-contributors__spinner spinner"></span>
                </div>
<?php /*
                <?php if ( current_user_can( 'create_users' ) and 'edit' === $screen ) : ?>
                    <a class="molongui-post-contributors__add-new button" title="<?php esc_html_e( "Quick add a new contributor. To add an existing contributor, type their name in the search box on the left.", 'molongui-post-contributors' ); ?>"><span class="dashicons dashicons-plus-alt2"></span></a>
                <?php endif; ?>
*/ ?>
            </div>
<?php /*
            <?php if ( current_user_can( 'create_users' ) ) : ?>
                <div class="molongui-post-contributors__add-form" style="display:none">
                    <p class="molongui-post-contributors__tip"><?php esc_html_e( "Use this form only to create a new contributor. To add an existing contributor, type their name in the search box above.", 'molongui-post-contributors' ); ?></p>
                    <label for="molongui-new-contributor-name"><?php esc_html_e( "Display Name", 'molongui-post-contributors' ); ?></label>
                    <input name="molongui-new-contributor-name" class="" type="text" >
                    <label for="molongui-new-contributor-type"><?php esc_html_e( "Contributor Type", 'molongui-post-contributors' ); ?></label>
                    <select name="molongui-new-contributor-type" class="">
                        <option value="user"><?php esc_attr_e( "WP User", 'molongui-post-contributors' ); ?></option>
                        <option value="guest"><?php esc_attr_e( "Guest Contributor", 'molongui-post-contributors' ); ?></option>
                    </select>
                    <label for="molongui-new-contributor-email"><?php esc_html_e( "Email Address", 'molongui-post-contributors' ); ?></label>
                    <input name="molongui-new-contributor-email" class="" type="email" >
                    <?php wp_nonce_field( 'molongui_contributors_quick_add_contributor', 'molongui_contributors_quick_add_contributor_nonce' ); ?>
                    <button class="button"><?php esc_html_e( "Add New Contributor", 'molongui-post-contributors' ); ?></button>
                </div>
            <?php endif; ?>
*/ ?>
            <?php if ( apply_filters( 'molongui_contributors/contributor_selector/show_description', true ) ) : ?>
            <p class="molongui-post-contributors__tip molongui-post-contributors__tip--quick"><?php echo wp_kses_post( $tip ); ?></p>
            <?php endif; ?>
        </div>

        <div class="molongui-post-contributors__list">
            <span class="molongui-post-contributors__list-title">
                <?php esc_html_e( "Post Contributors", 'molongui-post-contributors' ); ?>
            </span>
            <span class="molongui-post-contributors__none">
                <?php esc_html_e( "None. Use the search box to add one", 'molongui-post-contributors' ); ?>
            </span>
            <?php if ( 'edit' === $screen ) : ?>
                <?php if ( isset( $contributor ) and is_object( $contributor ) ) : ?>
                    <div id="contributor-<?php echo esc_attr( $contributor->ID ); ?>" class="molongui-post-contributors__item molongui-post-contributors__item--<?php echo $contributor->type; ?>"
                         data-contributor-id="<?php echo esc_attr( $contributor->ID ); ?>"
                         data-contributor-type="<?php echo esc_attr( $contributor->type ); ?>"
                         data-contributor-ref="<?php echo esc_attr( $contributor->ref ); ?>"
                         data-contributor-name="<?php echo esc_attr( $contributor->display_name ); ?>"
                         data-contributor-nicename="<?php echo esc_attr( $contributor->user_nicename ); ?>">
                        <div class="molongui-post-contributors__row">
                            <?php
                            $contributor_gravatar = get_avatar( $contributor->ID, 36 );
                            if ( !empty( $contributor_gravatar ) ) : ?>
                                <div class="molongui-post-contributors__avatar">
                                    <?php echo $contributor_gravatar; ?>
                                </div>
                            <?php endif; ?>
                            <div class="molongui-post-contributors__column">
                                <div class="molongui-post-contributors__name">
                                    <?php echo esc_html( $contributor->display_name ); ?>
                                </div>
                                <?php $role = !empty( $contributor->post_role_name ) ? $contributor->post_role_name : $contributor->default_role; ?>
                                <select class="molongui-post-contributors__role" title="<?php esc_attr_e( "Click to change contribution role", 'molongui-post-contributors' ); ?>">
                                    <?php foreach ( $contributor_roles as $contributor_role ) : ?>
                                        <option value="<?php echo $contributor_role->slug; ?>" <?php selected( $role, $contributor_role->slug ); ?>><?php echo $contributor_role->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="molongui-post-contributors__actions">
                                <?php
                                $delete_icon    = '<span class="dashicons dashicons-no-alt molongui-post-contributors__delete" title="' . esc_attr__( "Remove", 'molongui-post-contributors' ) . '"></span>';
                                $move_up_icon   = '<span class="dashicons dashicons-arrow-up-alt2 molongui-post-contributors__up" title="' . esc_attr__( "Move up", 'molongui-post-contributors' ) . '"></span>';
                                $move_down_icon = '<span class="dashicons dashicons-arrow-down-alt2 molongui-post-contributors__down" title="' . esc_attr__( "Move down", 'molongui-post-contributors' ) . '"></span>';
                                printf ( '%s%s%s'
                                    , apply_filters( 'molongui_contributors/contributor_selector/show_arrows', true ) ? $move_up_icon : ''
                                    , apply_filters( 'molongui_contributors/contributor_selector/show_arrows', true ) ? $move_down_icon : ''
                                    , $delete_icon
                                );
                                ?>
                            </div>
                            <input type="hidden" name="molongui_post_contributors[<?php echo esc_attr( $contributor->post_role_name ); ?>][]" value="<?php echo esc_attr( $contributor->user_nicename ); ?>" class="molongui-post-contributors__contributorData">
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="molongui-post-contributors__roles">
            <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=contributor_role' ); ?>" target="_blank" title="<?php esc_attr_e( "Click to add new and manage existing contributor roles", 'molongui-post-contributors' ); ?>">
            <?php
            /*! // translators: %s: External link icon. */
            echo wp_kses_post( sprintf( __( "Edit contributor roles %s", 'molongui-post-contributors' ), '<span class="dashicons dashicons-external"></span>' ) );
            ?>
            </a>
        </div>

        <?php wp_nonce_field( 'molongui_post_contributors', 'molongui_post_contributors_nonce' ); ?>

    </div>

<?php
Post::enqueue_contributors_metabox_scripts();