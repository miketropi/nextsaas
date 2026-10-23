<?php

/**
 * Server renderer for the Site header block.
 *
 * The navigation is a classic WordPress menu: `nextsaas/site-header` renders the
 * theme's own `primary` location, and the block's `navItems` attribute is the
 * fallback used while no menu is assigned, so the header is complete — brand,
 * links, call to action, drawer toggle and skip link — on a brand-new site and with
 * JavaScript turned off.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-nextsaas-site-header-menu-walker.php';

$nextsaas_items      = nextsaas_items( $attributes, 'navItems' );
$nextsaas_sticky     = nextsaas_attr_bool( $attributes, 'sticky', true );
$nextsaas_nav_id     = 'main-nav';
$nextsaas_menu_class = 'nextsaas-nav';

$nextsaas_menu = wp_nav_menu(
	array(
		'theme_location'  => nextsaas_primary_menu_location(),
		'container'       => '',
		'items_wrap'      => '%3$s',
		'depth'           => 2,
		'walker'          => new Nextsaas_Site_Header_Menu_Walker(),
		'fallback_cb'     => array( Nextsaas_Site_Header_Menu_Walker::class, 'fallback' ),
		'nextsaas_items'  => $nextsaas_items,
		'echo'            => false,
		'menu_class'      => $nextsaas_menu_class,
	),
);

$nextsaas_brand_url = nextsaas_attr( $attributes, 'brandUrl', '@home' );
$nextsaas_brand     = nextsaas_attr( $attributes, 'brandLabel', 'NextSaas' );
$nextsaas_brand_img = '';

/*
 * The brand is a text wordmark until an editor picks a logo. It never falls back
 * to the labeled placeholder blocks the sections use: a header is chrome, so an
 * unset logo has to stay the designed wordmark rather than a reservation box.
 */
if ( 'image' === nextsaas_attr( $attributes, 'brandMode', 'text' ) ) {
	$nextsaas_brand_id  = nextsaas_attr_int( $attributes, 'brandMediaId' );
	$nextsaas_brand_src = nextsaas_attr( $attributes, 'brandMediaUrl' );

	if ( $nextsaas_brand_id > 0 || '' !== $nextsaas_brand_src ) {
		$nextsaas_brand_img = nextsaas_media(
			array(
				'mediaId'  => $nextsaas_brand_id,
				'mediaUrl' => $nextsaas_brand_src,
				'mediaAlt' => nextsaas_attr( $attributes, 'brandMediaAlt' ),
			),
			'brand-media',
			'eager',
		);
	}
}

$nextsaas_cta = nextsaas_cta(
	array(
		'label'   => nextsaas_attr( $attributes, 'ctaLabel' ),
		'url'     => nextsaas_attr( $attributes, 'ctaUrl' ),
		'variant' => nextsaas_attr( $attributes, 'ctaVariant', 'secondary' ),
		'arrow'   => nextsaas_attr_bool( $attributes, 'ctaArrow', true ),
	),
	'nav-cta',
);
?>
<header <?php
	echo nextsaas_section_attrs(
	'site-header',
	array(),
	array_filter(
		array(
				'topnav',
				$nextsaas_sticky ? 'topnav--sticky' : '',
			),
	),
); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<?php
	$nextsaas_skip_label = nextsaas_attr( $attributes, 'skipLinkLabel' );
	$nextsaas_skip_to    = nextsaas_attr( $attributes, 'skipLinkTarget', '#content' );

	if ( '' !== $nextsaas_skip_label ) {
		?>
		<a class="skip-link" href="<?php echo esc_url( $nextsaas_skip_to ); ?>"><?php echo esc_html( $nextsaas_skip_label ); ?></a>
		<?php
	}
	?>
	<div class="container topnav-inner">
		<a class="brand" href="<?php echo esc_url( nextsaas_resolve_url( $nextsaas_brand_url ) ); ?>" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'brandAriaLabel' ) ); ?>"<?php echo nextsaas_link_is_current( $nextsaas_brand_url ) ? ' aria-current="page"' : ''; ?>>
			<?php if ( '' !== $nextsaas_brand_img ) : ?>
				<?php echo $nextsaas_brand_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_media().?>
			<?php else : ?>
				<span class="brand-mark" aria-hidden="true"><?php echo nextsaas_icon( 'asterisk' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></span><?php echo esc_html( $nextsaas_brand ); ?>
			<?php endif; ?>
		</a>
		<nav id="<?php echo esc_attr( $nextsaas_nav_id ); ?>" class="<?php echo esc_attr( $nextsaas_menu_class ); ?>" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'navAriaLabel' ) ); ?>"<?php echo nextsaas_nav_compact_mode_attr(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by nextsaas_nav_compact_mode_attr().?>>
			<?php echo (string) $nextsaas_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the walker.?>
		</nav>
		<?php echo $nextsaas_cta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by nextsaas_cta().?>
		<button type="button" class="btn btn-secondary menu-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $nextsaas_nav_id ); ?>">
			<?php echo esc_html( nextsaas_attr( $attributes, 'menuToggleLabel' ) ); ?>
			<?php echo nextsaas_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?>
		</button>
	</div>
	<div class="nextsaas-nav-overlay" data-nextsaas-nav-overlay hidden></div>
</header>
