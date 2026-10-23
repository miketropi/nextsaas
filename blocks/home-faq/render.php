<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
$items = nextsaas_items( $attributes, 'items' ); ?>
<section <?php echo nextsaas_section_attrs( 'home-faq', $attributes, array( 'section', 'faq' ) ); ?>><div class="container grid-1-2"><div><p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p><h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2></div><div><?php foreach ( $items as $index => $item ) : ?><details<?php echo 0 === $index ? ' open' : ''; ?>><summary data-nextsaas-rich="items.<?php echo esc_attr( (string) $index ); ?>.heading"><?php echo esc_html( (string) ( $item['heading'] ?? '' ) ); ?><?php echo nextsaas_icon( 'plus' ); ?></summary><p data-nextsaas-rich="items.<?php echo esc_attr( (string) $index ); ?>.body"><?php echo esc_html( (string) ( $item['body'] ?? '' ) ); ?></p></details><?php endforeach; ?></div></div></section>
