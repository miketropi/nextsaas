<?php

/**
 * Child asset pipeline.
 *
 * The compiled stylesheet and script are enqueued after the parent's
 * `nextora-app` (style) and `nextora-main` (script) so cascade and runtime order
 * stay predictable.
 *
 * File names deliberately avoid the parent's `assets/css/app.css` and
 * `assets/js/main.js`: `add_editor_style()` resolves its argument against the
 * *stylesheet* directory — which is this child — so a child file with either of
 * those names would silently replace the parent's editor canvas stylesheet.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the cache-busting version for a build artefact.
 *
 * Development gets the file mtime so a rebuild is picked up without a version
 * bump; every other environment gets the released constant, which is what the
 * package test compares against the zip's file names.
 *
 * @param string $relative_path Path relative to the child theme root.
 *
 * @return string Version string: file mtime while WP_DEBUG is on, else NEXTSAAS_VERSION.
 */
function nextsaas_asset_version( string $relative_path ): string {
	$path = NEXTSAAS_DIR . $relative_path;

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && is_readable( $path ) ) {
		$mtime = filemtime( $path );

		if ( false !== $mtime ) {
			return (string) $mtime;
		}
	}

	return NEXTSAAS_VERSION;
}

/**
 * Enqueue the compiled child stylesheet on the front end.
 *
 * Deliberately not registered on `enqueue_block_assets`: that hook also runs in
 * wp-admin, where this sheet's reset — a bare `body` rule plus the page
 * background and text colour — would win over WordPress's own admin typography
 * and repaint the whole editor chrome. The block editor canvas still receives
 * the same file through `add_editor_style()`, which loads inside the editor
 * iframe where the reset belongs.
 */
function nextsaas_enqueue_styles(): void {
	$relative_path = '/assets/css/nextsaas.css';

	if ( ! is_readable( NEXTSAAS_DIR . $relative_path ) ) {
		return;
	}

	$deps = array();

	if ( wp_style_is( 'global-styles', 'registered' ) ) {
		$deps[] = 'global-styles';
	}

	if ( wp_style_is( 'nextora-app', 'registered' ) || wp_style_is( 'nextora-app', 'enqueued' ) ) {
		$deps[] = 'nextora-app';
	}

	wp_enqueue_style(
		'nextsaas-app',
		NEXTSAAS_URI . $relative_path,
		$deps,
		nextsaas_asset_version( $relative_path ),
	);
}
add_action( 'wp_enqueue_scripts', 'nextsaas_enqueue_styles', 25 );

/**
 * Mark the document as JavaScript-capable before the first paint.
 *
 * The compact navigation collapses the menu only for `html.nextsaas-js`, so a
 * visitor without JavaScript keeps a complete, in-flow navigation instead of a
 * drawer that can never open. The marker has to be set in the head — a deferred
 * bundle runs after the first paint and the menu would visibly expand and then
 * collapse on every mobile page load — so it is one inline statement, the same
 * technique WordPress itself uses for its own enhancement classes.
 */
function nextsaas_print_js_marker(): void {
	wp_print_inline_script_tag(
		"document.documentElement.classList.add('nextsaas-js');",
	);
}
add_action( 'wp_head', 'nextsaas_print_js_marker', 1 );

/**
 * Enqueue the compiled child script after the parent script.
 */
function nextsaas_enqueue_scripts(): void {
	$relative_path = '/assets/js/nextsaas.js';

	if ( ! is_readable( NEXTSAAS_DIR . $relative_path ) ) {
		return;
	}

	$deps = array();

	if ( wp_script_is( 'nextora-main', 'registered' ) || wp_script_is( 'nextora-main', 'enqueued' ) ) {
		$deps[] = 'nextora-main';
	}

	wp_enqueue_script(
		'nextsaas-main',
		NEXTSAAS_URI . $relative_path,
		$deps,
		nextsaas_asset_version( $relative_path ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		),
	);

	wp_localize_script(
		'nextsaas-main',
		'nextsaasNav',
		array(
			'openMenu'    => __( 'Open menu', 'nextsaas' ),
			'closeMenu'   => __( 'Close menu', 'nextsaas' ),
			'openSubmenu' => __( 'Open submenu', 'nextsaas' ),
			'closeSubmenu' => __( 'Close submenu', 'nextsaas' ),
			'copied'      => __( 'Article link copied.', 'nextsaas' ),
			'copyPrompt'  => __( 'Copy this address:', 'nextsaas' ),
		),
	);
}
add_action( 'wp_enqueue_scripts', 'nextsaas_enqueue_scripts', 30 );

/**
 * Give the block editor canvas the same compiled stylesheet as the front end.
 */
function nextsaas_add_editor_canvas_styles(): void {
	if ( is_readable( NEXTSAAS_DIR . '/assets/css/nextsaas.css' ) ) {
		add_editor_style( 'assets/css/nextsaas.css' );
	}

	if ( is_readable( NEXTSAAS_DIR . '/assets/css/nextsaas-editor.css' ) ) {
		add_editor_style( 'assets/css/nextsaas-editor.css' );
	}
}
add_action( 'after_setup_theme', 'nextsaas_add_editor_canvas_styles', 20 );
