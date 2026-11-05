<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 ); if ( ! defined( 'ABSPATH' ) ) { exit; }
$nextsaas_items = nextsaas_items( $attributes, 'items' ); $nextsaas_media = nextsaas_items( $attributes, 'media' );

/*
 * The two screenshots share one array attribute, and WordPress substitutes an array
 * default wholesale rather than per row, so a save carrying a single row would
 * otherwise leave the second slot without the placeholder copy the design approved.
 * The manifest's own default rows are merged underneath each saved row: an author's
 * id, URL and alt always win, and a slot nobody filled still renders its labelled
 * reservation.
 */
$nextsaas_capability_type = WP_Block_Type_Registry::get_instance()->get_registered( 'nextsaas/home-capabilities' );
$nextsaas_media_defaults = $nextsaas_capability_type instanceof WP_Block_Type && isset( $nextsaas_capability_type->attributes['media']['default'] ) && is_array( $nextsaas_capability_type->attributes['media']['default'] )
	? $nextsaas_capability_type->attributes['media']['default']
	: array();

$nextsaas_screenshots = array();

foreach ( array( 0, 1 ) as $nextsaas_slot ) {
	$nextsaas_default = is_array( $nextsaas_media_defaults[ $nextsaas_slot ] ?? null ) ? $nextsaas_media_defaults[ $nextsaas_slot ] : array();
	$nextsaas_saved   = is_array( $nextsaas_media[ $nextsaas_slot ] ?? null ) ? $nextsaas_media[ $nextsaas_slot ] : array();

	$nextsaas_screenshots[ $nextsaas_slot ] = array_merge( $nextsaas_default, $nextsaas_saved );
}
?>
<section <?php echo nextsaas_section_attrs( 'home-capabilities', $attributes, array( 'section' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="container"><div class="section-heading"><p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p><div class="heading-row"><h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2><p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p></div></div>
<div class="capability-grid" data-nextsaas-gap="grid"><?php foreach ( array_slice( $nextsaas_items, 0, 2 ) as $nextsaas_i => $nextsaas_item ) : ?><article class="capability"><?php echo nextsaas_media( $nextsaas_screenshots[ $nextsaas_i ] ?? array(), 'nextsaas-content-image capability-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().?><span class="num" data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_i ); ?>.meta"><?php echo nextsaas_rich( (string) ( $nextsaas_item['meta'] ?? '' ) ); ?></span><h3 data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_i ); ?>.heading"><?php echo nextsaas_rich( (string) ( $nextsaas_item['heading'] ?? '' ) ); ?></h3><p data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_i ); ?>.body"><?php echo nextsaas_rich( (string) ( $nextsaas_item['body'] ?? '' ) ); ?></p></article><?php endforeach; ?></div>
<div class="capability-bottom" data-nextsaas-gap="grid"><?php foreach ( array_slice( $nextsaas_items, 2 ) as $nextsaas_i => $nextsaas_item ) : $nextsaas_n = $nextsaas_i + 2; ?><article><h3 data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_n ); ?>.heading"><?php echo nextsaas_rich( (string) ( $nextsaas_item['heading'] ?? '' ) ); ?></h3><p data-nextsaas-rich="items.<?php echo esc_attr( (string) $nextsaas_n ); ?>.body"><?php echo nextsaas_rich( (string) ( $nextsaas_item['body'] ?? '' ) ); ?></p></article><?php endforeach; ?></div></div></section>
