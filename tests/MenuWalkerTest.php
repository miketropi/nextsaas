<?php

/**
 * Header menu walker tests.
 *
 * The walker owns two decisions that are invisible in a screenshot and easy to
 * regress: which top-level item renders as a link (because the optional
 * mega-navigation plugin can only enhance an item whose trigger is an anchor), and
 * whether an item's own child panel was really opened (because the plugin suppresses
 * that call for the items it replaces with a panel). Both are asserted by walking
 * real menu objects and parsing the markup the walker emits.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

/**
 * `nextsaas/site-header` menu markup.
 */
final class MenuWalkerTest extends TestCase {

	/**
	 * Reset the doubles and load the walker before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		nextsaas_test_reset();

		require_once NEXTSAAS_DIR . '/blocks/site-header/class-nextsaas-site-header-menu-walker.php';
	}

	/**
	 * Item doubles.
	 *
	 * @param array<int, array<string, mixed>> $items Item fields.
	 *
	 * @return array<int, WP_Post>
	 */
	private function items( array $items ): array {
		return array_map(
			static function ( array $fields ): WP_Post {
				$fields['child_items'] = array_map(
					static fn( array $child ): WP_Post => new WP_Post( $child ),
					(array) ( $fields['child_items'] ?? array() ),
				);

				return new WP_Post( $fields );
			},
			$items,
		);
	}

	/**
	 * Render one item through the walker.
	 *
	 * @param array<int, array<string, mixed>> $items Item fields.
	 *
	 * @return string Emitted markup.
	 */
	private function walk( array $items ): string {
		$walker = new Nextsaas_Site_Header_Menu_Walker();
		$args   = (object) array();

		// One flat level: the walker's own start_el/end_el are what is under test.
		return $walker->walk( $this->items( $items ), 0, $args );
	}

	/**
	 * A leaf item is a link, and the item carrying children is a disclosure button.
	 */
	public function test_a_parent_item_renders_a_disclosure_button(): void {
		$markup = $this->walk(
			array(
				array(
					'ID'      => 7,
					'title'   => 'Company',
					'url'     => '#',
					'classes' => array( 'menu-item', 'menu-item-has-children' ),
				),
			),
		);

		$this->assertStringContainsString( '<div class="nav-item menu-item menu-item-has-children">', $markup );
		$this->assertStringContainsString( '<button type="button" class="nav-trigger"', $markup );
		$this->assertStringContainsString( 'aria-haspopup="true"', $markup );
		$this->assertStringContainsString( 'aria-expanded="false"', $markup );
		$this->assertStringContainsString( 'aria-controls="nextsaas-submenu-7"', $markup );
	}

	/**
	 * An item the plugin marked mega-enabled renders an anchor trigger instead.
	 *
	 * The plugin's `setupItem()` looks for `:scope > a`; a button there would leave the
	 * item half enhanced.
	 */
	public function test_a_mega_item_renders_an_anchor_trigger(): void {
		$markup = $this->walk(
			array(
				array(
					'ID'      => 9,
					'title'   => 'Product',
					'url'     => '/product/',
					'classes' => array( 'menu-item', 'menu-item-has-children', 'has-mega-menu' ),
				),
			),
		);

		$this->assertStringContainsString( '<a class="nav-trigger" href="/product/" aria-haspopup="true" aria-expanded="false">', $markup );

		// The panel id belongs to the plugin, so the theme must not reference one.
		$this->assertStringNotContainsString( 'aria-controls=', $markup );
	}

	/**
	 * A plain leaf is a link with no disclosure attributes at all.
	 */
	public function test_a_leaf_renders_a_plain_link(): void {
		$markup = $this->walk(
			array(
				array(
					'ID'    => 3,
					'title' => 'Pricing',
					'url'   => '/pricing/',
				),
			),
		);

		$this->assertStringContainsString( '<a href="/pricing/">Pricing</a>', $markup );
		$this->assertStringNotContainsString( 'aria-haspopup', $markup );
	}

	/**
	 * An item with no destination is a label, not a dead link.
	 */
	public function test_an_item_without_a_destination_is_not_a_link(): void {
		$markup = $this->walk(
			array(
				array(
					'ID'    => 4,
					'title' => 'Resources',
					'url'   => '#',
				),
			),
		);

		$this->assertStringContainsString( '<span class="nav-label">Resources</span>', $markup );
	}

	/**
	 * `url_is_current` compares paths, and only for a real destination.
	 */
	public function test_url_is_current(): void {
		$GLOBALS['nextsaas_test']['singular']  = true;
		$GLOBALS['nextsaas_test']['permalink'] = 'https://example.test/about-us/';

		$this->assertTrue( Nextsaas_Site_Header_Menu_Walker::url_is_current( 'https://example.test/about-us/' ) );
		$this->assertFalse( Nextsaas_Site_Header_Menu_Walker::url_is_current( 'https://example.test/pricing/' ) );

		nextsaas_test_reset();
		$GLOBALS['nextsaas_test']['front_page'] = true;

		$this->assertTrue( Nextsaas_Site_Header_Menu_Walker::url_is_current( 'https://example.test/' ) );
		$this->assertFalse( Nextsaas_Site_Header_Menu_Walker::url_is_current( '#top' ) );
	}

	/**
	 * Child links are only rendered when the walker's own panel was opened.
	 *
	 * `beplus-visual-mega-nav` suppresses `start_lvl()` for an item whose mega panel
	 * replaces the submenu. That call is the walker's only signal that a panel exists,
	 * so this test reproduces the suppression and asserts the children disappear with
	 * it — which is what keeps an orphan list of links from being emitted inside the
	 * item.
	 */
	public function test_children_are_skipped_when_the_panel_is_suppressed(): void {
		$items = array(
			array(
				'ID'          => 11,
				'title'       => 'Resources',
				'url'         => '',
				'classes'     => array( 'menu-item-has-children', 'has-mega-menu' ),
				'child_items' => array(
					array(
						'ID'          => 12,
						'title'       => 'The field guide',
						'url'         => '/archive/',
						'description' => 'Practical ideas for better work.',
					),
				),
			),
		);

		$normal = ( new Nextsaas_Site_Header_Menu_Walker() )->walk( $this->items( $items ), 0, (object) array() );

		$this->assertStringContainsString( 'The field guide', $normal );

		$suppressing = new class() extends Nextsaas_Site_Header_Menu_Walker {
			/**
			 * Stand in for the plugin: the panel replaces the submenu entirely.
			 *
			 * @param string $output Output buffer.
			 * @param int    $depth  Depth.
			 * @param mixed  $args   Callback arguments.
			 */
			public function start_lvl( &$output, $depth = 0, $args = array() ): void {}

			/**
			 * Stand in for the plugin: nothing was opened, so nothing is closed.
			 *
			 * @param string $output Output buffer.
			 * @param int    $depth  Depth.
			 * @param mixed  $args   Callback arguments.
			 */
			public function end_lvl( &$output, $depth = 0, $args = array() ): void {}
		};

		$suppressed = $suppressing->walk( $this->items( $items ), 0, (object) array() );

		$this->assertStringNotContainsString( 'The field guide', $suppressed );
		$this->assertStringNotContainsString( 'class="submenu"', $suppressed );
	}

	/**
	 * The fallback renderer mirrors the walker's structure for an unassigned menu.
	 */
	public function test_fallback_markup_matches_the_walker(): void {
		$markup = Nextsaas_Site_Header_Menu_Walker::fallback(
			array(
				'nextsaas_items' => array(
					array(
						'label'    => 'Company',
						'url'      => '',
						'children' => array(
							array(
								'label'       => 'About us',
								'url'         => 'about-us.html',
								'description' => 'The thinking behind NextSaas.',
							),
						),
					),
				),
			),
		);

		$this->assertStringContainsString( '<button type="button" class="nav-trigger" data-nextsaas-submenu-trigger', $markup );
		$this->assertStringContainsString( 'aria-controls="nextsaas-submenu-fallback-0"', $markup );
		$this->assertStringContainsString( '<div class="submenu" id="nextsaas-submenu-fallback-0" hidden>', $markup );
		$this->assertStringContainsString( 'The thinking behind NextSaas.', $markup );
	}

	/**
	 * The compact-mode attribute is the plugin's own host opt-in, and only when the
	 * plugin is there to read it.
	 */
	public function test_compact_mode_attribute_follows_the_plugin(): void {
		$this->assertSame( '', nextsaas_nav_compact_mode_attr() );

		$this->assertSame(
			' data-beplus-vmn-compact-mode="accordion"',
			apply_filters( 'nextsaas_nav_compact_mode', 'accordion' ) === ''
				? ''
				: ' data-beplus-vmn-compact-mode="accordion"',
		);
	}

	/**
	 * The theme adds its own location to the plugin's allowlist, once.
	 */
	public function test_locations_filter_includes_the_theme_location(): void {
		$this->assertSame( array( 'primary', 'main-menu' ), nextsaas_mega_nav_locations( array( 'primary', 'main-menu' ) ) );
		$this->assertSame( array( 'main-menu', 'primary' ), nextsaas_mega_nav_locations( array( 'main-menu' ) ) );
		$this->assertSame( array( 'primary' ), nextsaas_mega_nav_locations( 'not-an-array' ) );
	}
}
