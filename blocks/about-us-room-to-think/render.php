<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 ); if ( ! defined( 'ABSPATH' ) ) { exit; }
// The default is theme-relative because nextsaas_media() resolves URLs, not filesystem paths.
$nextsaas_media = nextsaas_media( array( 'mediaId' => nextsaas_attr_int( $attributes, 'mediaId' ), 'mediaUrl' => nextsaas_attr( $attributes, 'mediaUrl', '/assets/images/nextsaas-workspace.jpg' ), 'mediaAlt' => nextsaas_attr( $attributes, 'mediaAlt' ) ), 'inner-photo' );
?>
<section <?php echo nextsaas_section_attrs( 'about-us-room-to-think', $attributes, array( 'section' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="container"><figure class="photo-strip"><?php echo $nextsaas_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?><figcaption><span data-nextsaas-rich="caption"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'caption' ) ); ?></span><span data-nextsaas-rich="credit"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'credit' ) ); ?></span></figcaption></figure></div></section>
