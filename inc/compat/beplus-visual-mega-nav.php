<?php

/**
 * Integration with Beplus Visual Mega Navigation.
 *
 * The plugin is optional. The theme always renders its own `primary` classic menu
 * and its own compact drawer, and the plugin only ever *decorates* those items:
 *
 * - The plugin's `MenuRenderer` wraps a theme walker with
 *   `MegaMenuWalkerDelegator` and appends the panel markup inside the menu item.
 *   It decides to do that for the locations returned by `beplus_vmn_locations`
 *   (default `primary`, `main-menu`, `header`), so `primary` is added explicitly
 *   here: the integration then survives a change to the plugin's default list.
 * - The plugin also filters `nav_menu_css_class`, which is how the theme's walker
 *   learns that a top-level item is mega-enabled. That filter runs for every menu
 *   the plugin sees, whether or not it wrapped the walker, so the walker never
 *   reads a plugin class directly — it reacts to the class name only, and only
 *   when the panel markup really exists.
 * - `data-beplus-vmn-compact-mode="accordion"` is printed by the header block on
 *   the navigation element. It is the plugin's own host opt-in: below 1024px the
 *   panels render inline inside the theme's drawer instead of the plugin opening a
 *   second off-canvas surface. That keeps one drawer, one overlay, one body lock
 *   and one focus trap.
 *
 * No plugin code is copied into the theme, and nothing here runs — or is needed —
 * when the plugin is inactive.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the optional mega-navigation plugin is active.
 *
 * Detected through the class the plugin's own bootstrap loads, so the check cannot
 * be fooled by a folder that exists but was never activated.
 *
 * @return bool True when the plugin's front-end renderer is available.
 */
function nextsaas_has_mega_nav_plugin(): bool {
	return class_exists( 'Beplus\\VisualMegaNav\\Frontend\\MenuRenderer' );
}

/**
 * Add the theme's location to the plugin's allowlist.
 *
 * @param mixed $locations Locations the plugin builds mega panels for.
 *
 * @return array<int, string> Allowlist including the theme's location.
 */
function nextsaas_mega_nav_locations( $locations ): array {
	$locations = is_array( $locations ) ? $locations : array();
	$location  = nextsaas_primary_menu_location();

	if ( ! in_array( $location, $locations, true ) ) {
		$locations[] = $location;
	}

	return array_values(
		array_filter(
			array_map(
				static fn( $value ): string => is_scalar( $value ) ? (string) $value : '',
				$locations,
			),
		),
	);
}
add_filter( 'beplus_vmn_locations', 'nextsaas_mega_nav_locations' );

/**
 * The host opt-in attribute that tells the plugin to nest panels in the theme
 * drawer instead of opening its own off-canvas surface.
 *
 * @return string Attribute string, already escaped, or `''` when the plugin is absent.
 */
function nextsaas_nav_compact_mode_attr(): string {
	if ( ! nextsaas_has_mega_nav_plugin() ) {
		return '';
	}

	/**
	 * Filters the compact-mode value printed on the navigation element.
	 *
	 * An empty string disables the opt-in and lets the plugin own its tablet
	 * off-canvas surface again.
	 *
	 * @param string $mode Plugin host contract value.
	 */
	$mode = (string) apply_filters( 'nextsaas_nav_compact_mode', 'accordion' );

	if ( '' === $mode ) {
		return '';
	}

	return ' data-beplus-vmn-compact-mode="' . esc_attr( $mode ) . '"';
}

/**
 * Class the plugin adds to a top-level item whose mega panel will be rendered.
 *
 * @return string Class name.
 */
function nextsaas_mega_nav_item_class(): string {
	return 'has-mega-menu';
}
