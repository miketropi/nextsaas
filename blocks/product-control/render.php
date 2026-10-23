<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
$nextsaas_items = nextsaas_items( $attributes, 'items' );
?>
<section <?php echo nextsaas_section_attrs( 'product-control', $attributes, array( 'section' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?> >
	<div class="container">
		<div class="grid-1-2">
			<div>
				<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
				<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
			</div>
			<div class="grid-2">
				<?php foreach ( $nextsaas_items as $nextsaas_index => $nextsaas_item ) : ?>
					<article class="value-card">
						<h3 data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_index ); ?>.heading"><?php echo nextsaas_rich( (string) ( $nextsaas_item['heading'] ?? '' ) ); ?></h3>
						<p data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_index ); ?>.body"><?php echo nextsaas_rich( (string) ( $nextsaas_item['body'] ?? '' ) ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<p class="note" data-nextsaas-rich="note"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'note' ) ); ?></p>
	</div>
</section>
