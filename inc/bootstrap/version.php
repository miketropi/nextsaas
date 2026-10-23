<?php

/**
 * Single source of truth for the NextSaaS theme version.
 *
 * Read by phpstan-bootstrap.php, tests/bootstrap.php, scripts/build-package.mjs
 * and functions.php. Keep the style.css header in sync when releasing; the
 * `theme-version` test asserts the two agree.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'NEXTSAAS_VERSION' ) ) {
	define( 'NEXTSAAS_VERSION', '1.0.0' );
}
