<?php

/**
 * Server renderer for the article hero.
 *
 * Everything visible is the queried post's own data — title, primary category, date,
 * reading time, permalink — so the section is a template for one article rather than a
 * copy of one. Each value resolves through `nextsaas_queried_post()` and falls back to
 * the block's deterministic copy when no post is in context, which is exactly the state
 * the editor canvas renders in.
 *
 * The copy control prints the permalink it will copy, so the bundle never has to guess
 * where the request came from.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_post = nextsaas_queried_post();

$nextsaas_title = null === $nextsaas_post
	? nextsaas_attr( $attributes, 'fallbackTitle' )
	: (string) get_the_title( $nextsaas_post );

$nextsaas_lead = null === $nextsaas_post
	? nextsaas_attr( $attributes, 'fallbackLead' )
	: (string) get_the_excerpt( $nextsaas_post );

$nextsaas_terms = null === $nextsaas_post ? array() : get_the_category( $nextsaas_post->ID );

$nextsaas_primary_term = array() !== $nextsaas_terms && isset( $nextsaas_terms[0] )
	? (string) $nextsaas_terms[0]->name
	: '';

$nextsaas_category = '' !== $nextsaas_primary_term
	? $nextsaas_primary_term
	: nextsaas_attr( $attributes, 'fallbackCategory' );

$nextsaas_permalink = null === $nextsaas_post
	? home_url( '/' )
	: (string) get_permalink( $nextsaas_post );

$nextsaas_minutes = null === $nextsaas_post
	? 1
	: nextsaas_reading_time( $nextsaas_post->post_content );

$nextsaas_date = null === $nextsaas_post
	? nextsaas_attr( $attributes, 'datePrefix' )
	: (string) get_the_date( '', $nextsaas_post );
?>
<section <?php
	echo nextsaas_section_attrs( 'post-article-hero', $attributes, array( 'inner-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="breadcrumbs">
			<a href="<?php echo esc_url( nextsaas_resolve_url( nextsaas_attr( $attributes, 'homeUrl' ) ) ); ?>" data-nextsaas-rich="homeLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'homeLabel' ) ); ?></a>
			<?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?>
			<a href="<?php echo esc_url( nextsaas_resolve_url( nextsaas_attr( $attributes, 'archiveUrl' ) ) ); ?>" data-nextsaas-rich="archiveLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'archiveLabel' ) ); ?></a>
			<?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?>
			<span><?php echo esc_html( '' !== $nextsaas_primary_term ? $nextsaas_primary_term : __( 'Perspective', 'nextsaas' ) ); ?></span>
		</div>

		<div class="article-head">
			<p class="eyebrow" id="article-category"><?php echo esc_html( $nextsaas_category ); ?></p>
			<h1 class="inner-title" id="article-title"><?php echo esc_html( $nextsaas_title ); ?></h1>
			<p class="lead" id="article-desc" style="margin-top:32px;max-width:52ch"><?php echo esc_html( $nextsaas_lead ); ?></p>

			<div class="article-byline">
				<div>
					<strong data-nextsaas-rich="authorLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'authorLabel' ) ); ?></strong>
					<p class="meta"><?php echo esc_html( $nextsaas_date ); ?> / <span id="article-time"><?php echo esc_html( sprintf( _n( '%s min read', '%s min read', $nextsaas_minutes, 'nextsaas' ), number_format_i18n( $nextsaas_minutes ) ) ); ?></span></p>
				</div>
				<button
					class="btn btn-secondary"
					id="copy-article"
					type="button"
					aria-controls="copy-status"
					data-nextsaas-copy
					data-article-url="<?php echo esc_url( $nextsaas_permalink ); ?>"
				><span data-nextsaas-rich="copyLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'copyLabel' ) ); ?></span> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></button>
			</div>
			<p class="form-status" id="copy-status" role="status" data-nextsaas-copy-status></p>
		</div>
	</div>
</section>
