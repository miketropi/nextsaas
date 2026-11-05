<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
$nextsaas_use_cases = nextsaas_items( $attributes, 'useCases' );
$nextsaas_media = array_intersect_key( $attributes, array_flip( array( 'mediaId', 'mediaUrl', 'mediaAlt', 'placeholderLabel', 'placeholderTop', 'placeholderBadge', 'placeholderTitle', 'placeholderNote', 'placeholderFoot', 'placeholderAspect' ) ) );

/*
 * The star slot is replaceable: a selected decoration is rendered as an image, and
 * anything else keeps the approved SVG exactly as it was. Rotation is a stored
 * boolean whose default is `true`, so content saved before the control existed still
 * turns, and an author who chose a still decoration gets no animation at all.
 */
$nextsaas_decoration = array(
	'mediaId'  => nextsaas_attr_int( $attributes, 'decorationMediaId' ),
	'mediaUrl' => nextsaas_attr( $attributes, 'decorationMediaUrl' ),
	'mediaAlt' => nextsaas_attr( $attributes, 'decorationMediaAlt' ),
);

$nextsaas_has_decoration = $nextsaas_decoration['mediaId'] > 0 || '' !== trim( $nextsaas_decoration['mediaUrl'] );
$nextsaas_star_class     = nextsaas_attr_bool( $attributes, 'decorationRotate', true ) ? 'hero-star is-rotating' : 'hero-star';
?>
<section <?php echo nextsaas_section_attrs( 'home-hero', $attributes, array( 'section', 'hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
<div class="container">
<div class="hero-kicker"><span data-nextsaas-rich="kicker"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'kicker' ) ); ?></span><span data-nextsaas-rich="kickerSecondary"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'kickerSecondary' ) ); ?> <?php echo nextsaas_icon( 'arrow-down-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></span></div>
<div class="hero-main" data-nextsaas-gap="grid">
<h1 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h1>
<div class="hero-orbit"><div class="<?php echo esc_attr( $nextsaas_star_class ); ?>"><?php if ( $nextsaas_has_decoration ) : ?><?php echo nextsaas_media( $nextsaas_decoration, 'hero-decoration-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().?><?php else : ?><svg viewBox="0 0 200 200" fill="currentColor" aria-hidden="true"><path d="M87 0h26l-1 64 44-46 26 26-46 44 64-1v26l-64-1 46 44-26 26-44-46 1 64H87l1-64-44 46-26-26 46-44-64 1V87l64 1-46-44 26-26 44 46z"/></svg><?php endif; ?></div><p data-nextsaas-rich="orbitCopy"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'orbitCopy' ) ); ?></p></div>
</div>
<div class="hero-bottom" data-nextsaas-gap="grid"><div><p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p></div><div><div class="hero-actions"><button type="button" class="btn btn-primary btn-arrow" data-nextsaas-rich="primaryLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'primaryLabel' ) ); ?><?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></button><a class="text-link" href="<?php echo esc_url( nextsaas_attr( $attributes, 'workspaceUrl' ) ); ?>" data-nextsaas-rich="workspaceLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'workspaceLabel' ) ); ?> <span><?php echo nextsaas_icon( 'arrow-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></span></a></div></div></div>
<div class="hero-image" id="platform"><?php echo nextsaas_media( $nextsaas_media, 'nextsaas-content-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().?></div>
<div class="platform-caption"><span data-nextsaas-rich="caption"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'caption' ) ); ?></span><span data-nextsaas-rich="captionNote"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'captionNote' ) ); ?></span></div>
<div class="use-cases"><span data-nextsaas-rich="useCasesLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'useCasesLabel' ) ); ?></span><?php foreach ( $nextsaas_use_cases as $nextsaas_index => $nextsaas_item ) : ?><a href="<?php echo esc_url( (string) ( $nextsaas_item['url'] ?? '' ) ); ?>" data-pick="<?php echo esc_attr( (string) ( $nextsaas_item['pick'] ?? '' ) ); ?>" data-nextsaas-rich="useCases.<?php echo esc_attr( (string) $nextsaas_index ); ?>.label"><?php echo nextsaas_rich( (string) ( $nextsaas_item['label'] ?? '' ) ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></a><?php endforeach; ?></div>
</div>
</section>
