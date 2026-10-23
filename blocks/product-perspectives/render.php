<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
$nextsaas_perspectives = nextsaas_items( $attributes, 'perspectives' );
?>
<section <?php echo nextsaas_section_attrs( 'product-perspectives', $attributes, array( 'section', 'work-section' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?> >
	<div class="container">
		<div class="hero-split">
			<div>
				<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
				<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
			</div>
			<?php foreach ( $nextsaas_perspectives as $nextsaas_index => $nextsaas_perspective ) : ?>
				<div class="reading-copy">
					<h3 data-nextsaas-rich="perspectives.<?php echo esc_attr( (string) $nextsaas_index ); ?>.heading"><?php echo nextsaas_rich( (string) ( $nextsaas_perspective['heading'] ?? '' ) ); ?></h3>
					<p style="margin-top:24px" data-nextsaas-rich="perspectives.<?php echo esc_attr( (string) $nextsaas_index ); ?>.bodyOne"><?php echo nextsaas_rich( (string) ( $nextsaas_perspective['bodyOne'] ?? '' ) ); ?></p>
					<p data-nextsaas-rich="perspectives.<?php echo esc_attr( (string) $nextsaas_index ); ?>.bodyTwo"><?php echo nextsaas_rich( (string) ( $nextsaas_perspective['bodyTwo'] ?? '' ) ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
