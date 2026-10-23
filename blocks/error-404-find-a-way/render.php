<?php

/**
 * Server renderer for the "Find a way" recovery cards.
 *
 * Three cards, each a link with a heading and a supporting line. The card list is a
 * repeatable attribute rather than three fixed fields, so an editor can point a
 * visitor at whichever surfaces this site actually has.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section <?php
	echo nextsaas_section_attrs( 'error-404-find-a-way', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="grid-3">
			<?php foreach ( nextsaas_items( $attributes, 'items' ) as $nextsaas_item ) : ?>
				<?php
				$nextsaas_heading = is_scalar( $nextsaas_item['heading'] ?? '' ) ? (string) $nextsaas_item['heading'] : '';
				$nextsaas_body    = is_scalar( $nextsaas_item['body'] ?? '' ) ? (string) $nextsaas_item['body'] : '';
				$nextsaas_url     = is_scalar( $nextsaas_item['url'] ?? '' ) ? (string) $nextsaas_item['url'] : '';
				?>
				<article class="value-card">
					<h3>
						<a class="text-link" href="<?php echo esc_url( nextsaas_resolve_url( $nextsaas_url ) ); ?>"><?php echo esc_html( $nextsaas_heading ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
					</h3>
					<p><?php echo esc_html( $nextsaas_body ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
