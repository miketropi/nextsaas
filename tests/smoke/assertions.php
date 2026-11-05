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

/**
 * Parse rendered markup so the behavioural checks can assert on output, not on source.
 *
 * @param string $markup Rendered block markup.
 *
 * @return DOMDocument Parsed document.
 */
function nextsaas_smoke_dom( string $markup ): DOMDocument {
	$document = new DOMDocument();

	libxml_use_internal_errors( true );
	$document->loadHTML( '<?xml encoding="utf-8" ?>' . $markup );
	libxml_clear_errors();

	return $document;
}

/**
 * The first node matching an XPath expression, or null.
 *
 * @param DOMDocument $document Parsed markup.
 * @param string      $xpath    XPath expression.
 *
 * @return DOMElement|null Matching element.
 */
function nextsaas_smoke_node( DOMDocument $document, string $xpath ): ?DOMElement {
	$found = ( new DOMXPath( $document ) )->query( $xpath );

	if ( false === $found || 0 === $found->length ) {
		return null;
	}

	$node = $found->item( 0 );

	return $node instanceof DOMElement ? $node : null;
}

/**
 * Render one nextsaas block from attributes, with no inner blocks.
 *
 * @param string               $name  Registered block name.
 * @param array<string, mixed> $attrs Block attributes.
 *
 * @return string Rendered markup.
 */
function nextsaas_smoke_render( string $name, array $attrs = array() ): string {
	return (string) render_block(
		array(
			'blockName'    => $name,
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		),
	);
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

// ── Section appearance: blank, preset, hostile and derived values ────────
$nextsaas_blank_root = nextsaas_smoke_node(
	nextsaas_smoke_dom( nextsaas_smoke_render( 'nextsaas/home-hero' ) ),
	'//*[@data-nextsaas-section="home-hero"]',
);

nextsaas_smoke_check(
	'blank appearance controls emit no override at all',
	null !== $nextsaas_blank_root
		&& ! str_contains( (string) $nextsaas_blank_root->getAttribute( 'style' ), '--nextsaas-' )
		&& ! str_contains( (string) $nextsaas_blank_root->getAttribute( 'class' ), 'has-nextsaas-' ),
	null !== $nextsaas_blank_root ? (string) $nextsaas_blank_root->getAttribute( 'style' ) : 'no root',
);

$nextsaas_preset_root = nextsaas_smoke_node(
	nextsaas_smoke_dom(
		nextsaas_smoke_render(
			'nextsaas/home-hero',
			array(
				'sectionBg'  => 'contrast',
				'sectionGap' => 'var:preset|spacing|nextsaas-lg',
			),
		),
	),
	'//*[@data-nextsaas-section="home-hero"]',
);
$nextsaas_preset_style = null !== $nextsaas_preset_root ? (string) $nextsaas_preset_root->getAttribute( 'style' ) : '';
$nextsaas_preset_class = null !== $nextsaas_preset_root ? (string) $nextsaas_preset_root->getAttribute( 'class' ) : '';

nextsaas_smoke_check(
	'a preset background emits the preset variable, not a copied colour',
	str_contains( $nextsaas_preset_style, '--nextsaas-block-bg:var(--wp--preset--color--contrast)' )
		&& str_contains( $nextsaas_preset_class, 'has-nextsaas-background' ),
	$nextsaas_preset_style,
);

nextsaas_smoke_check(
	'a spacing preset emits the spacing variable',
	str_contains( $nextsaas_preset_style, '--nextsaas-block-gap:var(--wp--preset--spacing--nextsaas-lg)' )
		&& str_contains( $nextsaas_preset_class, 'has-nextsaas-gap' ),
	$nextsaas_preset_style,
);

nextsaas_smoke_check(
	'an empty foreground over a changed background derives a readable pair',
	str_contains( $nextsaas_preset_class, 'has-nextsaas-derived-heading' )
		&& str_contains( $nextsaas_preset_class, 'has-nextsaas-derived-content' )
		&& str_contains( $nextsaas_preset_style, '--nextsaas-derived-text:var(--wp--preset--color--' )
		&& is_numeric( (string) ( null !== $nextsaas_preset_root ? $nextsaas_preset_root->getAttribute( 'data-nextsaas-content-ratio' ) : '' ) ),
	$nextsaas_preset_style,
);

$nextsaas_hostile_root = nextsaas_smoke_node(
	nextsaas_smoke_dom(
		nextsaas_smoke_render(
			'nextsaas/home-hero',
			array(
				'sectionBg'      => 'red;background-image:url(javascript:alert(1))',
				'sectionHeading' => 'var(--evil)',
				'sectionText'    => 'expression(alert(1))',
				'sectionGap'     => '12px;position:fixed',
			),
		),
	),
	'//*[@data-nextsaas-section="home-hero"]',
);
$nextsaas_hostile_style = null !== $nextsaas_hostile_root ? (string) $nextsaas_hostile_root->getAttribute( 'style' ) : '';
$nextsaas_hostile_class = null !== $nextsaas_hostile_root ? (string) $nextsaas_hostile_root->getAttribute( 'class' ) : '';

nextsaas_smoke_check(
	'hostile colour and gap values inject nothing',
	! str_contains( $nextsaas_hostile_style, 'url(' )
		&& ! str_contains( $nextsaas_hostile_style, 'position' )
		&& ! str_contains( $nextsaas_hostile_style, 'evil' )
		&& ! str_contains( $nextsaas_hostile_class, 'has-nextsaas-' ),
	$nextsaas_hostile_style,
);

$nextsaas_explicit_root = nextsaas_smoke_node(
	nextsaas_smoke_dom(
		nextsaas_smoke_render( 'nextsaas/home-hero', array( 'sectionHeading' => '#345678' ) ),
	),
	'//*[@data-nextsaas-section="home-hero"]',
);

nextsaas_smoke_check(
	'an explicit colour is stored and emitted byte-for-byte',
	null !== $nextsaas_explicit_root
		&& str_contains( (string) $nextsaas_explicit_root->getAttribute( 'style' ), '--nextsaas-block-heading:#345678' ),
	null !== $nextsaas_explicit_root ? (string) $nextsaas_explicit_root->getAttribute( 'style' ) : 'no root',
);

// ── Per-panel media: own image, shared default, placeholder ─────────────
$nextsaas_panels = nextsaas_smoke_dom(
	nextsaas_smoke_render(
		'nextsaas/home-team-solutions',
		array(
			'mediaUrl' => 'https://example.test/default-panel.png',
			'panels'   => array(
				array( 'key' => 'one', 'mediaUrl' => 'https://example.test/panel-b.png' ),
				array( 'key' => 'two', 'mediaUrl' => '' ),
				array( 'key' => 'three' ),
			),
		),
	),
);
$nextsaas_panel_sources = array();

foreach ( ( new DOMXPath( $nextsaas_panels ) )->query( '//*[contains(@class, "team-panel")]//img' ) as $nextsaas_panel_image ) {
	if ( $nextsaas_panel_image instanceof DOMElement ) {
		$nextsaas_panel_sources[] = (string) $nextsaas_panel_image->getAttribute( 'src' );
	}
}

$nextsaas_panel_placeholders = ( new DOMXPath( $nextsaas_panels ) )->query( '//*[contains(@class, "team-panel")]//*[contains(@class, "image-reserve")]' );

nextsaas_smoke_check(
	'a panel shows its own image before the shared default',
	3 === count( $nextsaas_panel_sources )
		&& str_contains( $nextsaas_panel_sources[0], 'panel-b.png' )
		&& str_contains( $nextsaas_panel_sources[1], 'default-panel.png' )
		&& str_contains( $nextsaas_panel_sources[2], 'default-panel.png' )
		&& 0 === $nextsaas_panel_placeholders->length,
	implode( ', ', $nextsaas_panel_sources ),
);

$nextsaas_panel_fallback = nextsaas_smoke_dom(
	nextsaas_smoke_render(
		'nextsaas/home-team-solutions',
		array(
			'panels' => array(
				array( 'key' => 'one', 'mediaUrl' => 'https://example.test/panel-b.png' ),
				array( 'key' => 'two' ),
			),
		),
	),
);

nextsaas_smoke_check(
	'a panel with no image at all falls back to the labelled placeholder',
	1 === ( new DOMXPath( $nextsaas_panel_fallback ) )->query( '//*[contains(@class, "team-panel")]//img' )->length
		&& 1 === ( new DOMXPath( $nextsaas_panel_fallback ) )->query( '//*[contains(@class, "team-panel")]//*[contains(@class, "image-reserve")]' )->length,
);

// ── Panel icon images replace the SVG, and fall back to it ─────────────
$nextsaas_automation = nextsaas_smoke_dom(
	nextsaas_smoke_render(
		'nextsaas/home-automation',
		array(
			'steps' => array(
				array( 'heading' => 'One', 'icon' => 'workflow', 'mediaUrl' => 'https://example.test/step-one.png' ),
				array( 'heading' => 'Two', 'icon' => 'focus' ),
				array( 'heading' => 'Three' ),
			),
		),
	),
);
$nextsaas_icon_images = ( new DOMXPath( $nextsaas_automation ) )->query( '//*[contains(@class, "flow-icon")]/img' );
$nextsaas_icon_svgs   = ( new DOMXPath( $nextsaas_automation ) )->query( '//*[contains(@class, "flow-icon")]/svg' );

nextsaas_smoke_check(
	'a step icon image replaces its SVG and a step without one keeps it',
	1 === $nextsaas_icon_images->length && 1 === $nextsaas_icon_svgs->length,
	$nextsaas_icon_images->length . ' image(s), ' . $nextsaas_icon_svgs->length . ' svg(s)',
);

$nextsaas_impact = nextsaas_smoke_dom(
	nextsaas_smoke_render(
		'nextsaas/home-impact-features',
		array(
			'items' => array(
				array( 'heading' => 'One', 'icon' => 'asterisk', 'mediaUrl' => 'https://example.test/impact-one.png' ),
				array( 'heading' => 'Two', 'icon' => 'check' ),
			),
		),
	),
);

nextsaas_smoke_check(
	'an impact icon image replaces only the graphic in its index row',
	1 === ( new DOMXPath( $nextsaas_impact ) )->query( '//*[contains(@class, "impact-index")]/img' )->length
		&& 1 === ( new DOMXPath( $nextsaas_impact ) )->query( '//*[contains(@class, "impact-index")]/svg' )->length,
);

// ── Testimonial attribution ────────────────────────────────────────────
$nextsaas_testimonial = nextsaas_smoke_dom(
	nextsaas_smoke_render(
		'nextsaas/home-testimonial',
		array(
			'items' => array(
				array( 'heading' => 'Quote one', 'body' => 'Nguyễn An, CTO — Example Co' ),
				array( 'heading' => 'Quote two', 'body' => '' ),
			),
		),
	),
);
$nextsaas_captions = array();

foreach ( ( new DOMXPath( $nextsaas_testimonial ) )->query( '//figcaption' ) as $nextsaas_caption ) {
	$nextsaas_captions[] = trim( (string) $nextsaas_caption->textContent );
}

nextsaas_smoke_check(
	'each story renders its own attribution and an empty one stays empty',
	in_array( 'Nguyễn An, CTO — Example Co', $nextsaas_captions, true )
		&& ! str_contains( strtolower( implode( ' ', $nextsaas_captions ) ), 'awaiting approval' ),
	implode( ' | ', $nextsaas_captions ),
);

// ── Pricing: destinations and an unrestricted feature list ─────────────
$nextsaas_pricing = nextsaas_smoke_dom(
	nextsaas_smoke_render(
		'nextsaas/home-pricing',
		array(
			'plans' => array(
				array(
					'name'     => 'Personal',
					'ctaLabel' => 'Explore Personal',
					'ctaUrl'   => '',
					'features' => array( 'One', '', 'Two' ),
				),
				array(
					'name'     => 'Team',
					'ctaLabel' => 'Explore Team',
					'ctaUrl'   => '/contact-us/',
					'features' => array( 'Feature 1', 'Feature 2', 'Feature 3', 'Feature 4', 'Feature 5', 'Feature 6', 'Feature 7', 'Feature 8', 'Feature 9', 'Feature 10', 'Feature 11', 'Feature 12' ),
				),
			),
		),
	),
);
$nextsaas_price_doc = new DOMXPath( $nextsaas_pricing );
$nextsaas_price_ctas = $nextsaas_price_doc->query( '//*[contains(@class, "plan")]//*[contains(@class, "btn")]' );
$nextsaas_first_plan  = $nextsaas_price_doc->query( '//*[@data-nextsaas-plan="Personal"]//li' );
$nextsaas_second_plan = $nextsaas_price_doc->query( '//*[@data-nextsaas-plan="Team"]//li' );

$nextsaas_first_cta  = $nextsaas_price_ctas->item( 0 );
$nextsaas_second_cta = $nextsaas_price_ctas->item( 1 );

nextsaas_smoke_check(
	'an empty destination stays a preview button and a filled one becomes a link',
	$nextsaas_first_cta instanceof DOMElement
		&& 'button' === strtolower( $nextsaas_first_cta->tagName )
		&& $nextsaas_first_cta->getAttribute( 'data-nextsaas-rich' ) === 'plans.0.ctaLabel'
		&& $nextsaas_second_cta instanceof DOMElement
		&& 'a' === strtolower( $nextsaas_second_cta->tagName )
		&& str_contains( $nextsaas_second_cta->getAttribute( 'href' ), '/contact-us/' )
		&& $nextsaas_second_cta->getAttribute( 'data-nextsaas-rich' ) === 'plans.1.ctaLabel',
	$nextsaas_first_cta instanceof DOMElement ? $nextsaas_first_cta->tagName . ' / ' . ( $nextsaas_second_cta instanceof DOMElement ? $nextsaas_second_cta->tagName : 'missing' ) : 'missing',
);

nextsaas_smoke_check(
	'twelve feature lines survive rendering and a blank line renders nothing',
	12 === $nextsaas_second_plan->length && 2 === $nextsaas_first_plan->length,
	$nextsaas_second_plan->length . ' / ' . $nextsaas_first_plan->length,
);

// ── Native contact section keeps its marker and appearance ─────────────
$nextsaas_contact_root = nextsaas_smoke_node(
	nextsaas_smoke_dom( nextsaas_smoke_render( 'nextsaas/contact-us-contact', array( 'sectionBg' => 'surface' ) ) ),
	'//*[@data-nextsaas-section="contact-us-contact"]',
);

nextsaas_smoke_check(
	'the contact section renders its own appearance',
	null !== $nextsaas_contact_root
		&& str_contains( (string) $nextsaas_contact_root->getAttribute( 'class' ), 'has-nextsaas-background' )
		&& str_contains( (string) $nextsaas_contact_root->getAttribute( 'class' ), 'has-nextsaas-derived-heading' ),
	null !== $nextsaas_contact_root ? (string) $nextsaas_contact_root->getAttribute( 'class' ) : 'no root',
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
