<?php

/**
 * Server renderer for the Closing call to action block.
 *
 * Text content stays on the same line as its tag — extra whitespace inside a
 * text-bearing element changes how the theme's `text-wrap` rules balance lines.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_cta = nextsaas_cta(
	array(
		'label'   => nextsaas_attr( $attributes, 'ctaLabel' ),
		'url'     => nextsaas_attr( $attributes, 'ctaUrl' ),
		'variant' => nextsaas_attr( $attributes, 'ctaVariant', 'primary' ),
		'arrow'   => nextsaas_attr_bool( $attributes, 'ctaArrow' ),
		'icon'    => nextsaas_attr_bool( $attributes, 'ctaIcon', true ),
	),
);
?>
<section <?php
	echo nextsaas_section_attrs( 'closing-cta', $attributes, array( 'section', 'closing' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
		<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
		<p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p>
		<?php echo $nextsaas_cta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_cta().?>
	</div>
</section>
