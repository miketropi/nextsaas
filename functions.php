<?php

/**
 * NextSaaS child theme bootstrap.
 *
 * WordPress loads a child theme's functions.php before the parent's, so nothing at
 * file scope here may read a NEXTORA_* constant or call a parent function. Parent
 * assets are referenced by their registered handle names inside hooks, and the
 * parent's menu locations, blocks and contact form are all looked up lazily.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/inc/bootstrap/version.php';

if ( ! defined( 'NEXTSAAS_DIR' ) ) {
	define( 'NEXTSAAS_DIR', get_stylesheet_directory() );
}

if ( ! defined( 'NEXTSAAS_URI' ) ) {
	define( 'NEXTSAAS_URI', get_stylesheet_directory_uri() );
}

$nextsaas_autoloader = NEXTSAAS_DIR . '/vendor/autoload.php';

if ( is_readable( $nextsaas_autoloader ) ) {
	require_once $nextsaas_autoloader;
}

require_once NEXTSAAS_DIR . '/inc/assets/enqueue.php';
require_once NEXTSAAS_DIR . '/inc/blocks/helpers.php';
require_once NEXTSAAS_DIR . '/inc/blocks/query.php';
require_once NEXTSAAS_DIR . '/inc/navigation/setup.php';
require_once NEXTSAAS_DIR . '/inc/template/body-class.php';
require_once NEXTSAAS_DIR . '/inc/compat/beplus-visual-mega-nav.php';
require_once NEXTSAAS_DIR . '/blocks/blocks.php';
