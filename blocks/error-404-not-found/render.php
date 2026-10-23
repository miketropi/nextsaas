<?php

/**
 * Server renderer for the Page not found hero.
 *
 * Static by nature: the whole point of the surface is that nothing was found, so the
 * only dynamic value is where the two calls to action lead. The section carries the
 * render's `error-hero` class and deliberately not `section`, because the designer's
 * hero padding is its own.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_primary = nextsaas_cta(
	array(
		'label'   => nextsaas_attr( $attributes, 'primaryLabel' ),
		'url'     => nextsaas_attr( $attributes, 'primaryUrl' ),
		'variant' => 'primary',
	),
);

$nextsaas_secondary = nextsaas_attr( $attributes, 'secondaryLabel' );
$nextsaas_secondary_url = nextsaas_attr( $attributes, 'secondaryUrl' );
?>
<section <?php
	echo nextsaas_section_attrs( 'error-404-not-found', $attributes, array( 'error-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
		<div class="error-number" data-nextsaas-rich="errorNumber"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'errorNumber' ) ); ?></div>
		<h1 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h1>
		<p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p>
		<div class="page-actions">
			<?php
			echo $nextsaas_primary; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_cta().

			if ( '' !== $nextsaas_secondary ) {
				printf(
					'<a class="text-link" href="%1$s" data-nextsaas-rich="secondaryLabel">%2$s %3$s</a>',
					esc_url( nextsaas_resolve_url( $nextsaas_secondary_url ) ),
					nextsaas_rich( $nextsaas_secondary ),
					nextsaas_icon( 'arrow-up-right' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.
				);
			}
			?>
		</div>
	</div>
</section>
