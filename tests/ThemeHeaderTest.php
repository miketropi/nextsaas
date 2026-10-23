<?php

/**
 * Theme header and asset-contract tests.
 *
 * The version lives in three places — `inc/bootstrap/version.php`, the `style.css`
 * header and the release artefact name — and WordPress reads all three. A release
 * that updates one and not the others ships with a stale cache key or an
 * unreadable theme, so the agreement is asserted rather than documented.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

/**
 * Version, header and enqueue contract.
 */
final class ThemeHeaderTest extends TestCase {

	/**
	 * The parsed `style.css` header.
	 *
	 * @return array<string, string> Header name to value.
	 */
	private function theme_header(): array {
		$source = (string) file_get_contents( NEXTSAAS_DIR . '/style.css' );

		preg_match( '#/\*(.*?)\*/#s', $source, $matches );

		$fields = array();

		foreach ( explode( "\n", $matches[1] ?? '' ) as $line ) {
			if ( preg_match( '~^([A-Za-z ]+):\s*(.+)$~', trim( $line ), $field ) ) {
				$fields[ trim( $field[1] ) ] = trim( $field[2] );
			}
		}

		return $fields;
	}

	/**
	 * The child theme declares itself as a child of Nextora.
	 */
	public function test_header_declares_the_parent_and_requirements(): void {
		$header = $this->theme_header();

		$this->assertSame( 'NextSaaS', $header['Theme Name'] ?? null );
		$this->assertSame( 'nextora', $header['Template'] ?? null );
		$this->assertSame( 'nextsaas', $header['Text Domain'] ?? null );
		$this->assertSame( '6.4', $header['Requires at least'] ?? null );
		$this->assertSame( '8.1', $header['Requires PHP'] ?? null );
		$this->assertStringContainsString( 'General Public License', $header['License'] ?? '' );
	}

	/**
	 * The version constant and the header agree.
	 */
	public function test_version_constant_matches_the_header(): void {
		$this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+$/', NEXTSAAS_VERSION );

		$header = $this->theme_header();

		$this->assertSame( NEXTSAAS_VERSION, $header['Version'] ?? null );
	}

	/**
	 * The packaged artefact name carries the version, so a release is self-describing.
	 */
	public function test_package_script_names_the_artefact_after_the_version(): void {
		$script = (string) file_get_contents( NEXTSAAS_DIR . '/scripts/build-package.mjs' );

		$this->assertStringContainsString( 'nextsaas-v', $script );
		$this->assertStringContainsString( 'version.php', $script );
	}

	/**
	 * Asset file names avoid the two the parent resolves relative to this theme.
	 *
	 * `add_editor_style( 'assets/css/app.css' )` resolves against the *stylesheet*
	 * directory, which is this child, so a child file with either name would silently
	 * replace the parent's editor canvas stylesheet.
	 */
	public function test_asset_names_do_not_collide_with_the_parent(): void {
		$this->assertFileDoesNotExist( NEXTSAAS_DIR . '/assets/css/app.css' );
		$this->assertFileDoesNotExist( NEXTSAAS_DIR . '/assets/js/main.js' );

		foreach ( (array) glob( NEXTSAAS_DIR . '/inc/**/*.php' ) as $file ) {
			$source = (string) file_get_contents( (string) $file );

			$this->assertStringNotContainsString(
				"'assets/css/app.css'",
				$source,
				basename( (string) $file ) . ' must not point at the parent-resolved asset name',
			);
		}
	}

	/**
	 * The front-end stylesheet is never registered on `enqueue_block_assets`.
	 *
	 * That hook also runs in wp-admin, where the sheet's reset would repaint the admin
	 * chrome; the editor canvas receives the same file through `add_editor_style()`.
	 */
	public function test_stylesheet_is_front_end_only(): void {
		$source = (string) file_get_contents( NEXTSAAS_DIR . '/inc/assets/enqueue.php' );

		$this->assertStringContainsString( "add_action( 'wp_enqueue_scripts', 'nextsaas_enqueue_styles', 25 )", $source );
		$this->assertStringNotContainsString( "enqueue_block_assets'", $source );
	}
}
