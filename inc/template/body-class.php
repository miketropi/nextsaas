<?php

/**
 * The body class the inner-page composition depends on.
 *
 * The approved render ships two compositions and tells them apart with
 * `<body class="inner-page">`: the landing page has no class, and the other nine
 * surfaces do. Twenty-six rules in the compiled stylesheet hang off that class —
 * `inner-page .closing h2` sets the closing heading's 112px size instead of the
 * landing's 170px, `inner-page h2` narrows the section heading measure, and
 * `inner-page .section` retunes the vertical rhythm.
 *
 * Porting the rules without the class would leave every inner surface styled with the
 * landing's measurements, so the class is reproduced here, from the one signal the
 * design actually used: whether the request is the front page.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the inner-page composition class.
 *
 * @param array<int, string> $classes Body classes.
 *
 * @return array<int, string> Filtered body classes.
 */
function nextsaas_body_classes( array $classes ): array {
	if ( is_front_page() ) {
		return $classes;
	}

	$classes[] = 'inner-page';

	return $classes;
}
add_filter( 'body_class', 'nextsaas_body_classes' );
