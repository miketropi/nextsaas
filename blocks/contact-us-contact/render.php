<?php

/**
 * Server renderer for the Contact layout block.
 *
 * The form is NOT part of this block. `nextora/contact-form` owns submission — its
 * nonce, its REST route, its admin and visitor emails, its anti-spam — and the parent
 * theme already maintains all four. Reimplementing any of that here would mean two
 * submission paths, two places to fix a security bug and a second mail pipeline to
 * keep in sync with the first, so this block renders the section shell and its own
 * `.contact-aside` column and hands the form region to `$content`, which is the
 * rendered inner blocks.
 *
 * The `.contact-layout` grid is the reason the shell has to wrap the form rather than
 * sit beside it: the aside and the form are the two grid cells, and only a shared
 * parent can lay them out as the approved render does.
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Rendered inner blocks: the contact form.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section <?php
	echo nextsaas_section_attrs( 'contact-us-contact', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="contact-layout">
			<aside class="contact-aside">
				<p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p>
				<h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2>
				<?php foreach ( nextsaas_items( $attributes, 'routes' ) as $nextsaas_route ) : ?>
					<?php
					$nextsaas_title = is_scalar( $nextsaas_route['heading'] ?? '' ) ? (string) $nextsaas_route['heading'] : '';
					$nextsaas_body  = is_scalar( $nextsaas_route['body'] ?? '' ) ? (string) $nextsaas_route['body'] : '';
					$nextsaas_label = is_scalar( $nextsaas_route['linkLabel'] ?? '' ) ? (string) $nextsaas_route['linkLabel'] : '';
					$nextsaas_url   = is_scalar( $nextsaas_route['linkUrl'] ?? '' ) ? (string) $nextsaas_route['linkUrl'] : '';

					if ( '' === $nextsaas_title ) {
						continue;
					}
					?>
					<div class="contact-route">
						<h3><?php echo esc_html( $nextsaas_title ); ?></h3>
						<p><?php echo esc_html( $nextsaas_body ); ?></p>
						<?php if ( '' !== $nextsaas_label ) : ?>
							<a class="text-link" href="<?php echo esc_url( nextsaas_resolve_url( $nextsaas_url ) ); ?>"><?php echo esc_html( $nextsaas_label ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</aside>
			<?php
			/*
			 * The form cell. `$content` is already-escaped block output, printed the way
			 * WordPress prints block content.
			 */
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
			?>
		</div>
	</div>
</section>
