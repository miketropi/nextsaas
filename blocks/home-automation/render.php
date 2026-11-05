<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 ); if ( ! defined( 'ABSPATH' ) ) { exit; } $nextsaas_steps = nextsaas_items( $attributes, 'steps' );
?>
<section <?php echo nextsaas_section_attrs( 'home-automation', $attributes, array( 'section', 'automation' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="container"><div class="grid-2-1"><div><p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p><h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2></div><p data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p></div><div class="automation-flow" data-nextsaas-gap="grid" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'flowLabel' ) ); ?>"><?php foreach ( $nextsaas_steps as $nextsaas_i => $nextsaas_step ) : ?><?php
	/*
	 * A step paints its own icon image when it has one; otherwise the SVG the design
	 * uses for that step renders, so a row that never got an image is unchanged and a
	 * row with neither graphic renders nothing at all.
	 */
	$nextsaas_step_image = array(
		'mediaId'  => nextsaas_attr_int( $nextsaas_step, 'mediaId' ),
		'mediaUrl' => nextsaas_attr( $nextsaas_step, 'mediaUrl' ),
		'mediaAlt' => nextsaas_attr( $nextsaas_step, 'mediaAlt' ),
	);
	?><article class="flow-item"><div class="flow-icon"><?php if ( $nextsaas_step_image['mediaId'] > 0 || '' !== trim( $nextsaas_step_image['mediaUrl'] ) ) : ?><?php echo nextsaas_media( $nextsaas_step_image, 'nextsaas-icon-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().?><?php else : ?><?php echo nextsaas_icon( (string) ( $nextsaas_step['icon'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?><?php endif; ?></div><span class="meta" data-nextsaas-rich="steps.<?php echo esc_attr( (string) $nextsaas_i ); ?>.meta"><?php echo nextsaas_rich( (string) ( $nextsaas_step['meta'] ?? '' ) ); ?></span><h3 data-nextsaas-rich="steps.<?php echo esc_attr( (string) $nextsaas_i ); ?>.heading"><?php echo nextsaas_rich( (string) ( $nextsaas_step['heading'] ?? '' ) ); ?></h3><p data-nextsaas-rich="steps.<?php echo esc_attr( (string) $nextsaas_i ); ?>.body"><?php echo nextsaas_rich( (string) ( $nextsaas_step['body'] ?? '' ) ); ?></p><?php if ( $nextsaas_i < count( $nextsaas_steps ) - 1 ) : ?><?php echo nextsaas_icon( 'arrow-right', array( 'class' => 'flow-arrow' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?><?php endif; ?></article><?php endforeach; ?></div><p class="pricing-note" data-nextsaas-rich="note"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'note' ) ); ?></p></div></section>
