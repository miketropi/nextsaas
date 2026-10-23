<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section <?php echo nextsaas_section_attrs( 'page-hero', $attributes, array( 'inner-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
	<div class="container">
		<div class="breadcrumbs"><a href="<?php echo esc_url( nextsaas_resolve_url( '@home' ) ); ?>" data-nextsaas-rich="breadcrumbHomeLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'breadcrumbHomeLabel' ) ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></a><span data-nextsaas-rich="breadcrumbCurrentLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'breadcrumbCurrentLabel' ) ); ?></span></div>
		<div class="hero-split"><div class="page-intro"><p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p><h1 class="inner-title" data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h1></div><p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p></div>
	</div>
</section>
