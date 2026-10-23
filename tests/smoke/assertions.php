<?php

/**
 * WordPress integration assertions.
 *
 * Run inside a real WordPress by `scripts/wp-test.sh` (Playground by default, WP-CLI
 * with `--wp-cli`). Everything here needs a booted install: registered block types,
 * registered patterns, a rendered template and the absence of PHP notices. The unit
 * suite covers the theme's own decisions; this file covers the wiring.
 *
 * Each check prints one line and records a failure, so one run reports the whole
 * picture instead of stopping at the first problem.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

// `wp eval`/`eval-file` already booted WordPress; only the Playground `runPHP` step
// needs this include.
if ( ! defined( 'ABSPATH' ) ) {
	require_once dirname( __DIR__, 4 ) . '/wp-load.php';
}

/** @var array<int, string> $nextsaas_failures */
$nextsaas_failures = array();

/**
 * Record a check.
 *
 * @param string $label  What was checked.
 * @param bool   $ok     Whether it passed.
 * @param string $detail Extra context printed on failure.
 */
function nextsaas_smoke_check( string $label, bool $ok, string $detail = '' ): void {
	global $nextsaas_failures;

	if ( $ok ) {
		echo '  OK   ' . $label . "\n";

		return;
	}

	$nextsaas_failures[] = $label . ( '' !== $detail ? ' — ' . $detail : '' );
	echo '  FAIL ' . $label . ( '' !== $detail ? ' — ' . $detail : '' ) . "\n";
}

echo "NextSaaS smoke\n";

// The child is active and resolves to its parent.
$nextsaas_theme = wp_get_theme();

nextsaas_smoke_check( 'theme name is NextSaaS', 'NextSaaS' === $nextsaas_theme->get( 'Name' ), (string) $nextsaas_theme->get( 'Name' ) );
nextsaas_smoke_check(
	'parent template is nextora',
	'nextora' === get_template(),
	'template: ' . get_template() . ', stylesheet: ' . get_stylesheet(),
);
nextsaas_smoke_check(
	'the active stylesheet is the child',
	'nextsaas' === get_stylesheet(),
	(string) get_stylesheet(),
);

// The parent really loaded, so a missing parent is caught rather than silently skipped.
nextsaas_smoke_check( 'the parent theme functions loaded', function_exists( 'nextora_enqueue_styles' ) );
nextsaas_smoke_check( 'the child theme functions loaded', function_exists( 'nextsaas_register_blocks' ) );

// Every child block is registered under its own namespace.
$nextsaas_registry = WP_Block_Type_Registry::get_instance()->get_all_registered();
$nextsaas_blocks   = array_values(
	array_filter(
		array_keys( $nextsaas_registry ),
		static fn( string $name ): bool => str_starts_with( $name, 'nextsaas/' ),
	),
);

$nextsaas_expected = array();

foreach ( (array) glob( get_stylesheet_directory() . '/blocks/*/block.json' ) as $nextsaas_file ) {
	$nextsaas_decoded = json_decode( (string) file_get_contents( (string) $nextsaas_file ), true );

	if ( is_array( $nextsaas_decoded ) && isset( $nextsaas_decoded['name'] ) ) {
		$nextsaas_expected[] = (string) $nextsaas_decoded['name'];
	}
}

sort( $nextsaas_expected );
sort( $nextsaas_blocks );

nextsaas_smoke_check(
	'every nextsaas/* block is registered',
	array() !== $nextsaas_expected && $nextsaas_expected === $nextsaas_blocks,
	count( $nextsaas_expected ) . ' expected, ' . count( $nextsaas_blocks ) . ' registered',
);

// The reuse contract: the parent's contact form is available and not duplicated.
nextsaas_smoke_check(
	'the parent contact form block is registered',
	isset( $nextsaas_registry['nextora/contact-form'] ),
);
nextsaas_smoke_check(
	'the child does not clone the parent contact form',
	! isset( $nextsaas_registry['nextsaas/contact-form'] ),
);

// Pattern categories and the section patterns.
$nextsaas_category_registry = WP_Block_Pattern_Categories_Registry::get_instance();

nextsaas_smoke_check( 'the nextsaas-sections category exists', $nextsaas_category_registry->is_registered( 'nextsaas-sections' ) );
nextsaas_smoke_check( 'the nextsaas-pages category exists', $nextsaas_category_registry->is_registered( 'nextsaas-pages' ) );

$nextsaas_patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
$nextsaas_section_patterns = array();

foreach ( $nextsaas_patterns as $nextsaas_pattern ) {
	if ( isset( $nextsaas_pattern['categories'] ) && in_array( 'nextsaas-sections', (array) $nextsaas_pattern['categories'], true ) ) {
		$nextsaas_section_patterns[] = (string) $nextsaas_pattern['name'];
	}
}

nextsaas_smoke_check(
	'every section has a pattern',
	count( $nextsaas_section_patterns ) >= count( $nextsaas_expected ),
	count( $nextsaas_section_patterns ) . ' section patterns for ' . count( $nextsaas_expected ) . ' blocks',
);

// Every pattern parses as blocks, and a section pattern carries its own block.
$nextsaas_unparsed = array();

foreach ( $nextsaas_patterns as $nextsaas_pattern ) {
	if ( ! str_starts_with( (string) $nextsaas_pattern['name'], 'nextsaas/' ) ) {
		continue;
	}

	$nextsaas_parsed = parse_blocks( (string) $nextsaas_pattern['content'] );

	if ( ! is_array( $nextsaas_parsed ) ) {
		$nextsaas_unparsed[] = $nextsaas_pattern['name'];
	}
}

nextsaas_smoke_check( 'every nextsaas pattern parses', array() === $nextsaas_unparsed, implode( ', ', $nextsaas_unparsed ) );

// The templates exist and resolve, including the ones re-exported from the parent.
$nextsaas_missing_templates = array();

foreach ( array( 'front-page', 'page-about-us', 'page-contact-us', 'page-faq', 'page-pricing', 'page-product', 'archive', 'single', 'search', '404', 'index', 'page' ) as $nextsaas_slug ) {
	if ( ! is_readable( get_stylesheet_directory() . '/templates/' . $nextsaas_slug . '.html' ) ) {
		$nextsaas_missing_templates[] = $nextsaas_slug;
	}
}

nextsaas_smoke_check( 'every child template exists', array() === $nextsaas_missing_templates, implode( ', ', $nextsaas_missing_templates ) );

// Every block renders without a notice through the server-side renderer.
$nextsaas_render_errors = array();

foreach ( $nextsaas_expected as $nextsaas_name ) {
	$nextsaas_block = $nextsaas_registry[ $nextsaas_name ] ?? null;

	if ( ! $nextsaas_block instanceof WP_Block_Type || ! is_callable( $nextsaas_block->render_callback ) && ! $nextsaas_block->render_callback ) {
		continue;
	}

	$nextsaas_markup = render_block(
		array(
			'blockName'    => $nextsaas_name,
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
	);

	if ( '' === trim( (string) $nextsaas_markup ) ) {
		$nextsaas_render_errors[] = $nextsaas_name;
	}
}

nextsaas_smoke_check(
	'every block renders non-empty markup',
	array() === $nextsaas_render_errors,
	implode( ', ', $nextsaas_render_errors ),
);

// Every block carries the handles the editor needs to load its own panel.
$nextsaas_no_editor = array();

foreach ( $nextsaas_expected as $nextsaas_name ) {
	$nextsaas_type = $nextsaas_registry[ $nextsaas_name ] ?? null;

	if ( ! $nextsaas_type instanceof WP_Block_Type ) {
		continue;
	}

	if ( array() === ( $nextsaas_type->editor_script_handles ?? array() ) ) {
		$nextsaas_no_editor[] = $nextsaas_name . ' (script)';
	}

	if ( array() === ( $nextsaas_type->editor_style_handles ?? array() ) ) {
		$nextsaas_no_editor[] = $nextsaas_name . ' (style)';
	}
}

nextsaas_smoke_check(
	'every block registers an editor script and style',
	array() === $nextsaas_no_editor,
	implode( ', ', array_slice( $nextsaas_no_editor, 0, 6 ) ),
);

// Navigation location.
nextsaas_smoke_check(
	'the primary navigation location is registered',
	array_key_exists( 'primary', get_registered_nav_menus() ),
);

// No PHP notices were raised while loading and rendering.
$nextsaas_last_error = error_get_last();
$nextsaas_notice     = null !== $nextsaas_last_error && in_array( $nextsaas_last_error['type'], array( E_WARNING, E_NOTICE, E_DEPRECATED ), true );

nextsaas_smoke_check( 'no PHP notice or warning while rendering', ! $nextsaas_notice, $nextsaas_notice ? (string) $nextsaas_last_error['message'] : '' );

echo "\n";

if ( array() !== $nextsaas_failures ) {
	echo 'FAILED (' . count( $nextsaas_failures ) . ")\n";

	foreach ( $nextsaas_failures as $nextsaas_failure ) {
		echo '  - ' . $nextsaas_failure . "\n";
	}

	if ( ! defined( 'NEXTSAAS_SMOKE_NO_EXIT' ) ) {
		exit( 1 );
	}

	return;
}

echo "PASSED\n";
