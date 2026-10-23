<?php

/**
 * Server renderer for the Product workspace preview block.
 *
 * The approved render reserves this slot for a real screenshot rather than faking
 * one, so the media helper is asked for an image first and only falls back to the
 * labelled reservation box when no asset has been chosen. The caption row and the
 * two calls to action are the only editable text.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_media = nextsaas_media(
	array(
		'mediaId'           => nextsaas_attr_int( $attributes, 'mediaId' ),
		'mediaUrl'          => nextsaas_attr( $attributes, 'mediaUrl' ),
		'mediaAlt'          => nextsaas_attr( $attributes, 'mediaAlt' ),
		'placeholderLabel'  => nextsaas_attr( $attributes, 'placeholderLabel' ),
		'placeholderTop'    => nextsaas_attr( $attributes, 'placeholderTop' ),
		'placeholderBadge'  => nextsaas_attr( $attributes, 'placeholderBadge' ),
		'placeholderTitle'  => nextsaas_attr( $attributes, 'placeholderTitle' ),
		'placeholderNote'   => nextsaas_attr( $attributes, 'placeholderNote' ),
		'placeholderFoot'   => nextsaas_attr( $attributes, 'placeholderFoot' ),
		'placeholderAspect' => nextsaas_attr( $attributes, 'placeholderAspect' ),
	),
	'content-img',
	'eager',
);

$nextsaas_primary = nextsaas_cta(
	array(
		'label'   => nextsaas_attr( $attributes, 'primaryLabel' ),
		'url'     => nextsaas_attr( $attributes, 'primaryUrl' ),
		'variant' => nextsaas_attr( $attributes, 'primaryVariant', 'primary' ),
	),
);

$nextsaas_secondary = nextsaas_attr( $attributes, 'secondaryLabel' );
$nextsaas_secondary_url = nextsaas_attr( $attributes, 'secondaryUrl' );
?>
<section <?php
	echo nextsaas_section_attrs( 'product-workspace-preview', $attributes, array( 'section' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="product-showcase">
			<?php echo $nextsaas_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().?>
		</div>
		<div class="platform-caption">
			<?php foreach ( nextsaas_items( $attributes, 'captions' ) as $nextsaas_caption ) : ?>
				<span><?php echo esc_html( is_scalar( $nextsaas_caption['text'] ?? '' ) ? (string) $nextsaas_caption['text'] : '' ); ?></span>
			<?php endforeach; ?>
		</div>
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
