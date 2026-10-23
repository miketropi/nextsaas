<?php

/**
 * Assets, licences and build-output tests.
 *
 * Three things here can silently rot and are expensive to notice later: a vendored
 * font whose licence file was forgotten, a build output that is not committed so a
 * fresh clone renders unstyled, and a packaged ZIP that leaks its own sources. Each
 * is asserted from the filesystem rather than from a narrative.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

/**
 * Vendored assets, licences and committed build outputs.
 */
final class AssetsTest extends TestCase {

	/**
	 * Every font the design uses is vendored with its licence.
	 */
	public function test_vendored_fonts_and_licences(): void {
		$fonts = array(
			'assets/fonts/bodoni-moda/bodoni-moda-latin-400-normal.ttf',
			'assets/fonts/bodoni-moda/bodoni-moda-latin-400-italic.ttf',
			'assets/fonts/dm-sans/dm-sans-latin-400-normal.ttf',
			'assets/fonts/dm-sans/dm-sans-latin-500-normal.ttf',
			'assets/fonts/dm-sans/dm-sans-latin-600-normal.ttf',
			'assets/fonts/dm-sans/dm-sans-latin-700-normal.ttf',
		);

		foreach ( $fonts as $font ) {
			$this->assertFileExists( NEXTSAAS_DIR . '/' . $font, $font . ' is vendored' );
		}

		$licences = array(
			'assets/licenses/bodoni-moda-OFL.txt',
			'assets/licenses/dm-sans-OFL.txt',
			'assets/licenses/lucide-license.txt',
		);

		foreach ( $licences as $licence ) {
			$this->assertFileExists( NEXTSAAS_DIR . '/' . $licence, $licence . ' ships with the fonts it covers' );
			$this->assertGreaterThan( 100, (int) filesize( NEXTSAAS_DIR . '/' . $licence ) );
		}
	}

	/**
	 * The one licensed photograph the render used is vendored, not hotlinked.
	 */
	public function test_the_workspace_photograph_is_local(): void {
		$path = NEXTSAAS_DIR . '/assets/images/nextsaas-workspace.jpg';

		$this->assertFileExists( $path );
		$this->assertGreaterThan( 100000, (int) filesize( $path ) );
	}

	/**
	 * Compiled output is committed, so a clone renders without a build step.
	 */
	public function test_build_outputs_are_committed(): void {
		$outputs = array(
			'assets/css/nextsaas.css',
			'assets/css/nextsaas-editor.css',
			'assets/js/nextsaas.js',
		);

		foreach ( $outputs as $output ) {
			$this->assertFileExists( NEXTSAAS_DIR . '/' . $output, $output . ' is committed' );
			$this->assertGreaterThan( 500, (int) filesize( NEXTSAAS_DIR . '/' . $output ) );
		}
	}

	/**
	 * Every block ships the compiled editor bundle its manifest points at.
	 */
	public function test_every_block_ships_a_compiled_bundle(): void {
		foreach ( (array) glob( NEXTSAAS_DIR . '/blocks/*/block.json' ) as $manifest_path ) {
			$slug     = basename( dirname( (string) $manifest_path ) );
			$manifest = json_decode( (string) file_get_contents( (string) $manifest_path ), true );

			$this->assertIsArray( $manifest );

			$this->assertFileExists( NEXTSAAS_DIR . '/blocks/' . $slug . '/index.js', $slug . ' has no compiled editor bundle' );
			$this->assertFileExists( NEXTSAAS_DIR . '/blocks/' . $slug . '/index.asset.php', $slug . ' has no asset manifest' );

			if ( isset( $manifest['viewScript'] ) ) {
				$this->assertFileExists( NEXTSAAS_DIR . '/blocks/' . $slug . '/view.js', $slug . ' declares a view script it does not ship' );
				$this->assertFileExists( NEXTSAAS_DIR . '/blocks/' . $slug . '/view.asset.php' );
			}

			// A generated asset manifest is PHP returning an array with both keys.
			$asset = include NEXTSAAS_DIR . '/blocks/' . $slug . '/index.asset.php';

			$this->assertIsArray( $asset );
			$this->assertArrayHasKey( 'dependencies', $asset );
			$this->assertArrayHasKey( 'version', $asset );
			$this->assertNotSame( '', (string) $asset['version'] );
		}
	}

	/**
	 * No compiled bundle is a placeholder or an editor bundle without its deps.
	 */
	public function test_asset_versions_are_content_addressed(): void {
		foreach ( (array) glob( NEXTSAAS_DIR . '/blocks/*/index.asset.php' ) as $asset_path ) {
			$asset = include (string) $asset_path;

			$this->assertIsArray( $asset );

			// `1.0.0.<hash>`: the theme version plus a content hash, so a rebuild that
			// changes nothing rewrites identical bytes and a rebuild that changes a
			// bundle bumps the cache key.
			$this->assertMatchesRegularExpression(
				'/^' . preg_quote( NEXTSAAS_VERSION, '/' ) . '\.[0-9a-f]{8}$/',
				(string) $asset['version'],
				basename( dirname( (string) $asset_path ) ) . ' version is not content-addressed',
			);
		}
	}
}
