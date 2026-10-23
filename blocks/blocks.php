<?php

/**
 * Register every NextSaaS block that ships a block.json manifest.
 *
 * The parent registrar scans get_template_directory(), so child blocks are only
 * registered here — from the stylesheet directory.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all child blocks found in blocks/<slug>/block.json.
 */
function nextsaas_register_blocks(): void {
	$blocks_dir = get_stylesheet_directory() . '/blocks';

	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$block_dirs = glob( $blocks_dir . '/*', GLOB_ONLYDIR );

	if ( false === $block_dirs ) {
		return;
	}

	sort( $block_dirs );

	foreach ( $block_dirs as $block_dir ) {
		if ( ! is_readable( $block_dir . '/block.json' ) ) {
			continue;
		}

		register_block_type( $block_dir );

		$register_editor = $block_dir . '/register-editor.php';

		if ( is_readable( $register_editor ) ) {
			require_once $register_editor;
		}
	}
}
add_action( 'init', 'nextsaas_register_blocks' );

/**
 * Add the NextSaaS block category to the inserter.
 *
 * @param array<int, array<string, string>> $categories Registered block categories.
 *
 * @return array<int, array<string, string>> Filtered categories.
 */
function nextsaas_register_block_categories( array $categories ): array {
	foreach ( $categories as $category ) {
		if ( isset( $category['slug'] ) && 'nextsaas' === $category['slug'] ) {
			return $categories;
		}
	}

	array_unshift(
		$categories,
		array(
			'slug'  => 'nextsaas',
			'title' => __( 'NextSaaS Sections', 'nextsaas' ),
			'icon'  => 'art',
		),
	);

	return $categories;
}
add_filter( 'block_categories_all', 'nextsaas_register_block_categories' );

/**
 * Force every nextsaas/* block into the NextSaaS category regardless of block.json.
 *
 * @param array<string, mixed> $args Block type registration arguments.
 * @param string               $name Block name.
 *
 * @return array<string, mixed> Filtered arguments.
 */
function nextsaas_filter_block_type_args( array $args, string $name ): array {
	if ( str_starts_with( $name, 'nextsaas/' ) ) {
		$args['category'] = 'nextsaas';
	}

	return $args;
}
add_filter( 'register_block_type_args', 'nextsaas_filter_block_type_args', 10, 2 );

/**
 * Mirror the server-side category in the client-side block registry.
 *
 * The editor validates the category of a registered block against the categories
 * the client knows about; block.json values are applied client-side too, so the
 * same normalisation has to happen in JS.
 */
function nextsaas_enqueue_block_category_filter(): void {
	$script = <<<'JS'
( function ( hooks ) {
	hooks.addFilter( 'blocks.registerBlockType', 'nextsaas/category', function ( settings, name ) {
		if ( name && name.indexOf( 'nextsaas/' ) === 0 ) {
			settings = Object.assign( {}, settings, { category: 'nextsaas' } );
		}
		return settings;
	} );
} )( window.wp.hooks );
JS;

	wp_add_inline_script( 'wp-blocks', $script, 'after' );
}
add_action( 'enqueue_block_editor_assets', 'nextsaas_enqueue_block_category_filter', 5 );

/**
 * Enqueue the shared block-editor stylesheet.
 *
 * Each nextsaas/* block keeps only canvas-preview rules in its own
 * blocks/<slug>/editor.css; the inspector affordances shared by every section —
 * the repeater list editor, the block preview shell and the media picker — live in
 * the single compiled assets/css/nextsaas-editor.css. The editor canvas already
 * receives assets/css/nextsaas.css through `add_editor_style()`.
 */
function nextsaas_enqueue_editor_styles(): void {
	$relative_path = '/assets/css/nextsaas-editor.css';

	if ( ! is_readable( NEXTSAAS_DIR . $relative_path ) ) {
		return;
	}

	wp_enqueue_style(
		'nextsaas-editor',
		NEXTSAAS_URI . $relative_path,
		array(),
		nextsaas_asset_version( $relative_path ),
	);
}
add_action( 'enqueue_block_editor_assets', 'nextsaas_enqueue_editor_styles' );

/**
 * Register the pattern category used by the NextSaaS insertion patterns.
 */
function nextsaas_register_pattern_categories(): void {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'nextsaas-sections',
		array(
			'label'       => __( 'NextSaaS Sections', 'nextsaas' ),
			'description' => __( 'One block per section of the NextSaaS surfaces.', 'nextsaas' ),
		),
	);

	register_block_pattern_category(
		'nextsaas-pages',
		array(
			'label'       => __( 'NextSaaS Pages', 'nextsaas' ),
			'description' => __( 'Whole NextSaaS surfaces, composed in manifest order.', 'nextsaas' ),
		),
	);
}
add_action( 'init', 'nextsaas_register_pattern_categories' );
