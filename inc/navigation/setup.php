<?php

/**
 * Navigation locations owned by NextSaaS.
 *
 * The theme registers and renders its own `primary` location, so basic navigation
 * never depends on an optional plugin. `beplus-visual-mega-nav`, when active, is
 * layered on top of the very same classic menu — see
 * inc/compat/beplus-visual-mega-nav.php.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The one navigation location this theme renders.
 *
 * @return string Location slug.
 */
function nextsaas_primary_menu_location(): string {
	return (string) apply_filters( 'nextsaas_primary_menu_location', 'primary' );
}

/**
 * Register the theme's navigation location.
 *
 * Runs after the parent's own `after_setup_theme` callbacks and merges with
 * whatever the parent registered, so both menus survive.
 */
function nextsaas_register_nav_menus(): void {
	register_nav_menus(
		array(
			nextsaas_primary_menu_location() => __( 'Primary navigation', 'nextsaas' ),
		),
	);
}
add_action( 'after_setup_theme', 'nextsaas_register_nav_menus', 11 );

/**
 * Whether the `primary` menu can actually be rendered.
 *
 * `has_nav_menu()` answers for the location, and the location is reported as
 * assigned as soon as a menu is chosen for it in the Customizer or the Site
 * Editor. Both the header block and its smoke assertions ask here so the fallback
 * decision is made in exactly one place.
 *
 * @return bool True when at least one classic menu is assigned.
 */
function nextsaas_has_primary_menu(): bool {
	return has_nav_menu( nextsaas_primary_menu_location() );
}
