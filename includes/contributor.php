<?php

namespace Molongui\Contributors;

use Molongui\Contributors\Common\Utils\Debug;
use Molongui\Contributors\Common\Utils\WP;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly
class Contributor
{
    public function __construct()
    {
        add_action( 'wp_ajax_contributors_ajax_suggest', array( $this, 'ajax_suggest' ) );
    }
    public function ajax_suggest()
    {
        if ( !WP::verify_nonce( 'contributors-search', '_wpnonce', 'get' ) )
        {
            echo wp_json_encode( array( 'result' => 'error', 'message' => __( "Missing or invalid nonce.", 'molongui-post-contributors' ) ) );
            wp_die();
        }

        if ( empty( $_REQUEST['q'] ) )  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        {
            die();
        }

        $search = sanitize_text_field( strtolower( $_REQUEST['q'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $ignore = array_map( 'sanitize_text_field', explode( ',', sanitize_text_field( $_REQUEST['existing_contributors'] ) ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        $contributors = self::search( $search, $ignore );
        if ( empty( $contributors ) )
        {
            echo esc_html( apply_filters( 'molongui_contributors/no_matching_contributors_message', __( "No matching contributors found.", 'molongui-post-contributors' ) ) );
        }

        foreach ( $contributors as $contributor )
        {
            $user_type = 'guest-user';
            if ( $contributor instanceof \WP_User )
            {
                $user_type = 'wp-user';
            }
            printf(
                "%s %s<code><small>%s %s(#%s)</small></code><span style='display:none'> ∣ %s ∣ %s ∣ %s ∣ %s ∣ %s</span>\n",
                esc_html( str_replace( '∣', '|', $contributor->display_name ) ),
                '<span style="display:none">∣ </span>',
                esc_html( $contributor->type ),
                '<span style="display:none">∣ </span>',
                esc_html( $contributor->ID ),
                esc_html( $contributor->user_email ),
                esc_html( $contributor->user_login ),
                esc_html( rawurldecode( $contributor->user_nicename ) ),
                esc_url( $contributor->avatar ),
                esc_html( substr( self::get_default_role( $contributor ), strlen( Contributor_Role::get_taxonomy_prefix() ) ) )
            );
        }

        die();
    }
    public function search( $search = '', $ignored_contributors = array() )
    {
        $args = array
        (
            'count_total'    => false,
            'search'         => sprintf( '*%s*', $search ),
            'search_columns' => array
            (
                'ID',
                'display_name',
                'user_email',
                'user_login',
            ),
            'capability'     => apply_filters( 'molongui_contributors/users_cap', array() ),
            'fields'         => 'ID',
        );
        $found_users = get_users( $args );

        foreach ( $found_users as $found_user )
        {
            $contributor = self::get_by( 'id', $found_user );
            $term        = self::get_term( $contributor );

            if ( empty( $term ) or empty( $term->description ) )
            {
                $term = self::update_term( $contributor );
            }
        }
        $args = array
        (
            'taxonomy'   => Contributor_Role::get_contributor_taxonomies(),
            'search'     => $search,
            'get'        => 'all',
            'number'     => 10,
            'hide_empty' => false,
        );
        $args = apply_filters( 'molongui_contributors/search_contributors_get_terms_args', $args );
        add_filter( 'terms_clauses', array( $this, 'terms_clauses' ), 10, 3 );
        $found_terms = get_terms( $args );
        remove_filter( 'terms_clauses', array( $this, 'terms_clauses' ), 10 );

        if ( empty( $found_terms ) or is_wp_error( $found_terms ) )
        {
            return array();
        }
        $found_contributors = array();
        foreach ( $found_terms as $found_term )
        {
            $found_user = self::get_by( 'user_nicename', $found_term->slug );
            if ( !empty( $found_user ) )
            {
                $found_contributors[$found_user->user_login]         = $found_user;
                $found_contributors[$found_user->user_login]->type   = 'WP User';
                $found_contributors[$found_user->user_login]->avatar = get_avatar_url( $found_user->ID, array( 'size' => array( 20, 20 ) ) );
            }
        }
        $ignored_contributors = apply_filters( 'molongui_contributors/ignored_contributors', $ignored_contributors );
        foreach ( $found_contributors as $key => $found_user )
        {
            if ( in_array( $found_user->user_nicename, $ignored_contributors, true ) )
            {
                unset( $found_contributors[$key] );
            }
            elseif ( 'wpuser' === $found_user->type and false === $found_user->has_cap( apply_filters( 'molongui_contributors/users_cap', 'edit_posts' ) ) )
            {
                unset( $found_contributors[$key] );
            }
        }

        return (array) $found_contributors;
    }
    public function terms_clauses( $pieces, $taxonomies, $args )
    {
        Debug::console_log( array( $pieces, $taxonomies, $args ), 'Contributor::terms_clauses()' );

        $pieces['where'] = str_replace( 't.name LIKE', 'tt.description LIKE', $pieces['where'] );
        return $pieces;
    }
    public static function get_default_role( $contributor, $args = null )
    {
        if ( is_int( $contributor ) )
        {
            $contributor_id = $contributor;
            $default_role   = get_user_meta( $contributor_id, '_default_contributor_role', true );

            /*!
             * FILTER HOOK
             *
             * Allow filtering the default contributor role.
             *
             * @param string $default_role   The default contributor role.
             * @param int    $contributor_id The contributor ID.
             * @param array  $args           Optional. Extra arguments to retrieve the contributor slug. Default null.
             * @since 1.0.0
             */
            $default_role = apply_filters( 'molongui_contributors/pre_default_contributor_role', $default_role, $contributor_id, $args );

            if ( !empty( $default_role ) )
            {
                $default_role = Contributor_Role::get_taxonomy_prefix() . $default_role;
            }
            elseif ( $roles = Contributor_Role::get_contributor_taxonomies() )
            {
                /*!
                 * FILTER HOOK
                 *
                 * Allow filtering the taxonomy used as default when the contributor has none defined.
                 *
                 * @param string The taxonomy taken as default (first listed taxonomy).
                 * @since 1.0.0
                 */
                $default_role = apply_filters( 'molongui_contributors/default_role', $roles[1] );
            }
            else
            {
                $default_role = false;
            }
        }
        elseif ( is_object( $contributor ) )
        {
            $contributor_id = $contributor->ID;
            $default_role   = $contributor->default_role;
        }
        else
        {
            $contributor_id = 0;
            $default_role   = '';
        }

        /*!
         * FILTER HOOK
         *
         * Allow filtering the default contributor role.
         *
         * @param string The default contributor role.
         * @param int    The contributor ID.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/default_contributor_role', $default_role, $contributor_id );
    }
    public static function get_term_prefix( $args = null )
    {
        $defaults = array
        (
            'hyphenated' => true,
        );

        if ( empty( $args ) )
        {
            $args = array();
        }

        $args = wp_parse_args( $args, $defaults );

        $prefix = 'mpcu';

        if ( $args['hyphenated'] )
        {
            $prefix = $prefix . '-';
        }

        /*!
         * FILTER HOOK
         *
         * Allow filtering the contributor type prefix to add to the term slug.
         *
         * @param array $prefix The prefix to add to the term slug.
         * @param array $args   Optional. Extra arguments to retrieve the term prefix.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/contributor_term_prefix', $prefix, $args );
    }
    public static function clean_slug( $contributor_slug, $args = null )
    {
        $defaults = array();

        if ( empty( $args ) )
        {
            $args = array();
        }
        $args = wp_parse_args( $args, $defaults );

        $prefix  = self::get_term_prefix( array( 'hyphenated' => false ) );
        $pattern = '#^' . $prefix . '\-#';
        $slug    = preg_replace( $pattern, '', $contributor_slug );
        /*!
         * FILTER HOOK
         *
         * Allow filtering the contributor slug.
         *
         * @param array  $slug              The clean slug for the contributor.
         * @param array  $contributor_slug  The contributor slug as it is stored in the wp_terms table.
         * @param array  $args              Optional. Extra arguments to retrieve the contributor slug.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/contributor_slug', $slug, $contributor_slug, $args );
    }
    public static function get_by( $key, $value )
    {
        /*!
         * Allows the object for a contributor to be returned early.
         *
         * Returning a non-null value will effectively short-circuit get_by(), passing the value through the
         * {@see 'molongui_contributors/get_contributor_by'} filter and returning early.
         *
         * @param string|null $contributor The contributor object. Default null.
         * @param string      $key         Key to search by (slug, email).
         * @param string      $value       Value to search for.
         * @since 1.0.0
         */
        $contributor = apply_filters( 'molongui_contributors/pre_get_contributor_by', null, $key, $value );

        if ( !is_null( $contributor ) )
        {
            return apply_filters( 'molongui_contributors/get_contributor_by', $contributor, $key, $value );
        }

        switch ( $key )
        {
            case 'id':
            case 'login':
            case 'email':
            case 'user_login':
            case 'user_email':
            case 'user_nicename':
                if ( 'user_login' === $key )
                {
                    $key = 'login';
                }
                if ( 'user_email' === $key )
                {
                    $key = 'email';
                }
                if ( 'user_nicename' === $key )
                {
                    $key = 'slug';
                    $value = self::clean_slug( $value );
                }

                $user = get_user_by( $key, $value );
                if ( !$user )
                {
                    return false;
                }
                $user->type         = 'wpuser';
                $user->term_prefix  = self::get_term_prefix();
                $user->default_role = self::get_default_role( $user->ID );

                return $user;

                break;
        }

        return false;
    }
    public static function get_term( $contributor, $role = null )
    {
        if ( !is_object( $contributor ) )
        {
            return;
        }
        if ( empty( $role ) )
        {
            if ( !empty( $contributor->role ) )
            {
                $role = $contributor->role;
            }
            else
            {
                return;
            }
        }

        $cache_key = 'contributor-term-' . $contributor->user_nicename;
        if ( false !== ( $term = wp_cache_get( $cache_key, 'molongui-post-contributors' ) ) )
        {
            return $term;
        }
        $term = get_term_by( 'slug', $contributor->term_prefix . $contributor->user_nicename, $role );
        if ( !$term )
        {
            $term = get_term_by( 'name', $contributor->user_nicename, $role );
        }

        wp_cache_set( $cache_key, $term, 'molongui-post-contributors' );

        return $term;
    }
    public static function update_term( $contributor, $role = null )
    {
        if ( !is_object( $contributor ) )
        {
            return false;
        }
        if ( empty( $role ) )
        {
            $role = Contributor::get_default_role( $contributor );
            if ( empty( $role ) )
            {
                return false;
            }
        }
        $search_values = array();
        foreach ( Contributor::search_fields() as $search_field )
        {
            $search_values[] = $contributor->$search_field;
        }
        $term_description = implode( ' ', $search_values );

        if ( $term = Contributor::get_term( $contributor, $role ) )
        {
            if ( $term->description != $term_description )
            {
                wp_update_term( $term->term_id, $role, array( 'description' => $term_description ) );
            }
        }
        else
        {
            $contributor_slug = $contributor->term_prefix . $contributor->user_nicename;
            $args             = array
            (
                'slug'        => $contributor_slug,
                'description' => $term_description,
            );

            $new_term = wp_insert_term( $contributor->user_login, $role, $args );
        }
        wp_cache_delete( 'contributor-term-' . $contributor->user_nicename, 'molongui-post-contributors' );

        return Contributor::get_term( $contributor, $role );
    }
    public static function search_fields()
    {
        /*!
         * FILTER HOOK
         *
         * Allow filtering the contributor search fields.
         *
         * @param array  Default search fields.
         * @since 1.0.0
         */
        return apply_filters( 'molongui_contributors/contributor_search_fields', array( 'display_name', 'first_name', 'last_name', 'user_login', 'ID', 'user_email' ) );
    }

} // class
new Contributor();