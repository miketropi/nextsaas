<?php

/**
 * Server renderer for the FAQ answers block.
 *
 * The accordion is a native `<details>`/`<summary>` list, so it opens, closes and
 * prints with no JavaScript at all. The category filter is a row of `aria-pressed`
 * buttons and the search is a form: both are rendered server-side in their complete,
 * unfiltered state, and the bundle only narrows an already-readable list. That keeps
 * the no-JavaScript state from being a blank panel with a filter bar above it.
 *
 * Every string the bundle prints is read from a `data-*` attribute here, so
 * `view.ts` carries no English literal and a translator only has one file to change.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_items      = nextsaas_items( $attributes, 'items' );
$nextsaas_categories = nextsaas_items( $attributes, 'categories' );
$nextsaas_uid        = wp_unique_id( 'nextsaas-faq-' );
$nextsaas_count      = count( $nextsaas_items );
$nextsaas_count_text = sprintf(
	1 === $nextsaas_count
		? nextsaas_attr( $attributes, 'countOne', '%s question' )
		: nextsaas_attr( $attributes, 'countMany', '%s questions' ),
	number_format_i18n( $nextsaas_count ),
);
?>
<section <?php
	echo nextsaas_section_attrs( 'faq-answers', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="faq-layout" data-nextsaas-faq>
			<aside class="faq-sidebar">
				<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
				<div class="filter-options" role="group" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'filterLabel' ) ); ?>">
					<?php foreach ( $nextsaas_categories as $nextsaas_index => $nextsaas_category ) : ?>
						<?php
						$nextsaas_label = is_scalar( $nextsaas_category['label'] ?? '' ) ? (string) $nextsaas_category['label'] : '';
						$nextsaas_value = is_scalar( $nextsaas_category['value'] ?? '' ) ? (string) $nextsaas_category['value'] : $nextsaas_label;

						if ( '' === $nextsaas_label ) {
							continue;
						}
						?>
						<button
							type="button"
							aria-pressed="<?php echo 0 === $nextsaas_index ? 'true' : 'false'; ?>"
							data-faq-category="<?php echo esc_attr( $nextsaas_value ); ?>"
						><?php echo esc_html( $nextsaas_label ); ?></button>
					<?php endforeach; ?>
				</div>
				<p class="note"><?php echo esc_html( nextsaas_attr( $attributes, 'noteLabel' ) ); ?></p>
				<a class="text-link" href="<?php echo esc_url( nextsaas_resolve_url( nextsaas_attr( $attributes, 'noteLinkUrl' ) ) ); ?>"><?php echo esc_html( nextsaas_attr( $attributes, 'noteLinkLabel' ) ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
			</aside>
			<div>
				<form class="search-box faq-search" role="search" data-nextsaas-faq-search>
					<label class="sr-only" for="<?php echo esc_attr( $nextsaas_uid ); ?>-query"><?php echo esc_html( nextsaas_attr( $attributes, 'searchFieldLabel' ) ); ?></label>
					<input
						type="search"
						id="<?php echo esc_attr( $nextsaas_uid ); ?>-query"
						placeholder="<?php echo esc_attr( nextsaas_attr( $attributes, 'searchPlaceholder' ) ); ?>"
						data-nextsaas-faq-query
					>
					<button class="btn btn-secondary" type="submit" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'searchSubmitLabel' ) ); ?>"><?php echo nextsaas_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></button>
				</form>

				<p
					class="faq-count subtle"
					role="status"
					data-nextsaas-faq-count
					data-faq-count-one="<?php echo esc_attr( nextsaas_attr( $attributes, 'countOne' ) ); ?>"
					data-faq-count-many="<?php echo esc_attr( nextsaas_attr( $attributes, 'countMany' ) ); ?>"
				><?php echo esc_html( $nextsaas_count_text ); ?></p>

				<div class="faq" data-nextsaas-faq-items>
					<?php foreach ( $nextsaas_items as $nextsaas_item ) : ?>
						<?php
						$nextsaas_question = is_scalar( $nextsaas_item['question'] ?? '' ) ? (string) $nextsaas_item['question'] : '';
						$nextsaas_answer   = is_scalar( $nextsaas_item['answer'] ?? '' ) ? (string) $nextsaas_item['answer'] : '';
						$nextsaas_group    = is_scalar( $nextsaas_item['category'] ?? '' ) ? (string) $nextsaas_item['category'] : '';

						if ( '' === $nextsaas_question ) {
							continue;
						}
						?>
						<details data-category="<?php echo esc_attr( $nextsaas_group ); ?>">
							<summary><?php echo esc_html( $nextsaas_question ); ?><?php echo nextsaas_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></summary>
							<p><?php echo esc_html( $nextsaas_answer ); ?></p>
						</details>
					<?php endforeach; ?>
				</div>

				<div class="empty-state" data-nextsaas-faq-empty hidden>
					<h2 data-nextsaas-rich="emptyHeading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'emptyHeading' ) ); ?></h2>
					<p data-nextsaas-rich="emptyBody"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'emptyBody' ) ); ?></p>
					<button class="btn btn-secondary" type="button" data-nextsaas-faq-reset><?php echo esc_html( nextsaas_attr( $attributes, 'resetLabel' ) ); ?></button>
				</div>
			</div>
		</div>
	</div>
</section>
