<?php

/**
 * Server renderer for the landing pricing block.
 *
 * Both billing periods are printed into the markup — the monthly figure is the
 * visible one, the yearly figure rides along as a `data-` attribute — so the toggle in
 * `view.ts` only swaps text that is already there. A visitor without JavaScript reads
 * the complete monthly offer, which is the same offer the approved render opens with,
 * and nothing in the section depends on a script to be legible.
 *
 * The rates are illustrative and the render says so; that note is an attribute rather
 * than hard-coded copy, because a site that publishes real prices has to be able to
 * take it away.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_plans = nextsaas_items( $attributes, 'plans' );
?>
<section <?php
	echo nextsaas_section_attrs( 'home-pricing', $attributes, array( 'section', 'pricing' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="section-heading">
			<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
			<div class="heading-row">
				<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
				<p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p>
			</div>
		</div>

		<div class="billing" data-nextsaas-billing>
			<div class="billing-toggle" role="group" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'billingLabel' ) ); ?>">
				<button type="button" aria-pressed="true" data-billing="monthly"><?php echo esc_html( nextsaas_attr( $attributes, 'monthlyLabel' ) ); ?></button>
				<button type="button" aria-pressed="false" data-billing="yearly"><?php echo esc_html( nextsaas_attr( $attributes, 'yearlyLabel' ) ); ?></button>
			</div>
			<p data-nextsaas-rich="offerNote"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'offerNote' ) ); ?></p>
		</div>

		<div class="price-grid" data-nextsaas-gap="grid">
			<?php foreach ( $nextsaas_plans as $nextsaas_index => $nextsaas_plan ) : ?>
				<?php
				/*
				 * Rows are read through `nextsaas_attr()`, which is the theme's single
				 * "read a scalar attribute" guard: a row saved before a key existed (or a
				 * hand-edited one) is missing it, and `?? ''` alone would still warn on the
				 * second, unguarded access.
				 */
				$nextsaas_name       = nextsaas_attr( $nextsaas_plan, 'name' );
				$nextsaas_features   = $nextsaas_plan['features'] ?? array();
				$nextsaas_monthly    = nextsaas_attr( $nextsaas_plan, 'monthlyLabel' );
				$nextsaas_yearly     = nextsaas_attr( $nextsaas_plan, 'yearlyLabel', $nextsaas_monthly );
				$nextsaas_note       = nextsaas_attr( $nextsaas_plan, 'note' );
				$nextsaas_meta       = nextsaas_attr( $nextsaas_plan, 'meta' );
				$nextsaas_text       = nextsaas_attr( $nextsaas_plan, 'description' );
				$nextsaas_unit       = nextsaas_attr( $nextsaas_plan, 'unit' );
				$nextsaas_featured   = ! empty( $nextsaas_plan['featured'] );

				if ( '' === $nextsaas_name ) {
					continue;
				}
				?>
				<article class="plan<?php echo $nextsaas_featured ? ' featured' : ''; ?>" data-nextsaas-plan="<?php echo esc_attr( $nextsaas_name ); ?>">
					<span class="meta"><?php echo esc_html( $nextsaas_meta ); ?></span>
					<h3><?php echo esc_html( $nextsaas_name ); ?></h3>
					<p><?php echo esc_html( $nextsaas_text ); ?></p>
					<div class="price num">
						<span
							data-nextsaas-price
							data-monthly="<?php echo esc_attr( $nextsaas_monthly ); ?>"
							data-yearly="<?php echo esc_attr( $nextsaas_yearly ); ?>"
							data-nextsaas-rich="plans.<?php echo esc_attr( (string) $nextsaas_index ); ?>.monthlyLabel"
						><?php echo esc_html( $nextsaas_monthly ); ?></span>
						<small><?php echo esc_html( $nextsaas_unit ); ?></small>
					</div>
					<div
						class="price-note"
						data-nextsaas-price-note
						data-monthly="<?php echo esc_attr( '' !== $nextsaas_note ? $nextsaas_note : nextsaas_attr( $attributes, 'monthlyNote' ) ); ?>"
						data-yearly="<?php echo esc_attr( nextsaas_attr( $attributes, 'yearlyNote' ) ); ?>"
					><?php echo esc_html( '' !== $nextsaas_note ? $nextsaas_note : nextsaas_attr( $attributes, 'monthlyNote' ) ); ?></div>
					<ul>
						<?php if ( is_array( $nextsaas_features ) ) : ?>
							<?php foreach ( $nextsaas_features as $nextsaas_feature ) : ?>
								<?php
								// A blank line is a line the author is still typing, not a
								// feature: it stays in the stored list and renders nothing.
								$nextsaas_feature_text = is_scalar( $nextsaas_feature ) ? trim( (string) $nextsaas_feature ) : '';

								if ( '' === $nextsaas_feature_text ) {
									continue;
								}
								?>
								<li><?php echo nextsaas_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?><?php echo esc_html( $nextsaas_feature_text ); ?></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
					<?php
					echo nextsaas_cta(
						array(
							'label'    => nextsaas_attr( $nextsaas_plan, 'ctaLabel' ),
							'url'      => nextsaas_attr( $nextsaas_plan, 'ctaUrl' ),
							'variant'  => 'secondary',
							'arrow'    => true,
							'richPath' => 'plans.' . (string) $nextsaas_index . '.ctaLabel',
						),
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_cta().
					?>
				</article>
			<?php endforeach; ?>
		</div>

		<p
			class="pricing-note"
			aria-live="polite"
			data-nextsaas-billing-note
			data-monthly="<?php echo esc_attr( nextsaas_attr( $attributes, 'monthlyNotice' ) ); ?>"
			data-yearly="<?php echo esc_attr( nextsaas_attr( $attributes, 'yearlyNotice' ) ); ?>"
		><?php echo esc_html( nextsaas_attr( $attributes, 'monthlyNotice' ) ); ?></p>
	</div>
</section>
