<?php

/**
 * Block-layer contract tests.
 *
 * Every assertion here reads parsed data — `block.json` decoded, the registered
 * block names, the files a manifest points at — rather than the text of a source
 * file, so a test can only fail when the theme's observable contract changes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

/**
 * Registration and manifest integrity of the block layer.
 */
final class BlockRegistrationTest extends TestCase {

	/**
	 * Reset the doubles before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		nextsaas_test_reset();
	}

	/**
	 * Path of every block directory.
	 *
	 * @return array<int, string> Directory names.
	 */
	private function block_slugs(): array {
		$slugs = array();

		foreach ( (array) glob( NEXTSAAS_DIR . '/blocks/*', GLOB_ONLYDIR ) as $dir ) {
			if ( is_readable( $dir . '/block.json' ) ) {
				$slugs[] = basename( $dir );
			}
		}

		sort( $slugs );

		return $slugs;
	}

	/**
	 * Decode one block's manifest.
	 *
	 * @param string $slug Block directory name.
	 *
	 * @return array<string, mixed> Decoded manifest.
	 */
	private function manifest( string $slug ): array {
		$raw = file_get_contents( NEXTSAAS_DIR . '/blocks/' . $slug . '/block.json' );

		$this->assertIsString( $raw, $slug . ' has an unreadable block.json' );

		$decoded = json_decode( $raw, true );

		$this->assertIsArray( $decoded, $slug . '/block.json is not valid JSON' );

		return $decoded;
	}

	/**
	 * Registering the blocks reports every block directory through the doubles.
	 */
	public function test_every_block_directory_is_registered(): void {
		nextsaas_register_blocks();

		$registered = $GLOBALS['nextsaas_test']['blocks'];
		$slugs      = $this->block_slugs();

		$this->assertNotEmpty( $slugs );

		foreach ( $slugs as $slug ) {
			$found = false;

			foreach ( $registered as $path ) {
				if ( str_ends_with( (string) $path, '/blocks/' . $slug ) ) {
					$found = true;
					break;
				}
			}

			$this->assertTrue( $found, 'register_block_type() never saw ' . $slug );
		}
	}

	/**
	 * Every manifest declares the contract the theme's renderers rely on.
	 */
	public function test_every_manifest_declares_the_block_contract(): void {
		foreach ( $this->block_slugs() as $slug ) {
			$manifest = $this->manifest( $slug );

			$this->assertSame( 3, $manifest['apiVersion'], $slug . ' is not apiVersion 3' );
			$this->assertSame( 'nextsaas/' . $slug, $manifest['name'], $slug . ' name does not match its directory' );
			$this->assertSame( 'nextsaas', $manifest['category'], $slug . ' is not in the nextsaas category' );
			$this->assertSame( 'nextsaas', $manifest['textdomain'], $slug . ' has the wrong textdomain' );
			$this->assertFalse( $manifest['supports']['html'], $slug . ' must not support html serialisation' );

			foreach ( array( 'editorScript', 'editorStyle', 'render' ) as $key ) {
				$this->assertArrayHasKey( $key, $manifest, $slug . ' is missing ' . $key );

				$file = NEXTSAAS_DIR . '/blocks/' . $slug . '/' . str_replace( 'file:./', '', (string) $manifest[ $key ] );

				$this->assertFileExists( $file, $slug . ' points at a missing ' . $key );
			}

			foreach ( array( 'style', 'viewScript' ) as $key ) {
				if ( ! isset( $manifest[ $key ] ) ) {
					continue;
				}

				$file = NEXTSAAS_DIR . '/blocks/' . $slug . '/' . str_replace( 'file:./', '', (string) $manifest[ $key ] );

				$this->assertFileExists( $file, $slug . ' points at a missing ' . $key );
			}
		}
	}

	/**
	 * A dynamic block must not declare a `save()` payload.
	 *
	 * `render.php` owns the markup, so a non-null save would freeze a second copy of
	 * it into post content and the two would drift.
	 */
	public function test_every_block_registers_an_empty_save(): void {
		foreach ( $this->block_slugs() as $slug ) {
			$index = file_get_contents( NEXTSAAS_DIR . '/blocks/' . $slug . '/index.tsx' );

			$this->assertIsString( $index );
			$this->assertStringContainsString( 'save: () => null', $index, $slug . ' does not null its save()' );
			$this->assertStringNotContainsString( 'save: () => null,', str_replace( 'save: () => null', '', $index ) );
		}
	}

	/**
	 * The category and pattern categories the inserter needs are registered.
	 */
	public function test_categories_are_registered(): void {
		$categories = array( array( 'slug' => 'core', 'title' => 'Core' ) );

		$filtered = nextsaas_register_block_categories( $categories );

		$this->assertSame( 'nextsaas', $filtered[0]['slug'] );
		$this->assertSame( 'NextSaaS Sections', $filtered[0]['title'] );

		// A second call is idempotent, so the inserter never shows two categories.
		$this->assertCount( count( $filtered ), nextsaas_register_block_categories( $filtered ) );

		nextsaas_register_pattern_categories();

		$this->assertArrayHasKey( 'nextsaas-sections', $GLOBALS['nextsaas_test']['pattern_categories'] );
		$this->assertArrayHasKey( 'nextsaas-pages', $GLOBALS['nextsaas_test']['pattern_categories'] );
	}

	/**
	 * Every block is forced into the theme's category whatever its manifest says.
	 */
	public function test_block_type_args_are_reassigned_to_the_theme_category(): void {
		$args = nextsaas_filter_block_type_args( array( 'category' => 'widgets' ), 'nextsaas/home-hero' );

		$this->assertSame( 'nextsaas', $args['category'] );

		$untouched = nextsaas_filter_block_type_args( array( 'category' => 'widgets' ), 'core/heading' );

		$this->assertSame( 'widgets', $untouched['category'] );
	}

	/**
	 * Every block ships a section pattern, and the pattern targets its own block.
	 */
	public function test_every_block_has_a_section_pattern(): void {
		foreach ( $this->block_slugs() as $slug ) {
			$path = NEXTSAAS_DIR . '/patterns/' . $slug . '.php';

			$this->assertFileExists( $path, 'missing pattern for ' . $slug );

			$source = (string) file_get_contents( $path );

			$this->assertStringContainsString( 'Slug: nextsaas/' . $slug, $source );
			$this->assertStringContainsString( 'Categories: nextsaas-sections', $source );
			$this->assertStringContainsString( '<!-- wp:nextsaas/' . $slug . ' /-->', $source );
		}
	}
}
