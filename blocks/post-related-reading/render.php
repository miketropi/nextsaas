<?php

/**
 * Server renderer for the Related reading block.
 *
 * Starts its own loop rather than inheriting the main query, because "related" is a
 * judgement the block makes: the post's own terms first, then the newest stories as
 * a fallback, so a site with a single category still shows something a reader can
 * follow. `nextsaas_posts_query_args()` owns the page-size clamp and the taxonomy
 * clauses, so a hand-edited save cannot widen the query.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_post   = nextsaas_queried_post();
$nextsaas_post_id = null === $nextsaas_post ? 0 : (int) $nextsaas_post->ID;

$nextsaas_terms = array();

if ( $nextsaas_post_id > 0 ) {
	foreach ( array( 'category', 'post_tag' ) as $nextsaas_taxonomy ) {
		$nextsaas_found = wp_get_post_terms( $nextsaas_post_id, $nextsaas_taxonomy, array( 'fields' => 'ids' ) );

		if ( is_array( $nextsaas_found ) ) {
			$nextsaas_terms[ 'category' === $nextsaas_taxonomy ? 'categories' : 'tags' ] = $nextsaas_found;
		}
	}
}

$nextsaas_args = nextsaas_posts_query_args(
	array_merge(
		array(
			'postsPerPage' => nextsaas_attr_int( $attributes, 'postsPerPage', 3 ),
		),
		$nextsaas_terms,
	),
	$nextsaas_post_id,
);

$nextsaas_related = new WP_Query( $nextsaas_args );

/*
 * A post that shares no terms with anything else would otherwise show an empty
 * section, so the newest stories stand in — without the current post, which is the
 * one thing a reader has already read.
 */
if ( ! $nextsaas_related->have_posts() && array() !== $nextsaas_terms ) {
	$nextsaas_related = new WP_Query(
		nextsaas_posts_query_args(
			array( 'postsPerPage' => nextsaas_attr_int( $attributes, 'postsPerPage', 3 ) ),
			$nextsaas_post_id,
		),
	);
}
?>
<section <?php
	echo nextsaas_section_attrs( 'post-related-reading', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
		<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
		<div class="post-grid">
			<?php
			while ( $nextsaas_related->have_posts() ) :
				$nextsaas_related->the_post();
				$nextsaas_index = (int) $nextsaas_related->current_post;
				$nextsaas_cat   = get_the_category();
				$nextsaas_label = array() !== $nextsaas_cat ? $nextsaas_cat[0]->name : '';
				?>
				<article class="post-card">
					<div class="editorial-art<?php echo 1 === $nextsaas_index % 2 ? ' dark' : ''; ?>" aria-hidden="true">
						<span class="meta">
							<span><?php echo esc_html( nextsaas_attr( $attributes, 'artLabel' ) ); ?></span>
							<span><?php echo esc_html( nextsaas_ordinal( $nextsaas_index ) ); ?></span>
						</span>
						<strong><?php echo esc_html( $nextsaas_label ); ?></strong>
					</div>
					<span class="meta"><?php echo esc_html( $nextsaas_label ); ?><?php echo '' !== $nextsaas_label ? ' / ' : ''; ?><?php echo esc_html( (string) nextsaas_reading_time( get_the_content() ) ); ?> <?php echo esc_html__( 'min read', 'nextsaas' ); ?></span>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p><?php echo esc_html( wp_trim_words( (string) get_the_excerpt(), 24 ) ); ?></p>
					<a class="text-link" href="<?php the_permalink(); ?>"><?php echo esc_html( nextsaas_attr( $attributes, 'readLabel' ) ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
