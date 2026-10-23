<?php

/**
 * Server renderer for the journal carousel.
 *
 * The approved render hard-codes four mock posts; this renders the site's own, through
 * `nextsaas_posts_query_args()` so the page size is clamped and the optional taxonomy
 * filters only narrow the same latest-posts query.
 *
 * The track is a horizontally scrollable region — the design's own reading order — and
 * the two buttons are an additive convenience, which is why they are `aria-controls`
 * buttons rather than the only way through the list: with JavaScript off the track is
 * still a scrollable region with every card in it, announced as a carousel region and
 * focusable for arrow-key scrolling.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_query = new WP_Query( nextsaas_posts_query_args( $attributes, 0 ) );
$nextsaas_uid   = wp_unique_id( 'nextsaas-journal-' );
?>
<section <?php
	echo nextsaas_section_attrs( 'home-blog-posts-carousel', $attributes, array( 'section', 'blog-carousel' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="section-heading">
			<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
			<div class="heading-row">
				<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
				<p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p>
			</div>
		</div>

		<?php if ( $nextsaas_query->have_posts() ) : ?>
			<div class="carousel-toolbar" role="group" aria-label="<?php echo esc_attr__( 'Journal carousel controls', 'nextsaas' ); ?>">
				<span class="carousel-status" role="status" data-nextsaas-count><?php echo esc_html( '01 / ' . str_pad( (string) $nextsaas_query->post_count, 2, '0', STR_PAD_LEFT ) ); ?></span>
				<button class="carousel-btn" type="button" aria-label="<?php echo esc_attr__( 'Previous post', 'nextsaas' ); ?>" aria-controls="<?php echo esc_attr( $nextsaas_uid ); ?>" data-nextsaas-prev><?php echo nextsaas_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></button>
				<button class="carousel-btn" type="button" aria-label="<?php echo esc_attr__( 'Next post', 'nextsaas' ); ?>" aria-controls="<?php echo esc_attr( $nextsaas_uid ); ?>" data-nextsaas-next><?php echo nextsaas_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></button>
			</div>

			<div
				class="blog-track"
				id="<?php echo esc_attr( $nextsaas_uid ); ?>"
				role="region"
				aria-roledescription="<?php echo esc_attr__( 'carousel', 'nextsaas' ); ?>"
				aria-label="<?php echo esc_attr__( 'Journal posts', 'nextsaas' ); ?>"
				tabindex="0"
				data-nextsaas-track
			>
				<?php
				while ( $nextsaas_query->have_posts() ) :
					$nextsaas_query->the_post();
					$nextsaas_index    = (int) $nextsaas_query->current_post;
					$nextsaas_title    = get_the_title();
					$nextsaas_url      = get_permalink();
					$nextsaas_terms    = get_the_category();
					$nextsaas_group    = array() !== $nextsaas_terms ? $nextsaas_terms[0]->name : '';
					$nextsaas_minutes  = nextsaas_reading_time( (string) get_the_content() );

					if ( ! is_string( $nextsaas_url ) ) {
						continue;
					}
					?>
					<article class="blog-card">
						<div class="blog-art" aria-hidden="true">
							<span><?php echo esc_html( get_the_title() ); ?></span>
							<small><?php echo esc_html( nextsaas_attr( $attributes, 'artLabel', 'FIELD NOTES' ) . ' / ' . nextsaas_ordinal( $nextsaas_index ) ); ?></small>
						</div>
						<span class="meta"><?php echo esc_html( $nextsaas_group ); ?><?php echo '' !== $nextsaas_group ? ' · ' : ''; ?><?php echo esc_html( sprintf( _n( '%s MIN READ', '%s MIN READ', $nextsaas_minutes, 'nextsaas' ), number_format_i18n( $nextsaas_minutes ) ) ); ?></span>
						<h3><?php echo esc_html( is_string( $nextsaas_title ) ? $nextsaas_title : '' ); ?></h3>
						<p><?php echo esc_html( wp_trim_words( (string) get_the_excerpt(), 22 ) ); ?></p>
						<a class="btn btn-ghost text-link" href="<?php echo esc_url( $nextsaas_url ); ?>"><?php echo esc_html( nextsaas_attr( $attributes, 'readLabel' ) ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<p class="carousel-help" data-nextsaas-rich="help"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'help' ) ); ?></p>
		<?php else : ?>
			<p class="lead" data-nextsaas-rich="emptyNotice"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'emptyNotice' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
