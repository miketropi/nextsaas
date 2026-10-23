<?php

/**
 * Query and URL boundary tests.
 *
 * Everything here is a boundary: a value arriving from saved post content, from a
 * query string, or from the editor, being narrowed into something the theme is
 * willing to act on. Each assertion is about the decision, so a regression shows up
 * as a changed boundary rather than a changed string.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

/**
 * Query arguments, URL resolution and value normalisation.
 */
final class QueryTest extends TestCase {

	/**
	 * Reset the doubles before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		nextsaas_test_reset();
	}

	/**
	 * Term ids survive whatever shape saved content stored them in.
	 */
	public function test_term_ids_are_normalised(): void {
		$this->assertSame( array( 3, 7 ), nextsaas_query_term_ids( array( '3', 7, 0, -2, '7' ) ) );
		$this->assertSame( array( 5 ), nextsaas_query_term_ids( 5 ) );
		$this->assertSame( array(), nextsaas_query_term_ids( array( 0, 'x', null, array( 4 ) ) ) );
		$this->assertSame( array(), nextsaas_query_term_ids( array() ) );
	}

	/**
	 * The page size is clamped, and a non-numeric save falls back to the default.
	 */
	public function test_posts_query_args_clamp_the_page_size(): void {
		$defaults = nextsaas_posts_query_args( array() );

		$this->assertSame( 3, $defaults['posts_per_page'] );

		$low = nextsaas_posts_query_args( array( 'postsPerPage' => 0 ) );

		$this->assertSame( 1, $low['posts_per_page'] );

		$high = nextsaas_posts_query_args( array( 'postsPerPage' => 500 ) );

		$this->assertSame( 24, $high['posts_per_page'] );

		$hand_edited = nextsaas_posts_query_args( array( 'postsPerPage' => 'lots' ) );

		$this->assertSame( 3, $hand_edited['posts_per_page'] );
	}

	/**
	 * An empty filter list adds no clause; a populated one narrows the query.
	 */
	public function test_posts_query_args_only_add_taxonomy_clauses_when_set(): void {
		$unfiltered = nextsaas_posts_query_args( array() );

		$this->assertArrayNotHasKey( 'category__in', $unfiltered );
		$this->assertArrayNotHasKey( 'tag__in', $unfiltered );

		$filtered = nextsaas_posts_query_args(
			array(
				'categories' => array( 4 ),
				'tags'       => array( 9, 9 ),
			),
		);

		$this->assertSame( array( 4 ), $filtered['category__in'] );
		$this->assertSame( array( 9 ), $filtered['tag__in'] );
	}

	/**
	 * The exclusion only appears when there is something to exclude.
	 */
	public function test_posts_query_args_exclude_only_when_asked(): void {
		$this->assertArrayNotHasKey( 'post__not_in', nextsaas_posts_query_args( array() ) );
		$this->assertSame( array( 12 ), nextsaas_posts_query_args( array(), 12 )['post__not_in'] );
	}

	/**
	 * A search type filter is whitelisted, so a hand-edited URL cannot widen a search.
	 */
	public function test_search_post_type_is_whitelisted(): void {
		$this->assertSame( 'page', nextsaas_search_post_type( 'page' ) );
		$this->assertSame( 'post', nextsaas_search_post_type( ' POST ' ) );
		$this->assertSame( '', nextsaas_search_post_type( 'attachment' ) );
		$this->assertSame( '', nextsaas_search_post_type( 'wp_user' ) );
		$this->assertSame( '', nextsaas_search_post_type( '' ) );
	}

	/**
	 * A store post type joins the whitelist only when it is really registered.
	 */
	public function test_search_types_follow_registered_post_types(): void {
		$this->assertSame( array( 'page', 'post' ), nextsaas_search_available_types() );

		$GLOBALS['nextsaas_test']['post_types'][] = 'product';

		$this->assertSame( array( 'page', 'post', 'product' ), nextsaas_search_available_types() );
		$this->assertSame( 'product', nextsaas_search_post_type( 'product' ) );
	}

	/**
	 * Search URLs keep the term and the filter, and fall back to the site root.
	 */
	public function test_search_urls_keep_term_and_filter(): void {
		$this->assertSame( 'https://example.test/', nextsaas_search_url( '' ) );
		$this->assertSame( 'https://example.test/?s=workspace', nextsaas_search_url( ' workspace ' ) );
		$this->assertSame(
			'https://example.test/?s=workspace&post_type=page',
			nextsaas_search_url( 'workspace', 'page' ),
		);
		$this->assertSame(
			'https://example.test/?s=workspace',
			nextsaas_search_url( 'workspace', 'attachment' ),
		);
	}

	/**
	 * The result count wording is the approved wording, singular and plural.
	 */
	public function test_search_count_text(): void {
		$this->assertSame( 'Search pages and articles', nextsaas_search_count_text( 0, '' ) );
		$this->assertSame( '1 result for “workspace”', nextsaas_search_count_text( 1, 'workspace' ) );
		$this->assertSame( '3 results for “workspace”', nextsaas_search_count_text( 3, 'workspace' ) );
	}

	/**
	 * The approved render links page to page by file name; those values map onto a
	 * live site.
	 */
	public function test_design_filenames_resolve_to_live_urls(): void {
		$this->assertSame( 'https://example.test/', nextsaas_resolve_url( '' ) );
		$this->assertSame( 'https://example.test/', nextsaas_resolve_url( '@home' ) );
		$this->assertSame( 'https://example.test/', nextsaas_resolve_url( 'forma-saas-landing.html' ) );
		$this->assertSame( 'https://example.test/product/', nextsaas_resolve_url( 'product.html' ) );
		$this->assertSame( 'https://example.test/product/#control', nextsaas_resolve_url( 'product.html#control' ) );
		$this->assertSame( 'https://example.test/#capabilities', nextsaas_resolve_url( '@home#capabilities' ) );
		$this->assertSame( '#top', nextsaas_resolve_url( '#top' ) );
		$this->assertSame( 'https://elsewhere.test/x', nextsaas_resolve_url( 'https://elsewhere.test/x' ) );
		$this->assertSame( 'https://example.test/?s=', nextsaas_resolve_url( '@search' ) );
	}

	/**
	 * The posts page is honoured when one is set, and the front page is the fallback.
	 */
	public function test_posts_page_url_falls_back_to_the_front_page(): void {
		$this->assertSame( 'https://example.test/', nextsaas_resolve_url( '@posts' ) );

		$GLOBALS['nextsaas_test']['options']['page_for_posts'] = 21;
		$GLOBALS['nextsaas_test']['permalink']                 = 'https://example.test/journal/';

		$this->assertSame( 'https://example.test/journal/', nextsaas_resolve_url( '@posts' ) );
	}

	/**
	 * The newest post supplies `@latest-post`, and `blog-post.html` is its alias.
	 */
	public function test_latest_post_alias(): void {
		$this->assertSame( 'https://example.test/', nextsaas_resolve_url( '@latest-post' ) );

		$GLOBALS['nextsaas_test']['posts']      = array( (object) array( 'ID' => 5 ) );
		$GLOBALS['nextsaas_test']['permalink']  = 'https://example.test/start-with-the-work/';

		$this->assertSame( 'https://example.test/start-with-the-work/', nextsaas_resolve_url( '@latest-post' ) );
		$this->assertSame( 'https://example.test/start-with-the-work/', nextsaas_resolve_url( 'blog-post.html' ) );
	}

	/**
	 * Reading time never reports less than a minute.
	 */
	public function test_reading_time(): void {
		$this->assertSame( 1, nextsaas_reading_time( '' ) );
		$this->assertSame( 1, nextsaas_reading_time( '<p>One short sentence.</p>' ) );
		$this->assertSame( 2, nextsaas_reading_time( implode( ' ', array_fill( 0, 221, 'word' ) ) ) );
	}

	/**
	 * Ordinals are the editorial numbering the design uses.
	 */
	public function test_ordinals_are_zero_padded(): void {
		$this->assertSame( '01', nextsaas_ordinal( 0 ) );
		$this->assertSame( '09', nextsaas_ordinal( 8 ) );
		$this->assertSame( '10', nextsaas_ordinal( 9 ) );
	}

	/**
	 * `nextsaas_link_is_current` never claims a fragment or an off-site link.
	 */
	public function test_link_is_current_ignores_fragments_and_off_site_urls(): void {
		$this->assertFalse( nextsaas_link_is_current( '' ) );
		$this->assertFalse( nextsaas_link_is_current( '#top' ) );
		$this->assertFalse( nextsaas_link_is_current( 'https://elsewhere.test/' ) );
	}

	/**
	 * On a singular request the current permalink is marked, and nothing else is.
	 */
	public function test_link_is_current_marks_the_singular_request(): void {
		$GLOBALS['nextsaas_test']['singular']  = true;
		$GLOBALS['nextsaas_test']['permalink'] = 'https://example.test/about-us/';

		$this->assertTrue( nextsaas_link_is_current( 'about-us.html' ) );
		$this->assertFalse( nextsaas_link_is_current( 'pricing.html' ) );
	}

	/**
	 * A search request marks the search endpoint.
	 */
	public function test_link_is_current_marks_search(): void {
		$GLOBALS['nextsaas_test']['search'] = true;

		$this->assertTrue( nextsaas_link_is_current( '@search' ) );
		$this->assertFalse( nextsaas_link_is_current( 'pricing.html' ) );
	}
}
