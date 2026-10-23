<?php

/**
 * Server renderer for the Site search block.
 *
 * The block owns the form, the type filter and the design's "A FEW PLACES TO BEGIN"
 * default state. It deliberately does NOT render results: the `search.html` template
 * owns the results loop through `core/query` with `inherit`, which is what makes the
 * search inherit the real request — the term, the type filter and the paging
 * argument — without the theme re-implementing WordPress's search query.
 *
 * Every filter control is a link rather than a script-driven toggle, so a search can
 * be narrowed, shared and bookmarked with JavaScript off, and each one keeps the
 * other query arguments by rebuilding the request URL instead of replacing it.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_term = isset( $_GET['s'] ) && is_scalar( $_GET['s'] )
	? sanitize_text_field( wp_unslash( (string) $_GET['s'] ) )
	: '';

$nextsaas_type = isset( $_GET['post_type'] ) && is_scalar( $_GET['post_type'] )
	? sanitize_key( wp_unslash( (string) $_GET['post_type'] ) )
	: '';

$nextsaas_active = nextsaas_search_post_type( $nextsaas_type );

$nextsaas_found = isset( $GLOBALS['wp_query'] ) && $GLOBALS['wp_query'] instanceof WP_Query
	? (int) $GLOBALS['wp_query']->found_posts
	: 0;

$nextsaas_filters = array(
	''       => nextsaas_attr( $attributes, 'allLabel' ),
	'page'   => nextsaas_attr( $attributes, 'pagesLabel' ),
	'post'   => nextsaas_attr( $attributes, 'articlesLabel' ),
);
?>
<section <?php
	echo nextsaas_section_attrs( 'search-site-search', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<form class="search-box" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-nextsaas-search>
			<label class="sr-only" for="nextsaas-search-query"><?php echo esc_html( nextsaas_attr( $attributes, 'fieldLabel' ) ); ?></label>
			<input
				type="search"
				id="nextsaas-search-query"
				name="s"
				value="<?php echo esc_attr( $nextsaas_term ); ?>"
				placeholder="<?php echo esc_attr( nextsaas_attr( $attributes, 'placeholder' ) ); ?>"
				autocomplete="off"
			>
			<?php if ( '' !== $nextsaas_active ) : ?>
				<input type="hidden" name="post_type" value="<?php echo esc_attr( $nextsaas_active ); ?>">
			<?php endif; ?>
			<button class="btn btn-primary" type="submit">
				<?php echo esc_html( nextsaas_attr( $attributes, 'submitLabel' ) ); ?>
				<?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?>
			</button>
		</form>

		<div class="filter-bar">
			<nav class="filter-options" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'filterLabel' ) ); ?>">
				<?php foreach ( $nextsaas_filters as $nextsaas_value => $nextsaas_label ) : ?>
					<?php if ( '' === (string) $nextsaas_label ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<a
						href="<?php echo esc_url( nextsaas_search_url( $nextsaas_term, (string) $nextsaas_value ) ); ?>"
						<?php echo $nextsaas_value === $nextsaas_active ? ' aria-current="true"' : ''; ?>
					><?php echo esc_html( (string) $nextsaas_label ); ?></a>
				<?php endforeach; ?>
			</nav>
			<span class="meta" role="status" data-nextsaas-search-count><?php echo esc_html( nextsaas_search_count_text( $nextsaas_found, $nextsaas_term ) ); ?></span>
		</div>

		<?php if ( '' === $nextsaas_term ) : ?>
			<div class="search-default">
				<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
				<div class="quick-links">
					<?php foreach ( nextsaas_items( $attributes, 'quickLinks' ) as $nextsaas_link ) : ?>
						<?php
						$nextsaas_link_label = is_scalar( $nextsaas_link['label'] ?? '' ) ? (string) $nextsaas_link['label'] : '';
						$nextsaas_link_url   = is_scalar( $nextsaas_link['url'] ?? '' ) ? (string) $nextsaas_link['url'] : '';

						if ( '' === $nextsaas_link_label ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( nextsaas_resolve_url( $nextsaas_link_url ) ); ?>"><?php echo esc_html( $nextsaas_link_label ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
