<?php

/**
 * Constants for PHPStan analysis.
 *
 * Mirrors the constants that functions.php defines at runtime so static analysis
 * can resolve NEXTSAAS_* references without booting WordPress.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/wordpress/' );
}

require_once __DIR__ . '/inc/bootstrap/version.php';

if ( ! defined( 'NEXTSAAS_DIR' ) ) {
	define( 'NEXTSAAS_DIR', __DIR__ );
}

if ( ! defined( 'NEXTSAAS_URI' ) ) {
	define( 'NEXTSAAS_URI', 'https://example.test/wp-content/themes/nextsaas' );
}
