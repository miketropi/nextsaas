<?php

/**
 * Fixture seeder for the integration proof.
 *
 * Creates the surfaces the design manifest describes — one page per surface, a posts
 * page, seven posts across two categories and two tags (one of them deliberately
 * without a featured image), and the `primary` menu — then assigns each page its
 * custom template. Running it twice is a no-op: every lookup is by slug and every
 * template assignment is written, not appended.
 *
 * Run it with:
 *
 *     wp eval 'require_once get_stylesheet_directory() . "/tests/fixtures/seed.php"; nextsaas_seed_fixtures();'
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

/**
 * Find a post by slug, whatever its status.
 *
 * @param string $slug      Post slug.
 * @param string $post_type Post type.
 *
 * @return int Post id, `0` when absent.
 */
function nextsaas_fixture_post_id( string $slug, string $post_type = 'page' ): int {
	$found = get_posts(
		array(
			'name'        => $slug,
			'post_type'   => $post_type,
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
		),
	);

	return array() === $found ? 0 : (int) $found[0];
}

/**
 * Create a managed post, or update the existing one.
 *
 * @param array<string, mixed> $args Post arguments, without `ID`.
 *
 * @return int Post id.
 */
function nextsaas_fixture_post( array $args ): int {
	$existing = nextsaas_fixture_post_id( (string) $args['post_name'], (string) ( $args['post_type'] ?? 'page' ) );

	$args['ID'] = $existing;

	$id = $existing > 0 ? wp_update_post( $args ) : wp_insert_post( $args );

	return is_int( $id ) ? $id : 0;
}

/**
 * Seed every fixture.
 *
 * @return array<string, int> Created ids, keyed by a stable name.
 */
function nextsaas_seed_fixtures(): array {
	$ids = array();

	// Surfaces that carry an approved composition.
	$surfaces = array(
		'home'       => array( 'Home', 'home', 'front-page' ),
		'about-us'   => array( 'About us', 'about-us', 'page-about-us' ),
		'contact-us' => array( 'Contact us', 'contact-us', 'page-contact-us' ),
		'faqs'       => array( 'Questions & answers', 'faqs', 'page-faq' ),
		'pricing'    => array( 'Plans & pricing', 'pricing', 'page-pricing' ),
		'product'    => array( 'The workspace', 'product', 'page-product' ),
	);

	foreach ( $surfaces as $key => $surface ) {
		list( $title, $slug, $template ) = $surface;

		$ids[ $key ] = nextsaas_fixture_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
		);

		// `_wp_page_template` is what the Site Editor and the front end both read.
		update_post_meta( $ids[ $key ], '_wp_page_template', $template );
	}

	// The journal: a posts page plus seven stories across two categories and two tags.
	$ids['archive'] = nextsaas_fixture_post(
		array(
			'post_title'   => 'The field guide',
			'post_name'    => 'field-guide',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
	);

	update_post_meta( $ids['archive'], '_wp_page_template', 'archive' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );
	update_option( 'page_for_posts', $ids['archive'] );

	$categories = array( 'starting-out', 'team-culture', 'workflow' );
	$tags       = array( 'context', 'decisions' );

	foreach ( $categories as $slug ) {
		if ( ! term_exists( $slug, 'category' ) ) {
			wp_insert_term( ucwords( str_replace( '-', ' ', $slug ) ), 'category', array( 'slug' => $slug ) );
		}
	}

	foreach ( $tags as $slug ) {
		if ( ! term_exists( $slug, 'post_tag' ) ) {
			wp_insert_term( ucfirst( $slug ), 'post_tag', array( 'slug' => $slug ) );
		}
	}

	$stories = array(
		array( 'Build a workspace people actually use.', 'build-a-workspace', 'starting-out', 'context', true ),
		array( 'Make room for the work that matters.', 'make-room', 'workflow', 'context', false ),
		array( 'The quiet art of a better handoff.', 'better-handoff', 'team-culture', 'decisions', true ),
		array( 'Write down the why, not just the what.', 'write-down-the-why', 'team-culture', 'decisions', true ),
		array( 'End the week with a little perspective.', 'end-the-week', 'workflow', 'context', false ),
		array( 'Know when to let the routine run.', 'let-the-routine-run', 'workflow', 'context', true ),
		array( 'Make async updates worth reading.', 'async-updates', 'team-culture', 'decisions', true ),
	);

	foreach ( $stories as $index => $story ) {
		list( $title, $slug, $category, $tag, $featured ) = $story;

		$body = "<!-- wp:heading -->\n<h2>Start with the work.</h2>\n<!-- /wp:heading -->\n\n"
			. "<!-- wp:paragraph -->\n<p>A workspace becomes useful when it reflects the way people actually work. Choose one project that already matters, and write the outcome in plain language.</p>\n<!-- /wp:paragraph -->\n\n"
			. "<!-- wp:heading -->\n<h2>Keep context close.</h2>\n<!-- /wp:heading -->\n\n"
			. "<!-- wp:paragraph -->\n<p>Keep the reasoning beside the action. Link the decision to the research that informed it.</p>\n<!-- /wp:paragraph -->\n\n"
			. "<!-- wp:heading -->\n<h2>Find a useful rhythm.</h2>\n<!-- /wp:heading -->\n\n"
			. "<!-- wp:paragraph -->\n<p>Consistency matters more than complexity. Agree on a small set of habits your team can keep.</p>\n<!-- /wp:paragraph -->";

		$ids[ 'story-' . $index ] = nextsaas_fixture_post(
			array(
				'post_title'    => $title,
				'post_name'     => $slug,
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_content'  => $body,
				'post_excerpt'  => 'A practical guide to a shared space that earns its place.',
				'post_date'     => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( $index + 1 ) . ' days' ) ),
				'post_category' => array( (int) get_cat_ID( ucwords( str_replace( '-', ' ', $category ) ) ) ),
				'tags_input'    => array( $tag ),
			),
		);
	}

	// The `primary` menu, so the header renders the assigned-menu branch.
	$menu_name = 'NextSaaS primary';
	$menu      = wp_get_nav_menu_object( $menu_name );

	if ( ! is_object( $menu ) ) {
		$menu_id = wp_create_nav_menu( $menu_name );

		foreach ( array( 'product' => 'The workspace', 'pricing' => 'Plans & pricing', 'about-us' => 'About us', 'contact-us' => 'Contact us' ) as $slug => $label ) {
			$target = nextsaas_fixture_post_id( $slug );

			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $label,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $target,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				),
			);
		}

		// One item with children, so the disclosure markup is exercised too.
		$parent = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => 'Resources',
				'menu-item-url'    => '#',
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			),
		);

		foreach ( array( 'faqs' => 'Your questions', 'archive' => 'The field guide' ) as $slug => $label ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $label,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $ids[ $slug ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $parent,
					'menu-item-description' => 'Practical ideas for better work.',
				),
			);
		}
	} else {
		$menu_id = (int) $menu->term_id;
	}

	$locations              = (array) get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary']   = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	// Permalinks must be pretty for the manifest URLs to resolve.
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules( false );

	return $ids;
}
