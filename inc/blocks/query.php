<?php

/**
 * Query arguments shared by the NextSaaS surfaces that run their own loop.
 *
 * The archive, search, index and single templates delegate to `core/query` and
 * inherit the main query, so nothing here duplicates WordPress's own request
 * parsing for those. What remains are the two blocks that really do start their own
 * loop — the latest-posts carousel and the related-reading list — plus the
 * whitelists the filter links use so a hand-edited URL cannot widen a query.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalise a stored list of term ids.
 *
 * Block attributes come from saved post content, so a value can be a scalar, a list
 * of numeric strings, or carry duplicates and zeros. Only positive integers
 * survive, in the order they were saved, because `category__in` / `tag__in` treat a
 * zero as a real term id and would otherwise silently filter everything out.
 *
 * @param mixed $value Raw attribute value.
 *
 * @return array<int, int> Unique positive term ids.
 */
function nextsaas_query_term_ids( mixed $value ): array {
	$candidates = is_array( $value ) ? $value : array( $value );
	$ids        = array();

	foreach ( $candidates as $candidate ) {
		if ( ! is_scalar( $candidate ) ) {
			continue;
		}

		$id = (int) $candidate;

		if ( $id > 0 ) {
			$ids[] = $id;
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Query arguments for a latest-posts list.
 *
 * A value that is not a number at all is a hand-edited save, not a page size: it
 * falls back to the attribute default instead of collapsing the list to one card.
 * An empty filter list adds no clause at all, so an unfiltered block keeps exactly
 * the arguments it had before the filters existed.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param int                  $exclude_id Optional post to leave out of the list.
 *
 * @return array<string, mixed> Arguments for `WP_Query`.
 */
function nextsaas_posts_query_args( array $attributes, int $exclude_id = 0 ): array {
	$posts_per_page = $attributes['postsPerPage'] ?? 3;

	if ( ! is_numeric( $posts_per_page ) ) {
		$posts_per_page = 3;
	}

	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => max( 1, min( 24, (int) $posts_per_page ) ),
		'ignore_sticky_posts'    => false,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => true,
	);

	if ( $exclude_id > 0 ) {
		$args['post__not_in'] = array( $exclude_id );
	}

	$categories = nextsaas_query_term_ids( $attributes['categories'] ?? array() );
	$tags       = nextsaas_query_term_ids( $attributes['tags'] ?? array() );

	if ( array() !== $categories ) {
		$args['category__in'] = $categories;
	}

	if ( array() !== $tags ) {
		$args['tag__in'] = $tags;
	}

	return $args;
}

/**
 * Post types a NextSaaS search may look in.
 *
 * @return array<int, string> Pages and posts, plus the product post type when the
 *                            store plugin actually registers one.
 */
function nextsaas_search_available_types(): array {
	$types = array( 'page', 'post' );

	if ( post_type_exists( 'product' ) ) {
		$types[] = 'product';
	}

	return $types;
}

/**
 * Resolve a search type filter into a post type.
 *
 * The value arrives from a query string, where it can hold any of the available
 * types — or anything at all. Only a whitelisted value survives, so a hand-edited
 * `?post_type=` can never widen a search to a private or unregistered type; an
 * unknown value falls back to "all types" rather than to an error state.
 *
 * @param string $filter Sanitised filter value from the request.
 *
 * @return string Post type to restrict to, `''` for "no restriction".
 */
function nextsaas_search_post_type( string $filter ): string {
	$filter = strtolower( trim( $filter ) );

	if ( in_array( $filter, nextsaas_search_available_types(), true ) ) {
		return $filter;
	}

	return '';
}

/**
 * Build the search URL for a term and an optional type filter.
 *
 * The filter links and the search form both go through here so the entered term,
 * the active filter and the paging argument agree, and so the request keeps its
 * query string across a filter change.
 *
 * @param string $term Search term, may be empty.
 * @param string $type Post type filter, `''` for all.
 *
 * @return string Ready-to-escape URL.
 */
function nextsaas_search_url( string $term, string $type = '' ): string {
	$args = array();

	if ( '' !== trim( $term ) ) {
		$args['s'] = trim( $term );
	}

	$type = nextsaas_search_post_type( $type );

	if ( '' !== $type ) {
		$args['post_type'] = $type;
	}

	if ( array() === $args ) {
		return home_url( '/' );
	}

	return add_query_arg( $args, home_url( '/' ) );
}

/**
 * Build the label for a result count.
 *
 * @param int    $count Number of results.
 * @param string $term  The search term the count belongs to.
 *
 * @return string Human-readable count, e.g. `3 results for “workspace”`.
 */
function nextsaas_search_count_text( int $count, string $term ): string {
	$term = trim( $term );

	if ( '' === $term ) {
		return (string) __( 'Search pages and articles', 'nextsaas' );
	}

	return sprintf(
		/* translators: %1$s: number of results. %2$s: the search term. */
		_n( '%1$s result for “%2$s”', '%1$s results for “%2$s”', $count, 'nextsaas' ),
		number_format_i18n( $count ),
		$term,
	);
}
