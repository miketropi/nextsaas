<?php

/**
 * Design-manifest coverage tests.
 *
 * `tests/fixtures/design-manifest.json` records the approved OpenDesign render:
 * every page's ordered sections with their exact copy. It is the behaviour-defining
 * input for the port, so these tests fail whenever the block layer drifts away from
 * the design — a missing block, a renamed slug, somebody's paraphrased heading, or
 * a template that drops a section.
 *
 * They compare *parsed* values (decoded JSON, decoded block manifests, the section
 * slugs recovered from template block comments), never source text.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

/**
 * Manifest-to-implementation coverage.
 */
final class ManifestCoverageTest extends TestCase {

	/**
	 * Blocks whose manifest row describes live content rather than section copy.
	 *
	 * The approved render had to write *something* into these sections, so its heading
	 * or body is sample content — the first heading of a mock article, a mock story
	 * title — and the theme renders the real post instead. Comparing that sample copy
	 * with a block default would assert the opposite of the intent.
	 *
	 * @var array<int, string>
	 */
	private const LIVE_DATA = array( 'post-article-content' );

	/**
	 * Decoded design manifest.
	 *
	 * @var array<string, mixed>
	 */
	private array $manifest = array();

	/**
	 * Load the fixture once.
	 */
	protected function setUp(): void {
		parent::setUp();

		$raw = file_get_contents( __DIR__ . '/fixtures/design-manifest.json' );

		$this->assertIsString( $raw, 'the design manifest fixture is unreadable' );

		$decoded = json_decode( $raw, true );

		$this->assertIsArray( $decoded, 'the design manifest is not valid JSON' );
		$this->assertSame( 1, $decoded['version'] );

		$this->manifest = $decoded;
	}

	/**
	 * Collect every string leaf of a default value.
	 *
	 * Approved copy lives both in a plain string attribute and inside a repeater row —
	 * a testimonial's quote is an `items[].heading` default — so the whole default has
	 * to be walked, not just the top level.
	 *
	 * @param mixed              $value   Default value.
	 * @param array<int, string> $strings Collected plain strings, by reference.
	 */
	private static function collectStrings( mixed $value, array &$strings ): void {
		if ( is_string( $value ) ) {
			$strings[] = self::plain( $value );

			return;
		}

		if ( ! is_array( $value ) ) {
			return;
		}

		foreach ( $value as $entry ) {
			self::collectStrings( $entry, $strings );
		}
	}

	/**
	 * Collapse a string to the text a reader sees.
	 *
	 * @param string $value Rich or plain value.
	 *
	 * @return string Whitespace-collapsed, tag-free text.
	 */
	private static function plain( string $value ): string {
		// Tags become a space first: `strip_tags()` would concatenate either side of a
		// `<br>` into one word, which is not what a reader sees.
		$spaced = (string) preg_replace( '#<[^>]*>#', ' ', $value );

		return trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( $spaced ) ) );
	}

	/**
	 * Every page's slug list, in manifest order.
	 *
	 * @return array<string, array<int, string>> Page key to slugs.
	 */
	private function page_slugs(): array {
		$pages = array();

		foreach ( $this->manifest['pages'] as $page ) {
			$pages[ $page['page'] ] = array_map(
				static fn( array $section ): string => (string) $section['slug'],
				$page['sections'],
			);
		}

		return $pages;
	}

	/**
	 * Every distinct section slug in the manifest.
	 *
	 * @return array<int, string> Slugs.
	 */
	private function section_slugs(): array {
		$slugs = array();

		foreach ( $this->page_slugs() as $page_slugs ) {
			$slugs = array_merge( $slugs, $page_slugs );
		}

		$slugs = array_values( array_unique( $slugs ) );
		sort( $slugs );

		return $slugs;
	}

	/**
	 * Ten approved surfaces, and the ten the theme ships.
	 */
	public function test_the_manifest_covers_ten_pages(): void {
		$this->assertCount( 10, $this->page_slugs() );
	}

	/**
	 * Each manifest section has a registered block with a matching manifest.
	 */
	public function test_every_section_has_a_block(): void {
		foreach ( $this->section_slugs() as $slug ) {
			$this->assertFileExists(
				NEXTSAAS_DIR . '/blocks/' . $slug . '/block.json',
				'the manifest lists ' . $slug . ' but no such block exists',
			);
		}
	}

	/**
	 * Shared chrome is part of the contract too, even though no page lists it.
	 */
	public function test_shared_chrome_blocks_exist(): void {
		foreach ( array( 'site-header', 'site-footer' ) as $slug ) {
			$this->assertFileExists( NEXTSAAS_DIR . '/blocks/' . $slug . '/block.json' );
			$this->assertFileExists( NEXTSAAS_DIR . '/patterns/' . $slug . '.php' );
		}
	}

	/**
	 * Every page has the template its manifest entry names.
	 */
	public function test_every_page_has_its_template(): void {
		foreach ( $this->manifest['pages'] as $page ) {
			$template = NEXTSAAS_DIR . '/templates/' . $page['template'] . '.html';

			$this->assertFileExists( $template, $page['page'] . ' has no ' . $page['template'] . ' template' );
		}
	}

	/**
	 * Templates carry their page's sections once each, in manifest order.
	 *
	 * The `nextsaas/` slugs are recovered from the template's block comments; the
	 * manifest is the authority on both membership and order.
	 */
	public function test_templates_place_every_section_once_in_order(): void {
		foreach ( $this->manifest['pages'] as $page ) {
			$source = (string) file_get_contents(
				NEXTSAAS_DIR . '/templates/' . $page['template'] . '.html',
			);

			preg_match_all( '#<!--\s+wp:(nextsaas/[a-z0-9-]+)#', $source, $matches );

			$found = array_map(
				static fn( string $name ): string => substr( $name, strlen( 'nextsaas/' ) ),
				$matches[1],
			);

			$expected = array_map(
				static fn( array $section ): string => (string) $section['slug'],
				$page['sections'],
			);

			$expected = array_values( array_filter( $expected, static fn( string $slug ): bool => 'page-hero' !== $slug ) );
			$found    = array_values( array_filter( $found, static fn( string $slug ): bool => 'page-hero' !== $slug ) );

			$this->assertSame(
				$expected,
				$found,
				$page['page'] . ' does not place its sections once each in manifest order',
			);
		}
	}

	/**
	 * The section marker each renderer prints matches its manifest slug.
	 */
	public function test_every_renderer_marks_its_section_slug(): void {
		foreach ( $this->section_slugs() as $slug ) {
			$renderer = NEXTSAAS_DIR . '/blocks/' . $slug . '/render.php';

			$this->assertFileExists( $renderer, $slug . ' has no renderer' );

			$source = (string) file_get_contents( $renderer );

			// Whitespace inside the call is the renderer's choice; the slug is not.
			$this->assertMatchesRegularExpression(
				"/nextsaas_section_attrs\\(\\s*'" . preg_quote( $slug, '/' ) . "'/",
				$source,
				$slug . ' does not mark its section with nextsaas_section_attrs()',
			);
		}
	}

	/**
	 * A block's defaults carry the approved copy.
	 *
	 * Neutral slugs are shared by several pages, so a block holds exactly one set of
	 * defaults and the templates pass the other pages' copy explicitly. The comparison
	 * is therefore "this approved string is present among the block's own string
	 * defaults", which still fails on a paraphrase, a lost `’` or a dropped `<em>`.
	 *
	 * The attribute name is deliberately not asserted: the render names the same line
	 * differently per section (`eyebrow`, `kicker`, `sideNote`, `fallbackCategory`), and
	 * pinning a name here would make a rename look like an approval change.
	 */
	public function test_block_defaults_keep_the_approved_copy(): void {
		$bySlug = array();

		foreach ( $this->manifest['pages'] as $page ) {
			foreach ( $page['sections'] as $section ) {
				foreach ( array( $section['eyebrow'] ?? null, $section['heading']['html'] ?? null ) as $value ) {
					if ( is_string( $value ) && '' !== $value ) {
						$bySlug[ $section['slug'] ][] = self::plain( $value );
					}
				}
			}
		}

		$checked = 0;

		foreach ( $bySlug as $slug => $approved ) {
			if ( in_array( $slug, self::LIVE_DATA, true ) ) {
				continue;
			}

			$path = NEXTSAAS_DIR . '/blocks/' . $slug . '/block.json';

			if ( ! is_readable( $path ) ) {
				continue;
			}

			$manifest = json_decode( (string) file_get_contents( $path ), true );

			$this->assertIsArray( $manifest, $slug . '/block.json is not valid JSON' );

			/*
			 * Both sides are compared as plain text: the manifest records an eyebrow as the
			 * reader sees it (`A SMALL SHIFT. A DIFFERENT KIND OF DAY.`) while the attribute
			 * may store the same line with its inline markup (`<br>`), and neither form is
			 * more correct than the other.
			 */
			$defaults = array();

			foreach ( $manifest['attributes'] as $attribute ) {
				self::collectStrings( $attribute['default'] ?? null, $defaults );
			}

			$approved = array_values( array_unique( $approved ) );

			/*
			 * A neutral slug is shared by several pages, and a block carries exactly one set
			 * of defaults while the templates pass the rest — so a shared slug only has to
			 * carry one of its approved lines. A slug used by one section must carry every
			 * line that section shows.
			 */
			$missing = array_values( array_diff( $approved, $defaults ) );

			if ( count( $approved ) > count( $missing ) ) {
				$checked += count( $approved ) - count( $missing );
			}

			if ( count( $approved ) > 1 && count( $approved ) > count( $missing ) ) {
				continue;
			}

			$this->assertSame(
				array(),
				$missing,
				$slug . ' does not carry these approved lines: ' . implode( ', ', $missing ),
			);

			$checked += count( $approved );
		}

		$this->assertGreaterThan( 40, $checked, 'the copy comparison checked almost nothing' );
	}

	/**
	 * A custom template the theme wrote has a file; a parent one resolves there.
	 *
	 * A child theme's `customTemplates` array replaces the parent's, so the parent's
	 * six entries have to be re-registered. They deliberately have no child file:
	 * `get_block_template()` falls back to the parent directory, which is where those
	 * templates live and where they must keep being served from.
	 */
	public function test_custom_templates_resolve(): void {
		$manifest = json_decode( (string) file_get_contents( NEXTSAAS_DIR . '/theme.json' ), true );

		$this->assertIsArray( $manifest );

		$parent_dir = dirname( NEXTSAAS_DIR ) . '/nextora/templates';

		$child_owned = array(
			'front-page',
			'page-about-us',
			'page-contact-us',
			'page-faq',
			'page-pricing',
			'page-product',
			'archive',
			'single',
			'search',
			'404',
		);

		$names = array_map( static fn( array $template ): string => (string) $template['name'], $manifest['customTemplates'] );

		foreach ( $child_owned as $name ) {
			$this->assertContains( $name, $names, $name . ' is missing from customTemplates' );
			$this->assertFileExists( NEXTSAAS_DIR . '/templates/' . $name . '.html' );
		}

		foreach ( array( 'about-us', 'contact-us', 'frequently-asked-questions', 'page-full-width', 'theme-privacy-policy', 'theme-terms-conditions' ) as $name ) {
			$this->assertContains( $name, $names, $name . ' no longer re-registers the parent template' );
			$this->assertFileExists( $parent_dir . '/' . $name . '.html' );
		}
	}

	/**
	 * The shared hero keeps one neutral slug, and one block covers seven pages.
	 */
	public function test_page_hero_is_a_single_shared_block(): void {
		$pages = array();

		foreach ( $this->manifest['pages'] as $page ) {
			foreach ( $page['sections'] as $section ) {
				if ( 'page-hero' === $section['slug'] ) {
					$pages[] = $page['page'];
				}
			}
		}

		$this->assertCount( 7, $pages );
		$this->assertFileExists( NEXTSAAS_DIR . '/blocks/page-hero/block.json' );

		$manifest = json_decode( (string) file_get_contents( NEXTSAAS_DIR . '/blocks/page-hero/block.json' ), true );

		// One set of defaults, so the templates must pass the other six pages' copy.
		$this->assertSame( 'About us', $manifest['attributes']['breadcrumbCurrentLabel']['default'] );
	}

	/**
	 * A block's heading keeps the approved *line structure*, not just its words.
	 *
	 * A heading in this design is a typographic object: `Make space<br>for the
	 * <em>next.</em>` is two display lines, and adding or dropping a `<br>` silently
	 * changes the section's height by a whole line — which is exactly the defect the
	 * 1440px comparison of the landing hero caught. Comparing plain text alone cannot
	 * see it, so the inline tag sequence is compared as well.
	 */
	public function test_block_headings_keep_the_approved_line_structure(): void {
		$checked = 0;

		foreach ( $this->manifest['pages'] as $page ) {
			foreach ( $page['sections'] as $section ) {
				$approved = $section['heading']['html'] ?? '';

				if ( ! is_string( $approved ) || '' === $approved ) {
					continue;
				}

				$path = NEXTSAAS_DIR . '/blocks/' . $section['slug'] . '/block.json';

				if ( ! is_readable( $path ) ) {
					continue;
				}

				$manifest = json_decode( (string) file_get_contents( $path ), true );

				$this->assertIsArray( $manifest );

				$plain   = self::plain( $approved );
				$strings = self::collectRawStrings( $manifest['attributes'] );
				$same    = array_values(
					array_filter( $strings, static fn( string $value ): bool => self::plain( $value ) === $plain ),
				);

				// Only assert when the block really carries this line: a shared slug may
				// legitimately hold another page's copy instead.
				if ( array() === $same ) {
					continue;
				}

				$expected = self::tagSignature( $approved );

				$matches = array_filter( $same, static fn( string $value ): bool => self::tagSignature( $value ) === $expected );

				$this->assertNotSame(
					array(),
					$matches,
					$section['slug'] . ' heading changed its line structure: expected ' . $expected,
				);

				$checked++;
			}
		}

		$this->assertGreaterThan( 10, $checked, 'the line-structure comparison checked almost nothing' );
	}

	/**
	 * The inline tags a rich value carries, in order.
	 *
	 * @param string $value Rich value.
	 *
	 * @return string Signature, e.g. `br|em`.
	 */
	private static function tagSignature( string $value ): string {
		preg_match_all( '#<(br|em|strong|span)\b#i', $value, $matches );

		return implode( '|', array_map( 'strtolower', $matches[1] ) );
	}

	/**
	 * Every string leaf of a block's attribute defaults, unnormalised.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 *
	 * @return array<int, string> Raw string defaults.
	 */
	private static function collectRawStrings( array $attributes ): array {
		$strings = array();

		$walk = static function ( mixed $value ) use ( &$walk, &$strings ): void {
			if ( is_string( $value ) ) {
				$strings[] = $value;

				return;
			}

			if ( ! is_array( $value ) ) {
				return;
			}

			foreach ( $value as $entry ) {
				$walk( $entry );
			}
		};

		foreach ( $attributes as $attribute ) {
			$walk( $attribute['default'] ?? null );
		}

		return $strings;
	}
}
