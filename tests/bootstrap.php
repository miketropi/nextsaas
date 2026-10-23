<?php

/**
 * PHPUnit bootstrap.
 *
 * NextSaaS's units are deliberately WordPress-light: the decisions the suite
 * exercises (which blocks are registered, how a link attribute resolves, what query
 * arguments are assembled, how the header walker decides `aria-current`, and
 * whether the design manifest still covers the block layer) read only a small,
 * explicit set of WordPress functions. Those are doubled in
 * tests/Support/wp-stubs.php instead of booting a WordPress test suite, so
 * `composer test` stays fast and needs no database.
 *
 * The real WordPress integration proof is `npm run test:wp`, which runs the same
 * theme inside Playground against a real install.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/wordpress/' );
}

require dirname( __DIR__ ) . '/inc/bootstrap/version.php';

if ( is_readable( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
	require dirname( __DIR__ ) . '/vendor/autoload.php';
}

require __DIR__ . '/Support/wp-stubs.php';

if ( ! defined( 'NEXTSAAS_DIR' ) ) {
	define( 'NEXTSAAS_DIR', dirname( __DIR__ ) );
}

if ( ! defined( 'NEXTSAAS_URI' ) ) {
	define( 'NEXTSAAS_URI', 'https://example.test/wp-content/themes/nextsaas' );
}

require dirname( __DIR__ ) . '/inc/blocks/helpers.php';
require dirname( __DIR__ ) . '/inc/blocks/query.php';
require dirname( __DIR__ ) . '/inc/navigation/setup.php';
require dirname( __DIR__ ) . '/inc/compat/beplus-visual-mega-nav.php';
require dirname( __DIR__ ) . '/blocks/blocks.php';

nextsaas_test_reset();
