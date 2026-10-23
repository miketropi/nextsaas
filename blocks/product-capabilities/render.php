<?php

/**
 * Server renderer for the Product capabilities block.
 *
 * The approved render is a tab list over three panels. The tab pattern is written
 * server-side and completely: the first tab is selected, the others are
 * `hidden`, and every `aria-controls`/`aria-labelledby` pair points at an element
 * that exists. `view.ts` therefore only has to move the selection — with JavaScript
 * absent the first panel is a complete, readable summary of the section rather than
 * a blank tab shell.
 *
 * Each panel reserves its own product screenshot with the labelled placeholder, so
 * an editor can drop in real images one panel at a time.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_panels = nextsaas_items( $attributes, 'panels' );
$nextsaas_group  = 'nextsaas-product-capabilities';
?>
<section <?php
	echo nextsaas_section_attrs( 'product-capabilities', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="heading-row section-heading">
			<div>
				<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
				<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
			</div>
			<p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p>
		</div>
		<div class="product-tabs" role="tablist" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'tabListLabel' ) ); ?>">
			<?php foreach ( $nextsaas_panels as $nextsaas_index => $nextsaas_panel ) : ?>
				<button
					id="<?php echo esc_attr( $nextsaas_group . '-tab-' . $nextsaas_index ); ?>"
					type="button"
					role="tab"
					aria-selected="<?php echo 0 === $nextsaas_index ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr( $nextsaas_group . '-panel-' . $nextsaas_index ); ?>"
					tabindex="<?php echo 0 === $nextsaas_index ? '0' : '-1'; ?>"
					data-nextsaas-tab
				><?php echo esc_html( is_scalar( $nextsaas_panel['tabLabel'] ?? '' ) ? (string) $nextsaas_panel['tabLabel'] : '' ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php foreach ( $nextsaas_panels as $nextsaas_index => $nextsaas_panel ) : ?>
			<div
				class="product-panel"
				id="<?php echo esc_attr( $nextsaas_group . '-panel-' . $nextsaas_index ); ?>"
				role="tabpanel"
				aria-labelledby="<?php echo esc_attr( $nextsaas_group . '-tab-' . $nextsaas_index ); ?>"
				tabindex="0"
				<?php echo 0 === $nextsaas_index ? '' : 'hidden'; ?>
			>
				<div class="hero-split">
					<div>
						<h3 data-nextsaas-rich="panels.<?php echo esc_attr( (string) $nextsaas_index ); ?>.heading"><?php echo nextsaas_rich( is_scalar( $nextsaas_panel['heading'] ?? '' ) ? (string) $nextsaas_panel['heading'] : '' ); ?></h3>
						<p data-nextsaas-rich="panels.<?php echo esc_attr( (string) $nextsaas_index ); ?>.body"><?php echo nextsaas_rich( is_scalar( $nextsaas_panel['body'] ?? '' ) ? (string) $nextsaas_panel['body'] : '' ); ?></p>
						<ul class="feature-list">
							<?php
							$nextsaas_features = $nextsaas_panel['features'] ?? array();

							if ( is_array( $nextsaas_features ) ) :
								foreach ( $nextsaas_features as $nextsaas_feature ) :
									if ( ! is_scalar( $nextsaas_feature ) ) {
										continue;
									}
									?>
									<li><?php echo nextsaas_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?><?php echo esc_html( (string) $nextsaas_feature ); ?></li>
									<?php
								endforeach;
							endif;
							?>
						</ul>
					</div>
					<?php
					echo nextsaas_media( $nextsaas_panel, 'image-reserve' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
