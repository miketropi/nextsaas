<?php

/**
 * Editor-only registration for `nextsaas/site-header`.
 *
 * The block's editor script needs one fact the REST preview cannot tell it: whether
 * a classic menu is assigned to the theme's `primary` location. The sidebar explains
 * which navigation is live — the assigned menu, or the block's own fallback rows —
 * and that explanation is only worth printing when it is true.
 *
 * Localised onto the block's own generated editor-script handle
 * (`nextsaas-site-header-editor-script`, produced by `register_block_type()` from
 * `block.json`), and declared in `resources/ts/globals.d.ts` as an optional global,
 * so the script still runs when the localisation is missing.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Publish the header's editor context.
 */
function nextsaas_site_header_editor_context(): void {
	$handle = 'nextsaas-site-header-editor-script';

	if ( ! wp_script_is( $handle, 'registered' ) ) {
		return;
	}

	wp_localize_script(
		$handle,
		'nextsaasHeader',
		array(
			'hasMenu' => nextsaas_has_primary_menu(),
		),
	);
}
add_action( 'enqueue_block_editor_assets', 'nextsaas_site_header_editor_context', 20 );
