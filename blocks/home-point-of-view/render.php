<?php
/** @var array<string, mixed> $attributes */
declare( strict_types=1 );
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section <?php echo nextsaas_section_attrs( 'home-point-of-view', $attributes, array( 'section', 'intro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
<div class="container grid-1-2">
<p class="side-note" data-nextsaas-rich="sideNote"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'sideNote' ) ); ?></p>
<h2 data-nextsaas-rich="heading" data-nextsaas-word-reveal><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
</div>
</section>
