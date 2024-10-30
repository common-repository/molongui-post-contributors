<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Utils\Debug;
use WP_Error;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Contributor_Role
{
    const TAXONOMY = 'contributor_role';
    public function __construct()
    {
        add_action( 'init', array( $this, 'add_role_taxonomy' ), 0 );
        add_action( 'contributor_role_add_form_fields', array( $this, 'add_term_fields' ) );
        add_action( 'contributor_role_edit_form_fields', array( $this, 'edit_term_fields' ), 10, 2 );
        add_action( 'created_contributor_role', array( $this, 'save_term_fields' ) );
        add_action( 'edited_contributor_role' , array( $this, 'save_term_fields' ) );
        add_filter( 'pre_insert_term', array( $this, 'validate_term_name_length' ), 10, 2 );
        add_filter( 'manage_edit-contributor_role_columns', array( $this, 'add_columns_to_list' ) );
        add_filter( 'manage_contributor_role_custom_column', array( $this, 'fill_custom_columns' ), 10, 3 );
        add_action( 'init', array( $this, 'add_taxonomy_for_term' ), 1 );
    }
    public function add_role_taxonomy()
    {
        /*!
         * FILTER HOOK
         *
         * Allow filtering the labels used for the capacities taxonomy.
         *
         * @param array The capacities taxonomy labels
         * @since 1.0.0
         */
        $labels = apply_filters( 'molongui_contributors/contributor_role_labels', array
        (
            'name'                     => _x( "Roles", 'taxonomy general name', 'molongui-post-contributors' ),
            'singular_name'            => _x( "Role", 'taxonomy singular name', 'molongui-post-contributors' ),
            'search_items'             => __( "Search Roles", 'molongui-post-contributors' ),
            'all_items'                => __( "All Roles", 'molongui-post-contributors' ),
            'parent_item'              => __( "Parent Role", 'molongui-post-contributors' ),
            'parent_item_colon'        => __( "Parent Role:", 'molongui-post-contributors' ),
            'edit_item'                => __( "Edit Role", 'molongui-post-contributors' ),
            'update_item'              => __( "Update Role", 'molongui-post-contributors' ),
            'add_new_item'             => __( "Add New Role", 'molongui-post-contributors' ),
            'new_item_name'            => __( "New Role Name", 'molongui-post-contributors' ),
            'menu_name'                => __( "Roles", 'molongui-post-contributors' ),
            'back_to_items'            => __( "← Go to Roles", 'molongui-post-contributors' ),
            'name_field_description'   => __( "The name is not displayed in your site. For your reference only.", 'molongui-post-contributors' ),
            'desc_field_description'   => __( "The description is not displayed in your site. For your reference only.", 'molongui-post-contributors' ),
        ));

        $args = array
        (
            'hierarchical'       => false,
            'labels'             => $labels,
            'show_ui'            => true,
            'show_in_quick_edit' => false,
            'meta_box_cb'        => false,
            'show_admin_column'  => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'role' ),
        );

        register_taxonomy( self::TAXONOMY, array( 'contributor' ), $args );
    }
    public static function add_default_roles()
    {
        if ( !taxonomy_exists( self::TAXONOMY ) )
        {
            return;
        }

        $default_roles = array
        (
            'Reviewer'     => __( "Reviewed by", 'molongui-post-contributors' ),
            'Fact-checker' => __( "Fact-checked by", 'molongui-post-contributors' ),
            'Illustrator'  => __( "Illustrations by", 'molongui-post-contributors' ),
            'Photographer' => __( "Photography by", 'molongui-post-contributors' ),
        );

        foreach ( $default_roles as $role => $label )
        {
            if ( !term_exists( $role, self::TAXONOMY ) )
            {
                $term = wp_insert_term( $role, self::TAXONOMY );
                if ( !is_wp_error( $term ) )
                {
                    update_term_meta( $term['term_id'], 'leading-phrase', sanitize_text_field( $label ) );
                }
            }
        }
    }
    public function add_term_fields( $taxonomy )
    {
        ?>
        <div class="form-field">
            <label for="leading-phrase"><?php esc_html_e( "Leading phrase", 'molongui-post-contributors' ); ?></label>
            <input name="leading-phrase" id="leading-phrase" type="text" />
            <p><?php esc_html_e( "The phrase to add before the contributor name on your posts.", 'molongui-post-contributors' ); ?></p>
        </div>
        <?php
    }
    public function edit_term_fields( $term, $taxonomy )
    {
        $leading_phrase_value = esc_attr( get_term_meta( $term->term_id, 'leading-phrase', true ) );

        ?>
        <tr class="form-field">
            <th>
                <label for="leading-phrase"><?php esc_html_e( "Leading phrase", 'molongui-post-contributors' ); ?></label>
            </th>
            <td>
                <input name="leading-phrase" id="leading-phrase" type="text" value="<?php echo esc_attr( $leading_phrase_value ); ?>" />
                <p><?php esc_html_e( "The phrase to add before the contributor name on your posts.", 'molongui-post-contributors' ); ?></p>
            </td>
        </tr>
        <?php
    }
    public function save_term_fields( $term_id )
    {
        if ( isset( $_POST['leading-phrase'] ) )
        {
            update_term_meta(
                $term_id,
                'leading-phrase',
                sanitize_text_field( $_POST['leading-phrase'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
            );
        }
    }
    public function add_columns_to_list( $columns )
    {
        $new_column = array( 'leading_phrase' => _x( "Leading Phrase", 'taxonomy column id', 'molongui-post-contributors' ) );
        return array_merge( array_slice( $columns, 0, 3 ), $new_column, array_slice( $columns, 3 ) );
    }
    public function fill_custom_columns( $content, $column_name, $term_id )
    {
        switch ( $column_name )
        {
            case 'sortable_handle':
                $content = '<svg style="width: 1em; height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg"><path d="M219.428571 585.142857c-36.571429 0-73.142857 36.571429-73.142857 73.142857s36.571429 73.142857 73.142857 73.142857 73.142857-36.571429 73.142858-73.142857-36.571429-73.142857-73.142858-73.142857z m585.142858-146.285714c36.571429 0 73.142857-36.571429 73.142857-73.142857s-36.571429-73.142857-73.142857-73.142857-73.142857 36.571429-73.142858 73.142857 36.571429 73.142857 73.142858 73.142857zM219.428571 292.571429c-36.571429 0-73.142857 36.571429-73.142857 73.142857s36.571429 73.142857 73.142857 73.142857 73.142857-36.571429 73.142858-73.142857-36.571429-73.142857-73.142858-73.142857z m585.142858 292.571428c-36.571429 0-73.142857 36.571429-73.142858 73.142857s36.571429 73.142857 73.142858 73.142857 73.142857-36.571429 73.142857-73.142857-36.571429-73.142857-73.142857-73.142857zM512 292.571429c-36.571429 0-73.142857 36.571429-73.142857 73.142857s36.571429 73.142857 73.142857 73.142857 73.142857-36.571429 73.142857-73.142857-36.571429-73.142857-73.142857-73.142857z m0 292.571428c-36.571429 0-73.142857 36.571429-73.142857 73.142857s36.571429 73.142857 73.142857 73.142857 73.142857-36.571429 73.142857-73.142857-36.571429-73.142857-73.142857-73.142857z"  /></svg>';
                break;
            case 'leading_phrase':
                $content = esc_attr( get_term_meta( $term_id, 'leading-phrase', true ) );
                break;
            default:
                break;
        }

        return $content;
    }
    public function display_sortable_terms_notice()
    {
        ?>
        <div style="margin: 0 0 1em; padding: 15px; background: lightblue; border-radius: 4px; font-family: monospace; text-shadow: 1px 1px #d6e2e6; color: #2271b1">
            <div>
                <span class="dashicons dashicons-info-outline"></span> BYLINE ORDER
            </div>
            <div>
                <?php esc_html_e( "You can sort roles dragging the handle at the beginning of each row. The order you define will be used to display bylines in the frontend.", 'molongui-post-contributors' ); ?>
            </div>
        </div>
        <?php
    }
    public function add_taxonomy_for_term( $term_id )
    {
        $terms = self::get_contributor_roles();

        $args = array
        (
            'public'             => false,
            'hierarchical'       => false,
            'labels'             => array(),
            'show_ui'            => false,
            'show_in_quick_edit' => false,
            'meta_box_cb'        => false,
            'show_admin_column'  => false,
            'query_var'          => false,
            'sort'               => true,
        );

        if ( !empty( $terms ) ) foreach ( $terms as $term )
        {
            $r = register_taxonomy( self::get_taxonomy_prefix().$term->slug, array( 'post' ), $args );
            Debug::console_log( $r, sprintf( "Registered taxonomy for term %s", $term->slug ) );
        }
    }
    public function validate_term_name_length( $term, $taxonomy )
    {
        $max_length = 32 - strlen( self::get_taxonomy_prefix() );
        if ( strlen( $term ) > $max_length )
        {
            return new WP_Error(
                'term_name_too_long',
                sprintf( __( "Contributor role names cannot be longer than %d characters.", 'molongui-post-contributors' ), $max_length )
            );
        }
        return $term;
    }
    public static function get_contributor_roles()
    {
        $taxonomy = 'contributor_role';
        $terms = get_terms( array
        (
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ));

        /*!
         * FILTER HOOK
         *
         * Allow filtering the contributors to the post.
         *
         * @param array  $terms    The contributor roles.
         * @param string $taxonomy The taxonomy name.
         *
         * @since 1.0.0
         */
        $terms = apply_filters( 'molongui_contributors/get_contributor_roles', $terms, $taxonomy );

        return $terms;
    }
    public static function get_taxonomy_prefix()
    {
        /*!
         * FILTER HOOK
         *
         * Allow filtering the string used to make contributor roles taxonomies unique.
         *
         * @param string The default prefix used by the plugin.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/get_taxonomy_prefix', 'mpb-' );
    }
    public static function get_contributor_taxonomies()
    {
        $roles      = self::get_contributor_roles();
        $taxonomies = array();

        if ( !empty( $roles ) ) foreach ( $roles as $role )
        {
            $taxonomies[] = self::get_taxonomy_prefix().$role->slug;
        }

        /*!
         * FILTER HOOK
         *
         * Allow filtering the private taxonomies added by the plugin from user-defined roles.
         *
         * @param array Private taxonomies.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/get_contributor_taxonomies', $taxonomies );
    }
    public static function get_role_leading_phrase( $role )
    {
        $leading_phrase = '';

        $term = get_term_by( 'slug', $role, 'contributor_role' );
        if ( $term )
        {
            $leading_phrase = get_term_meta( $term->term_id, 'leading-phrase', true );
        }

        /*!
         * FILTER HOOK
         *
         * Allow filtering the leading phrase for the contributor role.
         *
         * @param string The leading phrase for the contributor role.
         * @param string The contributor role.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/leading_phrase', $leading_phrase, $role );
    }

} // class
new Contributor_Role();