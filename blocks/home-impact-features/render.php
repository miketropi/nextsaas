<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 ); if ( ! defined( 'ABSPATH' ) ) { exit; } $nextsaas_items = nextsaas_items( $attributes, 'items' );
?>
<section <?php echo nextsaas_section_attrs( 'home-impact-features', $attributes, array( 'section', 'impact' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="container"><div class="section-heading"><p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p><div class="heading-row"><h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2><p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p></div></div><div class="grid-3 impact-grid" data-nextsaas-gap="grid"><?php foreach ( $nextsaas_items as $nextsaas_i => $nextsaas_item ) : ?><?php
	/*
	 * The index keeps its number and its layout; only the graphic changes. An item with
	 * its own icon image replaces the SVG, one without falls back to it, and a new row
	 * with neither renders no graphic rather than an empty reservation.
	 */
	$nextsaas_item_image = array(
		'mediaId'  => nextsaas_attr_int( $nextsaas_item, 'mediaId' ),
		'mediaUrl' => nextsaas_attr( $nextsaas_item, 'mediaUrl' ),
		'mediaAlt' => nextsaas_attr( $nextsaas_item, 'mediaAlt' ),
	);
	?><article class="feature card-flat impact-card"><div class="impact-index"><span data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_i ); ?>.meta"><?php echo nextsaas_rich( (string) ( $nextsaas_item['meta'] ?? '' ) ); ?></span><?php if ( $nextsaas_item_image['mediaId'] > 0 || '' !== trim( $nextsaas_item_image['mediaUrl'] ) ) : ?><?php echo nextsaas_media( $nextsaas_item_image, 'nextsaas-icon-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().?><?php else : ?><?php echo nextsaas_icon( (string) ( $nextsaas_item['icon'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?><?php endif; ?></div><h3 data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_i ); ?>.heading"><?php echo nextsaas_rich( (string) ( $nextsaas_item['heading'] ?? '' ) ); ?></h3><p data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_i ); ?>.body"><?php echo nextsaas_rich( (string) ( $nextsaas_item['body'] ?? '' ) ); ?></p></article><?php endforeach; ?></div><p class="impact-note" data-nextsaas-rich="note"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'note' ) ); ?></p></div></section>
