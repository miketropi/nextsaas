<?php
/**
 * Server renderer for the Product workflow block.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */
declare( strict_types=1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
$nextsaas_steps = nextsaas_items( $attributes, 'steps' );
?>
<section <?php echo nextsaas_section_attrs( 'product-workflow', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?> >
	<div class="container">
		<div class="grid-1-2">
			<div>
				<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
				<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
			</div>
			<div>
				<p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p>
				<div class="work-steps">
					<?php foreach ( $nextsaas_steps as $nextsaas_index => $nextsaas_step ) : ?>
						<article class="value-card">
							<span class="meta"><?php echo esc_html( (string) ( $nextsaas_step['meta'] ?? '' ) ); ?></span>
							<h3 data-nextsaas-rich="steps.<?php echo esc_attr( (string) $nextsaas_index ); ?>.heading"><?php echo nextsaas_rich( (string) ( $nextsaas_step['heading'] ?? '' ) ); ?></h3>
							<p data-nextsaas-rich="steps.<?php echo esc_attr( (string) $nextsaas_index ); ?>.body"><?php echo nextsaas_rich( (string) ( $nextsaas_step['body'] ?? '' ) ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
				<p class="note" data-nextsaas-rich="note"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'note' ) ); ?></p>
			</div>
		</div>
	</div>
</section>
