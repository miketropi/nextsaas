<?php

/**
 * Shared server-side rendering helpers for NextSaaS blocks.
 *
 * Every `nextsaas/*` renderer composes these instead of repeating escaping,
 * URL resolution, icon geometry and media fallbacks thirty-nine times.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline SVG bodies for every icon the approved render uses.
 *
 * Geometry is taken verbatim from the approved OpenDesign files, which inline
 * Lucide (ISC licence). Keeping the paths here means the theme ships no icon font,
 * no sprite request, no runtime icon script and no second copy of an icon set.
 *
 * @return array<string, string> Icon name => SVG inner markup.
 */
function nextsaas_icon_paths(): array {
	return array(
		'arrow-down'      => '<path d="M12 5v14"/><path d="m19 12-7 7-7-7"/>',
		'arrow-down-left' => '<path d="M17 7 7 17"/><path d="M17 17H7V7"/>',
		'arrow-left'      => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
		'arrow-right'     => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'arrow-up'        => '<path d="m5 12 7-7 7 7"/><path d="M12 19V5"/>',
		'arrow-up-right'  => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
		'asterisk'        => '<path d="M12 6v12"/><path d="M17.196 9 6.804 15"/><path d="m6.804 9 10.392 6"/>',
		'check'           => '<path d="M20 6 9 17l-5-5"/>',
		'chevron-down'    => '<path d="m6 9 6 6 6-6"/>',
		'circle-check'    => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
		'focus'           => '<circle cx="12" cy="12" r="3"/><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/>',
		'github'          => '<path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/>',
		'instagram'       => '<rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37Z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>',
		'linkedin'        => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6Z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>',
		'menu'            => '<line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>',
		'messages-square' => '<path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/>',
		'move-up-right'   => '<path d="M13 5H19V11"/><path d="M19 5 5 19"/>',
		'plus'            => '<path d="M5 12h14"/><path d="M12 5v14"/>',
		'quote'           => '<path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/><path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z"/>',
		'user-round'      => '<circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/>',
		'workflow'        => '<rect width="8" height="8" x="3" y="3" rx="2"/><path d="M7 11v4a2 2 0 0 0 2 2h4"/><rect width="8" height="8" x="13" y="13" rx="2"/>',
		'x'               => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
		'youtube'         => '<path d="M2.5 7.1a24.12 24.12 0 0 0 0 9.8 2 2 0 0 0 1.4 1.4 49.56 49.56 0 0 0 16.2 0 2 2 0 0 0 1.4-1.4 24.12 24.12 0 0 0 0-9.8 2 2 0 0 0-1.4-1.4 49.56 49.56 0 0 0-16.2 0 2 2 0 0 0-1.4 1.4"/><path d="m10 15 5-3-5-3z"/>',
	);
}

/**
 * Render one inline Lucide-style icon.
 *
 * Geometry, stroke width and accessibility attributes match the approved render:
 * the icon is decorative (`aria-hidden`), inherits `currentColor` and is never
 * focusable, so no icon can introduce a second tab stop.
 *
 * @param string                $name Icon name.
 * @param array<string, string> $args Optional `class` override.
 *
 * @return string Escaped SVG markup, or an empty string for an unknown icon.
 */
function nextsaas_icon( string $name, array $args = array() ): string {
	$paths = nextsaas_icon_paths();

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	$class = $args['class'] ?? 'lucide lucide-' . $name;

	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="%1$s" aria-hidden="true" focusable="false">%2$s</svg>',
		esc_attr( $class ),
		$paths[ $name ],
	);
}

/**
 * Read a string attribute.
 *
 * Saved content can hold a non-scalar for any key, and every renderer has to be
 * able to stringify it, so the cast lives here.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $key        Attribute name.
 * @param string               $default    Value used when the attribute is missing.
 *
 * @return string Attribute value.
 */
function nextsaas_attr( array $attributes, string $key, string $default = '' ): string {
	if ( ! array_key_exists( $key, $attributes ) || ! is_scalar( $attributes[ $key ] ) ) {
		return $default;
	}

	return (string) $attributes[ $key ];
}

/**
 * Read an integer attribute.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $key        Attribute name.
 * @param int                  $default    Value used when the attribute is missing.
 *
 * @return int Attribute value.
 */
function nextsaas_attr_int( array $attributes, string $key, int $default = 0 ): int {
	if ( ! array_key_exists( $key, $attributes ) || ! is_scalar( $attributes[ $key ] ) ) {
		return $default;
	}

	return (int) $attributes[ $key ];
}

/**
 * Read a boolean attribute.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $key        Attribute name.
 * @param bool                 $default    Value used when the attribute is missing.
 *
 * @return bool Attribute value.
 */
function nextsaas_attr_bool( array $attributes, string $key, bool $default = false ): bool {
	if ( ! array_key_exists( $key, $attributes ) ) {
		return $default;
	}

	return (bool) $attributes[ $key ];
}

/**
 * Normalise a repeatable attribute into a list of associative rows.
 *
 * Saved content can hold a scalar, a list of scalars or a list with holes; only
 * array rows survive, which keeps every `foreach` in a renderer type-safe.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $key        Attribute name.
 *
 * @return array<int, array<string, mixed>> Repeatable rows.
 */
function nextsaas_items( array $attributes, string $key ): array {
	$value = $attributes[ $key ] ?? array();

	if ( ! is_array( $value ) ) {
		return array();
	}

	$rows = array();

	foreach ( $value as $row ) {
		if ( is_array( $row ) ) {
			$rows[] = $row;
		}
	}

	return $rows;
}

/**
 * Build the shared `nextsaas-scope` class list for a block wrapper.
 *
 * Every NextSaaS stylesheet selector is scoped under `.nextsaas-scope`, so each
 * block root carries it in addition to the section's own class name.
 *
 * @param string   $class_name Block-specific root class.
 * @param string[] $extra      Additional class names.
 *
 * @return string Space-separated class attribute value.
 */
function nextsaas_scope_class( string $class_name, array $extra = array() ): string {
	return trim(
		implode(
			' ',
			array_filter( array_merge( array( 'nextsaas-scope', $class_name ), $extra ) ),
		),
	);
}

/**
 * Active palette buckets flattened into `slug => colour`.
 *
 * `wp_get_global_settings()` returns one bucket per origin (default, theme,
 * custom) and the buckets are walked in that order, so a slug a site redefines in
 * the Customizer wins exactly as the generated preset variables do.
 *
 * @return array<string, string> Palette slug => stored colour value.
 */
function nextsaas_palette_colors(): array {
	static $palette = null;

	if ( is_array( $palette ) ) {
		return $palette;
	}

	$palette  = array();
	$settings = wp_get_global_settings( array( 'color', 'palette' ) );

	if ( ! is_array( $settings ) ) {
		return $palette;
	}

	foreach ( $settings as $bucket ) {
		if ( ! is_array( $bucket ) ) {
			continue;
		}

		foreach ( $bucket as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['slug'], $entry['color'] ) ) {
				continue;
			}

			if ( ! is_scalar( $entry['slug'] ) || ! is_scalar( $entry['color'] ) ) {
				continue;
			}

			$slug = strtolower( trim( (string) $entry['slug'] ) );

			if ( '' !== $slug ) {
				$palette[ $slug ] = trim( (string) $entry['color'] );
			}
		}
	}

	return $palette;
}

/**
 * Active spacing presets flattened into `slug => size`.
 *
 * @return array<string, string> Spacing slug => stored size value.
 */
function nextsaas_spacing_presets(): array {
	static $presets = null;

	if ( is_array( $presets ) ) {
		return $presets;
	}

	$presets  = array();
	$settings = wp_get_global_settings( array( 'spacing', 'spacingSizes' ) );

	if ( ! is_array( $settings ) ) {
		return $presets;
	}

	foreach ( $settings as $bucket ) {
		if ( ! is_array( $bucket ) ) {
			continue;
		}

		foreach ( $bucket as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['slug'], $entry['size'] ) ) {
				continue;
			}

			if ( ! is_scalar( $entry['slug'] ) || ! is_scalar( $entry['size'] ) ) {
				continue;
			}

			$slug = strtolower( trim( (string) $entry['slug'] ) );

			if ( '' !== $slug ) {
				$presets[ $slug ] = trim( (string) $entry['size'] );
			}
		}
	}

	return $presets;
}

/**
 * Palette slug referenced by a stored preset reference.
 *
 * The colour control stores a picked preset as its bare slug, while content saved
 * by an earlier version of the control holds one of the two reference spellings
 * WordPress itself uses. All three mean the same colour, so all three are read
 * here and resolved to a single variable form.
 *
 * @param string $value Stored attribute value.
 *
 * @return string Palette slug, or `''` when the value is not a preset reference.
 */
function nextsaas_color_preset_slug( string $value ): string {
	if ( preg_match( '/^var:preset\|color\|([a-z0-9][a-z0-9-]*)$/i', $value, $matches ) ) {
		return strtolower( $matches[1] );
	}

	if ( preg_match( '/^var\(\s*--wp--preset--color--([a-z0-9][a-z0-9-]*)\s*\)$/i', $value, $matches ) ) {
		return strtolower( $matches[1] );
	}

	return '';
}

/**
 * Parse an sRGB channel from a hex, `rgb()`, `hsl()` or `oklch()` argument.
 *
 * @param string $value Raw argument, e.g. `12.5%`, `0.4`, `200deg`.
 * @param float  $scale Value that `100%` represents.
 *
 * @return float|null Parsed number, or null when the argument is not a number.
 */
function nextsaas_parse_color_number( string $value, float $scale ): ?float {
	$value = trim( $value );

	if ( ! preg_match( '/^([-+]?(?:\d+\.?\d*|\.\d+))(deg|%|)$/i', $value, $matches ) ) {
		return null;
	}

	$number = (float) $matches[1];
	$unit   = strtolower( $matches[2] );

	if ( '%' === $unit ) {
		return $number / 100 * $scale;
	}

	return $number;
}

/**
 * Convert an OKLCh triplet to sRGB channels in the 0–255 range.
 *
 * @param float $lightness Lightness, 0–1.
 * @param float $chroma    Chroma.
 * @param float $hue       Hue in degrees.
 *
 * @return array{r: float, g: float, b: float} sRGB channels.
 */
function nextsaas_oklch_to_rgb( float $lightness, float $chroma, float $hue ): array {
	$radians = deg2rad( $hue );
	$a       = $chroma * cos( $radians );
	$b       = $chroma * sin( $radians );

	$long  = $lightness + 0.3963377774 * $a + 0.2158037573 * $b;
	$medium = $lightness - 0.1055613458 * $a - 0.0638541728 * $b;
	$short = $lightness - 0.0894841775 * $a - 1.2914855480 * $b;

	$long   = $long ** 3;
	$medium = $medium ** 3;
	$short  = $short ** 3;

	$channels = array(
		4.0767416621 * $long - 3.3077115913 * $medium + 0.2309699292 * $short,
		-1.2684380046 * $long + 2.6097574011 * $medium - 0.3413193965 * $short,
		-0.0041960863 * $long - 0.7034186147 * $medium + 1.7076147010 * $short,
	);

	$encode = static function ( float $channel ): float {
		$channel = max( 0.0, min( 1.0, $channel ) );

		return ( $channel <= 0.0031308 ? 12.92 * $channel : 1.055 * ( $channel ** ( 1 / 2.4 ) ) - 0.055 ) * 255;
	};

	return array(
		'r' => $encode( $channels[0] ),
		'g' => $encode( $channels[1] ),
		'b' => $encode( $channels[2] ),
	);
}

/**
 * Convert an HSL triplet to sRGB channels in the 0–255 range.
 *
 * @param float $hue        Hue in degrees.
 * @param float $saturation Saturation, 0–1.
 * @param float $lightness  Lightness, 0–1.
 *
 * @return array{r: float, g: float, b: float} sRGB channels.
 */
function nextsaas_hsl_to_rgb( float $hue, float $saturation, float $lightness ): array {
	$hue        = fmod( $hue, 360.0 );
	$hue        = $hue < 0 ? $hue + 360 : $hue;
	$saturation = max( 0.0, min( 1.0, $saturation ) );
	$lightness  = max( 0.0, min( 1.0, $lightness ) );

	$chroma = ( 1 - abs( 2 * $lightness - 1 ) ) * $saturation;
	$second = $chroma * ( 1 - abs( fmod( $hue / 60, 2.0 ) - 1 ) );
	$offset = $lightness - $chroma / 2;

	$sector = (int) floor( $hue / 60 ) % 6;

	$sets = array(
		array( $chroma, $second, 0.0 ),
		array( $second, $chroma, 0.0 ),
		array( 0.0, $chroma, $second ),
		array( 0.0, $second, $chroma ),
		array( $second, 0.0, $chroma ),
		array( $chroma, 0.0, $second ),
	);

	$rgb = $sets[ $sector ];

	return array(
		'r' => ( $rgb[0] + $offset ) * 255,
		'g' => ( $rgb[1] + $offset ) * 255,
		'b' => ( $rgb[2] + $offset ) * 255,
	);
}

/**
 * Parse a bounded CSS colour into sRGB channels plus alpha.
 *
 * Only the forms this theme can store or an author can type are understood: hex,
 * `rgb()`, `rgba()`, `hsl()`, `hsla()`, `oklch()` and the `transparent` keyword.
 * Anything else — a declaration, a URL, a `var()` reference, an unknown function —
 * returns null, which is what makes an invalid stored value render as no override
 * instead of as an injected declaration.
 *
 * @param string $value Raw CSS colour value.
 *
 * @return array{r: float, g: float, b: float, a: float}|null Channels in 0–255 plus alpha in 0–1.
 */
function nextsaas_parse_color( string $value ): ?array {
	$value = strtolower( trim( $value ) );

	if ( '' === $value ) {
		return null;
	}

	if ( 'transparent' === $value ) {
		return array(
			'r' => 0.0,
			'g' => 0.0,
			'b' => 0.0,
			'a' => 0.0,
		);
	}

	if ( str_starts_with( $value, '#' ) ) {
		$hex = substr( $value, 1 );

		if ( ! preg_match( '/^(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/', $hex ) ) {
			return null;
		}

		if ( strlen( $hex ) < 6 ) {
			$doubled = '';

			foreach ( str_split( $hex ) as $character ) {
				$doubled .= $character . $character;
			}

			$hex = $doubled;
		}

		return array(
			'r' => (float) hexdec( substr( $hex, 0, 2 ) ),
			'g' => (float) hexdec( substr( $hex, 2, 2 ) ),
			'b' => (float) hexdec( substr( $hex, 4, 2 ) ),
			'a' => 8 === strlen( $hex ) ? hexdec( substr( $hex, 6, 2 ) ) / 255 : 1.0,
		);
	}

	// Functional notation: name, a bounded argument list, no nested parentheses.
	if ( ! preg_match( '/^([a-z]+)\(([^()]*)\)$/', $value, $matches ) ) {
		return null;
	}

	$name = $matches[1];
	$list = preg_split( '/[\s,\/]+/', trim( $matches[2] ), -1, PREG_SPLIT_NO_EMPTY );

	if ( ! is_array( $list ) || array() === $list ) {
		return null;
	}

	$alpha = static function ( ?string $raw ) : ?float {
		if ( null === $raw ) {
			return 1.0;
		}

		return nextsaas_parse_color_number( $raw, 1.0 );
	};

	if ( 'rgb' === $name || 'rgba' === $name ) {
		if ( count( $list ) < 3 || count( $list ) > 4 ) {
			return null;
		}

		$channels = array();

		for ( $index = 0; $index < 3; $index++ ) {
			$channel = nextsaas_parse_color_number( $list[ $index ], 255.0 );

			if ( null === $channel ) {
				return null;
			}

			$channels[] = max( 0.0, min( 255.0, $channel ) );
		}

		$a = $alpha( $list[3] ?? null );

		if ( null === $a ) {
			return null;
		}

		return array(
			'r' => $channels[0],
			'g' => $channels[1],
			'b' => $channels[2],
			'a' => max( 0.0, min( 1.0, $a ) ),
		);
	}

	if ( 'hsl' === $name || 'hsla' === $name ) {
		if ( count( $list ) < 3 || count( $list ) > 4 ) {
			return null;
		}

		$hue        = nextsaas_parse_color_number( $list[0], 360.0 );
		$saturation = nextsaas_parse_color_number( $list[1], 1.0 );
		$lightness  = nextsaas_parse_color_number( $list[2], 1.0 );
		$a          = $alpha( $list[3] ?? null );

		if ( null === $hue || null === $saturation || null === $lightness || null === $a ) {
			return null;
		}

		return nextsaas_hsl_to_rgb( $hue, $saturation, $lightness ) + array(
			'a' => max( 0.0, min( 1.0, $a ) ),
		);
	}

	if ( 'oklch' === $name ) {
		if ( count( $list ) < 3 || count( $list ) > 4 ) {
			return null;
		}

		$lightness = nextsaas_parse_color_number( $list[0], 1.0 );
		$chroma    = nextsaas_parse_color_number( $list[1], 1.0 );
		$hue       = nextsaas_parse_color_number( $list[2], 360.0 );
		$a         = $alpha( $list[3] ?? null );

		if ( null === $lightness || null === $chroma || null === $hue || null === $a ) {
			return null;
		}

		// `oklch(98.5% …)` and `oklch(0.985 …)` are the same lightness; a stored
		// percentage above 1 is always a percentage, so both are scaled here.
		if ( $lightness > 1.0 ) {
			$lightness /= 100;
		}

		return nextsaas_oklch_to_rgb( max( 0.0, min( 1.0, $lightness ) ), max( 0.0, $chroma ), $hue ) + array(
			'a' => max( 0.0, min( 1.0, $a ) ),
		);
	}

	return null;
}

/**
 * Resolve a stored colour reference to opaque sRGB channels.
 *
 * Preset references are followed into the active palette; a colour with an alpha
 * channel is composited over `$under`, which is the surface it is really painted
 * on, so a translucent author colour is measured against the page it lands on
 * rather than against a fabricated grey.
 *
 * @param string                                             $value Raw CSS colour or preset reference.
 * @param array{r: float, g: float, b: float, a: float}|null $under Surface below the colour.
 * @param int                                                $depth Recursion guard for palette references.
 *
 * @return array{r: float, g: float, b: float, a: float}|null Opaque channels, or null when unresolvable.
 */
function nextsaas_resolve_css_color( string $value, ?array $under = null, int $depth = 0 ): ?array {
	if ( $depth > 4 ) {
		return null;
	}

	$value = trim( $value );

	if ( '' === $value ) {
		return null;
	}

	$slug = nextsaas_color_preset_slug( $value );

	if ( '' !== $slug ) {
		$palette = nextsaas_palette_colors();

		if ( ! isset( $palette[ $slug ] ) ) {
			return null;
		}

		return nextsaas_resolve_css_color( $palette[ $slug ], $under, $depth + 1 );
	}

	$color = nextsaas_parse_color( $value );

	if ( null === $color ) {
		return null;
	}

	if ( $color['a'] >= 1.0 ) {
		return $color;
	}

	if ( null === $under ) {
		return null;
	}

	return array(
		'r' => $color['r'] * $color['a'] + $under['r'] * ( 1 - $color['a'] ),
		'g' => $color['g'] * $color['a'] + $under['g'] * ( 1 - $color['a'] ),
		'b' => $color['b'] * $color['a'] + $under['b'] * ( 1 - $color['a'] ),
		'a' => 1.0,
	);
}

/**
 * The page canvas a section is painted on.
 *
 * Read from the global styles the site actually renders with, so a section
 * background declared `transparent` is measured against the real page colour
 * rather than an assumed one. An empty string means the canvas could not be
 * resolved, and every measurement that needs it reports `unknown`.
 *
 * @return string CSS colour for the canvas, or `''`.
 */
function nextsaas_page_canvas(): string {
	static $canvas = null;

	if ( is_string( $canvas ) ) {
		return $canvas;
	}

	$canvas = '';
	$stored = wp_get_global_styles( array( 'color', 'background' ) );

	if ( is_scalar( $stored ) && '' !== trim( (string) $stored ) ) {
		$canvas = nextsaas_resolve_section_color( (string) $stored );
	}

	return $canvas;
}

/**
 * The resolved page canvas as opaque channels.
 *
 * @return array{r: float, g: float, b: float, a: float}|null Canvas channels, or null when unresolvable.
 */
function nextsaas_page_canvas_color(): ?array {
	$canvas = nextsaas_page_canvas();

	return '' === $canvas ? null : nextsaas_resolve_css_color( $canvas );
}

/**
 * WCAG relative luminance of sRGB channels.
 *
 * @param array{r: float, g: float, b: float, a: float} $color Opaque channels in 0–255.
 *
 * @return float Relative luminance.
 */
function nextsaas_color_luminance( array $color ): float {
	$linear = static function ( float $channel ): float {
		$channel /= 255;

		return $channel <= 0.03928 ? $channel / 12.92 : ( ( $channel + 0.055 ) / 1.055 ) ** 2.4;
	};

	return 0.2126 * $linear( $color['r'] ) + 0.7152 * $linear( $color['g'] ) + 0.0722 * $linear( $color['b'] );
}

/**
 * WCAG contrast ratio between a stored foreground and a stored background.
 *
 * The single measurement used by both the real renderer and the editor preview,
 * so an inspector notice can never disagree with the page. A colour the theme
 * cannot resolve — an unknown preset, a `color-mix()` value, a stylesheet rule
 * this helper does not model — is reported as `unknown` instead of guessed at.
 *
 * @param string $foreground Stored foreground colour or preset reference.
 * @param string $background Stored background colour or preset reference.
 *
 * @return float|string Ratio rounded to two decimals, or `'unknown'`.
 */
function nextsaas_contrast_ratio( string $foreground, string $background ): float|string {
	$canvas = nextsaas_page_canvas_color();
	$base   = nextsaas_resolve_css_color( $background, $canvas );

	if ( null === $base ) {
		return 'unknown';
	}

	$front = nextsaas_resolve_css_color( $foreground, $base );

	if ( null === $front ) {
		return 'unknown';
	}

	$first  = nextsaas_color_luminance( $front );
	$second = nextsaas_color_luminance( $base );

	return round( ( max( $first, $second ) + 0.05 ) / ( min( $first, $second ) + 0.05 ), 2 );
}

/**
 * The highest-contrast active theme foreground for a background.
 *
 * The four palette roles are the theme's own text roles, so a derived foreground
 * always stays inside the design's vocabulary. Candidates are compared by WCAG
 * ratio against the resolved background and ties keep the declared order, which
 * puts `contrast` first.
 *
 * @param string $background Stored background colour or preset reference.
 *
 * @return array{value: string, ratio: float|string} Best candidate, empty and `unknown` when none resolves.
 */
function nextsaas_section_best_foreground( string $background ): array {
	$best = array(
		'value' => '',
		'ratio' => 'unknown',
	);

	foreach ( array( 'contrast', 'base', 'paragraph', 'surface' ) as $slug ) {
		$value = 'var(--wp--preset--color--' . $slug . ')';
		$ratio = nextsaas_contrast_ratio( $value, $background );

		if ( ! is_float( $ratio ) ) {
			continue;
		}

		if ( 'unknown' === $best['ratio'] || $ratio > $best['ratio'] ) {
			$best = array(
				'value' => $value,
				'ratio' => $ratio,
			);
		}
	}

	return $best;
}

/**
 * Derived foreground roles for a changed section background.
 *
 * Only called when the author changed the background and left a foreground role
 * empty; the result is emitted under the `--nextsaas-derived-*` properties so an
 * explicit author colour always keeps its own property and precedence. Heading,
 * content and focus are computed separately because they have different minimum
 * ratios: 3:1 is enough for display type and for a focus boundary, while body copy
 * needs 4.5:1.
 *
 * @param string $background Stored background colour or preset reference.
 *
 * @return array{heading: array{value: string, ratio: float|string}, content: array{value: string, ratio: float|string}, focus: array{value: string, ratio: float|string}}
 */
function nextsaas_section_foregrounds( string $background ): array {
	$text = nextsaas_section_best_foreground( $background );

	// Focus boundaries only need 3:1, so the first candidate that clears it wins;
	// when none does, the best available candidate is still reported with its own
	// measured ratio rather than silently dropped.
	$focus = $text;

	foreach ( array( 'contrast', 'base', 'paragraph', 'surface' ) as $slug ) {
		$value = 'var(--wp--preset--color--' . $slug . ')';
		$ratio = nextsaas_contrast_ratio( $value, $background );

		if ( is_float( $ratio ) && $ratio >= 3.0 ) {
			$focus = array(
				'value' => $value,
				'ratio' => $ratio,
			);
			break;
		}
	}

	return array(
		'heading' => $text,
		'content' => $text,
		'focus'   => $focus,
	);
}

/**
 * Resolve a stored section colour into the value a custom property carries.
 *
 * Accepts a bare palette slug (what the colour control stores for a picked
 * preset), either preset reference spelling, and a bounded colour literal.
 * Anything else — a declaration, a URL, an arbitrary `var()`, an unknown slug —
 * emits nothing, so a hostile attribute value can never inject CSS.
 *
 * @param string $value Stored attribute value.
 *
 * @return string CSS value, or `''` for "theme default".
 */
function nextsaas_resolve_section_color( string $value ): string {
	$value = trim( $value );

	if ( '' === $value ) {
		return '';
	}

	$palette = nextsaas_palette_colors();
	$slug    = nextsaas_color_preset_slug( $value );

	if ( '' === $slug && preg_match( '/^[a-z][a-z0-9-]*$/', $value ) ) {
		$slug = strtolower( $value );
	}

	if ( '' !== $slug ) {
		return isset( $palette[ $slug ] ) ? 'var(--wp--preset--color--' . $slug . ')' : '';
	}

	return null === nextsaas_parse_color( $value ) ? '' : $value;
}

/**
 * Resolve a stored section gap into the value a custom property carries.
 *
 * Only `0` or a spacing preset that the active theme really declares is accepted;
 * the preset is always emitted as the variable reference, so changing the size
 * behind the preset changes every section that selected it.
 *
 * @param string $value Stored attribute value.
 *
 * @return string CSS value, or `''` for "theme default".
 */
function nextsaas_resolve_section_gap( string $value ): string {
	$value = trim( $value );

	if ( '' === $value ) {
		return '';
	}

	if ( '0' === $value ) {
		return '0';
	}

	$slug = '';

	if ( preg_match( '/^var:preset\|spacing\|([a-z0-9][a-z0-9-]*)$/i', $value, $matches ) ) {
		$slug = strtolower( $matches[1] );
	} elseif ( preg_match( '/^var\(\s*--wp--preset--spacing--([a-z0-9][a-z0-9-]*)\s*\)$/i', $value, $matches ) ) {
		$slug = strtolower( $matches[1] );
	} elseif ( preg_match( '/^[a-z][a-z0-9-]*$/i', $value ) ) {
		$slug = strtolower( $value );
	}

	if ( '' === $slug || ! isset( nextsaas_spacing_presets()[ $slug ] ) ) {
		return '';
	}

	return 'var(--wp--preset--spacing--' . $slug . ')';
}

/**
 * Appearance declarations for one section block.
 *
 * Every custom block declares the same four optional attributes — `sectionBg`,
 * `sectionHeading`, `sectionText` and `sectionGap` — and this is the single place
 * they turn into markup: presence classes for the stylesheet, custom properties on
 * the section root, derived role properties for a section whose background changed
 * while a foreground was left empty, and the measured ratios the editor notices
 * read. Empty or invalid values emit nothing at all, so an untouched block renders
 * exactly the markup it rendered before the controls existed.
 *
 * @param array<string, mixed> $attributes Block attributes.
 *
 * @return array{class: string[], style: string, data: array<string, string>} Additional wrapper values.
 */
function nextsaas_section_appearance( array $attributes ): array {
	$raw = array(
		'background' => nextsaas_attr( $attributes, 'sectionBg' ),
		'heading'    => nextsaas_attr( $attributes, 'sectionHeading' ),
		'text'       => nextsaas_attr( $attributes, 'sectionText' ),
		'gap'        => nextsaas_attr( $attributes, 'sectionGap' ),
	);

	$background = nextsaas_resolve_section_color( $raw['background'] );
	$heading    = nextsaas_resolve_section_color( $raw['heading'] );
	$text       = nextsaas_resolve_section_color( $raw['text'] );
	$gap        = nextsaas_resolve_section_gap( $raw['gap'] );

	$classes      = array();
	$declarations = array();

	if ( '' !== $background ) {
		$classes[]      = 'has-nextsaas-background';
		$declarations[] = '--nextsaas-block-bg:' . $background;
	}

	if ( '' !== $heading ) {
		$classes[]      = 'has-nextsaas-heading';
		$declarations[] = '--nextsaas-block-heading:' . $heading;
	}

	if ( '' !== $text ) {
		$classes[]      = 'has-nextsaas-content';
		$declarations[] = '--nextsaas-block-text:' . $text;
	}

	if ( '' !== $gap ) {
		$classes[]      = 'has-nextsaas-gap';
		$declarations[] = '--nextsaas-block-gap:' . $gap;
	}

	$heading_ratio = 'unknown';
	$content_ratio = 'unknown';
	$derived       = array();

	if ( '' !== $background ) {
		$derived = nextsaas_section_foregrounds( $background );

		if ( '' === $heading && '' !== $derived['heading']['value'] ) {
			$classes[]      = 'has-nextsaas-derived-heading';
			$declarations[] = '--nextsaas-derived-heading:' . $derived['heading']['value'];
			$heading_ratio  = $derived['heading']['ratio'];
		}

		if ( '' === $text && '' !== $derived['content']['value'] ) {
			$classes[]      = 'has-nextsaas-derived-content';
			$declarations[] = '--nextsaas-derived-text:' . $derived['content']['value'];
			$content_ratio  = $derived['content']['ratio'];
		}

		if ( '' !== $derived['focus']['value'] ) {
			$declarations[] = '--nextsaas-derived-focus:' . $derived['focus']['value'];
		}

		if ( '' !== $heading ) {
			$heading_ratio = nextsaas_contrast_ratio( $heading, $background );
		}

		if ( '' !== $text ) {
			$content_ratio = nextsaas_contrast_ratio( $text, $background );
		}
	} elseif ( '' !== $heading || '' !== $text ) {
		// No changed background: an explicit foreground sits on the page canvas.
		$canvas = nextsaas_page_canvas();

		if ( '' !== $heading ) {
			$heading_ratio = nextsaas_contrast_ratio( $heading, $canvas );
		}

		if ( '' !== $text ) {
			$content_ratio = nextsaas_contrast_ratio( $text, $canvas );
		}
	}

	/*
	 * Cards, panels and buttons paint a surface of their own, so a content role needs
	 * one it can be read on. The surface is derived against the foreground those
	 * elements will really carry — the author's colour when there is one, the derived
	 * colour otherwise — and it is emitted whether or not the background changed: a
	 * light content colour on a light section needs exactly the same pairing as a
	 * light one on a dark section.
	 */
	$content_role = '' !== $text ? $text : (string) ( $derived['content']['value'] ?? '' );

	if ( '' !== $content_role ) {
		$surface = nextsaas_section_best_foreground( $content_role );

		if ( '' !== $surface['value'] ) {
			$declarations[] = '--nextsaas-derived-content-surface:' . $surface['value'];
		}
	}

	$data = array();

	if ( array() !== $classes || '' !== $gap || array() !== array_filter( $raw ) ) {
		$data['data-nextsaas-heading-ratio'] = is_float( $heading_ratio ) ? number_format( $heading_ratio, 2, '.', '' ) : 'unknown';
		$data['data-nextsaas-content-ratio'] = is_float( $content_ratio ) ? number_format( $content_ratio, 2, '.', '' ) : 'unknown';
		/*
		 * A stable key for the exact four attribute values this response was
		 * rendered from, so a preview reader can discard a response that arrives
		 * after the author already changed a control. It is the canonical spelling
		 * of the values rather than a digest: the editor has to compare it, and the
		 * only way to compare a digest is to reimplement the same hash in
		 * JavaScript, which is exactly the duplicated algorithm this contract avoids.
		 */
		$data['data-nextsaas-appearance-key'] = implode( '|', array( $raw['background'], $raw['heading'], $raw['text'], $raw['gap'] ) );
	}

	return array(
		'class' => $classes,
		'style' => implode( ';', $declarations ),
		'data'  => $data,
	);
}

/**
 * Wrapper attributes for a section block.
 *
 * Adds the scope class, the section class, the approved design anchor and the
 * `data-nextsaas-section` marker the manifest smoke assertions read.
 *
 * @param string               $slug          Section slug, e.g. `home-hero`.
 * @param array<string, mixed> $attributes    Block attributes.
 * @param string[]             $extra_classes Additional class names.
 * @param string               $style         Optional inline style declaration list.
 *
 * @return string Attribute string, already escaped, for direct output.
 */
function nextsaas_section_attrs( string $slug, array $attributes, array $extra_classes = array(), string $style = '' ): string {
	/*
	 * The design's own section classes are passed in by the caller rather than added
	 * here: the render's layouts are not uniform — the landing and inner pages all
	 * carry `section`, while the inner heroes carry only `inner-hero` and the error
	 * hero only `error-hero` — and `section` sets padding, so adding it where the
	 * design does not would double the section's inset.
	 */
	/*
	 * The four appearance attributes are read here rather than by each renderer, so
	 * every custom block gets the same presence classes, custom properties and
	 * measured ratios, and one block cannot drift from the rest. The declarations
	 * are merged with the caller's own `$style` and handed to core once: core
	 * concatenates its generated support styles (the native Dimensions padding and
	 * margin) with ours and sanitises the result, so a section carries a single
	 * style attribute with a single owner per declaration.
	 */
	$appearance = nextsaas_section_appearance( $attributes );
	$classes    = nextsaas_scope_class( 'nextsaas-' . $slug, array_merge( $extra_classes, $appearance['class'] ) );

	$attrs = array(
		'class'                 => $classes,
		'data-nextsaas-section' => $slug,
	);

	$section_id = nextsaas_attr( $attributes, 'sectionId' );

	if ( '' !== $section_id ) {
		$attrs['id'] = $section_id;
	}

	$declarations = trim( $style, "; \t\n\r\0\x0B" );
	$appearance_style = trim( $appearance['style'], "; \t\n\r\0\x0B" );

	if ( '' !== $appearance_style ) {
		$declarations = '' === $declarations ? $appearance_style : $declarations . ';' . $appearance_style;
	}

	if ( '' !== $declarations ) {
		$attrs['style'] = $declarations;
	}

	foreach ( $appearance['data'] as $name => $value ) {
		$attrs[ $name ] = $value;
	}

	return get_block_wrapper_attributes( $attrs );
}

/**
 * Escape a rich-text attribute for output.
 *
 * Rich attributes are stored as sanitized HTML fragments — `<br>`, `<em>` and
 * links — instead of plain text. `wp_kses_post()` is the single output gate for
 * every one of them, and text runs are escaped *before* `wp_kses_post()` re-allows
 * the inline markup, so a rich value and a plain value with the same copy render
 * the same characters (core runs `wptexturize()` over block-template output, and it
 * deliberately skips existing entities).
 *
 * @param mixed $value Stored attribute value.
 *
 * @return string Escaped HTML fragment.
 */
function nextsaas_rich( $value ): string {
	$value = (string) $value;

	$escaped = preg_replace_callback(
		'#(?:<[^>]*>)|([^<]+)#',
		static function ( array $matches ): string {
			return isset( $matches[1] ) ? esc_html( $matches[1] ) : $matches[0];
		},
		$value,
	);

	return wp_kses_post( is_string( $escaped ) ? $escaped : $value );
}

/**
 * Plain-text label derived from a rich value.
 *
 * Used for `aria-label`s and editor row labels, where markup is never allowed.
 *
 * @param string $value Rich value.
 *
 * @return string Single-line, tag-free label.
 */
function nextsaas_rich_label( string $value ): string {
	// Tags become a space: `strip_tags()` alone would join the words either side of a
	// `<br>`, so `Make space<br>for the…` would read as one word in a label.
	$spaced = (string) preg_replace( '#<[^>]*>#', ' ', $value );

	return trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( $spaced ) ) );
}

/**
 * Two-digit ordinal used by the editorial numbering in the design.
 *
 * @param int $index Zero-based item index.
 *
 * @return string Zero-padded number, e.g. `03`.
 */
function nextsaas_ordinal( int $index ): string {
	return str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
}

/**
 * Resolve a stored link attribute into a real URL.
 *
 * The approved render links page to page by file name (`product.html`), which is
 * meaningless once the surfaces are WordPress templates. This maps those values
 * onto the live site, and it is the single place every NextSaaS renderer and the
 * header/footer fallback agree on the mapping:
 *
 * - `@home`         — the site front page
 * - `@posts`        — the "Posts page" permalink, falling back to the front page
 * - `@latest-post`  — the newest published post, falling back to `@posts`
 * - `@search`       — the WordPress search endpoint
 * - `#anchor`       — passed through untouched (in-page target)
 * - `//host/…` and any `scheme:` URL — passed through untouched
 * - `?query`        — appended to the site root
 * - `name.html`     — the page of that slug (`product.html` → `/product/`)
 * - `name.html#a`   — the same, with the fragment preserved
 * - `/path/`        — resolved against the site root
 * - empty           — the site home
 *
 * `forma-saas-landing.html` and `blog-post.html` are aliases for `@home` and
 * `@latest-post` because the approved render uses them as chrome links to the
 * front page and to the newest article.
 *
 * @param string $url Raw attribute value.
 *
 * @return string Ready-to-escape URL.
 */
function nextsaas_resolve_url( string $url ): string {
	$url = trim( $url );

	if ( '' === $url ) {
		return home_url( '/' );
	}

	// A bare fragment is an in-page target, not something to resolve.
	if ( str_starts_with( $url, '#' ) ) {
		return $url;
	}

	// Off-site and protocol-relative URLs are already absolute.
	if ( str_starts_with( $url, '//' ) || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $url ) ) {
		return $url;
	}

	/*
	 * The fragment is split off before the token is resolved, because the approved
	 * render writes in-page links such as `@home#capabilities`: the token has to
	 * resolve to a URL first and the fragment is put back afterwards. Splitting it
	 * last would leave `@home#capabilities` looking like an unknown token.
	 */
	$fragment = '';

	if ( str_contains( $url, '#' ) ) {
		list( $url, $tail ) = explode( '#', $url, 2 );
		$fragment           = '#' . $tail;
	}

	if ( '@home' === $url ) {
		return home_url( '/' ) . $fragment;
	}

	if ( '@posts' === $url ) {
		$posts_page = (int) get_option( 'page_for_posts' );

		return ( $posts_page > 0 ? (string) get_permalink( $posts_page ) : home_url( '/' ) ) . $fragment;
	}

	if ( '@latest-post' === $url ) {
		$latest = get_posts(
			array(
				'numberposts'            => 1,
				'post_status'            => 'publish',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			),
		);

		return ( array() !== $latest ? (string) get_permalink( $latest[0] ) : nextsaas_resolve_url( '@posts' ) ) . $fragment;
	}

	if ( '@search' === $url ) {
		return home_url( '/?s=' ) . $fragment;
	}

	if ( str_starts_with( $url, '?' ) ) {
		return home_url( '/' ) . $url . $fragment;
	}

	if ( str_starts_with( $url, '/' ) ) {
		return home_url( $url ) . $fragment;
	}

	// Everything left is a page named by the approved render's file name.
	$slug = (string) preg_replace( '/\.html$/i', '', $url );

	if ( 'forma-saas-landing' === $slug || 'index' === $slug ) {
		return home_url( '/' ) . $fragment;
	}

	if ( 'blog-post' === $slug ) {
		return nextsaas_resolve_url( '@latest-post' ) . $fragment;
	}

	return home_url( '/' . ltrim( $slug, '/' ) . '/' ) . $fragment;
}

/**
 * Path component of a URL, without a trailing slash.
 *
 * @param string $url Absolute or relative URL.
 *
 * @return string Normalised path, `''` for the site root.
 */
function nextsaas_url_path( string $url ): string {
	$path = (string) wp_parse_url( $url, PHP_URL_PATH );

	return untrailingslashit( $path );
}

/**
 * Whether a link attribute points at the surface currently being rendered.
 *
 * Fragments and off-site URLs are never marked as current. Used to emit
 * `aria-current="page"` exactly where the approved render does.
 *
 * @param string $url Raw attribute value.
 *
 * @return bool True when the resolved target is the current request.
 */
function nextsaas_link_is_current( string $url ): bool {
	$url = trim( $url );

	if ( '' === $url || str_starts_with( $url, '#' ) ) {
		return false;
	}

	if ( '@search' === $url ) {
		return is_search();
	}

	$target = nextsaas_url_path( nextsaas_resolve_url( $url ) );

	if ( '' === $target ) {
		return is_front_page();
	}

	if ( is_singular() ) {
		return $target === nextsaas_url_path( (string) get_permalink() );
	}

	$posts_page = (int) get_option( 'page_for_posts' );

	if ( is_home() && $posts_page > 0 ) {
		return $target === nextsaas_url_path( (string) get_permalink( $posts_page ) );
	}

	return false;
}

/**
 * Resolve a bundled theme asset path to a URL.
 *
 * A block whose design slot is filled by a file that ships with the theme stores the
 * theme-relative path (`/assets/images/nextsaas-workspace.jpg`), because that is the
 * only form that survives being exported and re-imported. Printed as-is it would
 * resolve against the site root and 404, so every path under `assets/` is resolved
 * through `NEXTSAAS_URI` here — once, instead of in each renderer.
 *
 * @param string $path Stored path or any other URL.
 *
 * @return string URL ready for `esc_url()`.
 */
function nextsaas_asset_url( string $path ): string {
	$path = trim( $path );

	if ( ! preg_match( '#^/?assets/#', $path ) ) {
		return $path;
	}

	return NEXTSAAS_URI . '/' . ltrim( $path, '/' );
}

/**
 * Render a media attribute as an image, or as the approved labeled placeholder.
 *
 * The approved render never hotlinks and never fabricates a product screenshot:
 * where no licensed asset exists it draws a labelled reservation box that says so.
 * That behaviour is preserved exactly — an empty `mediaUrl`/`mediaId` produces the
 * placeholder, and any `<img>` produced here is either the site's own media library
 * item or the vendored workspace photograph.
 *
 * @param array<string, mixed> $media      Row holding `mediaId`, `mediaUrl`, `mediaAlt` and the placeholder keys.
 * @param string               $class_name Class the image or placeholder carries.
 * @param string               $loading    `eager` above the fold, `lazy` below it.
 *
 * @return string Image or placeholder markup.
 */
function nextsaas_media( array $media, string $class_name, string $loading = 'lazy' ): string {
	$media_id  = is_scalar( $media['mediaId'] ?? 0 ) ? absint( $media['mediaId'] ?? 0 ) : 0;
	$media_url = is_scalar( $media['mediaUrl'] ?? '' ) ? trim( (string) $media['mediaUrl'] ) : '';
	$alt       = is_scalar( $media['mediaAlt'] ?? '' ) ? trim( (string) $media['mediaAlt'] ) : '';

	if ( '' === $alt && $media_id > 0 ) {
		$alt = trim( (string) get_post_meta( $media_id, '_wp_attachment_image_alt', true ) );
	}

	if ( $media_id > 0 ) {
		/*
		 * The file's own ratio, declared on the element.
		 *
		 * WordPress marks media-library images with `sizes="auto, …"` after the block has
		 * rendered, and a browser that implements auto sizes lays such an image out as
		 * size-contained: it stops using the file's intrinsic ratio, so
		 * `width: 100%; height: auto` — how every NextSaaS image is sized — produced a
		 * wildly wrong height (a 1168×584 screenshot rendered 1168×1500, and no author
		 * rule can override the containment). A declared `aspect-ratio` is not intrinsic
		 * and therefore always applies, which keeps the pixels at the proportions the
		 * file really has whatever the browser does with auto sizes.
		 */
		$nextsaas_size  = wp_get_attachment_image_src( $media_id, 'full' );
		$nextsaas_ratio = is_array( $nextsaas_size ) && ! empty( $nextsaas_size[1] ) && ! empty( $nextsaas_size[2] )
			? ( (int) $nextsaas_size[1] ) . ' / ' . ( (int) $nextsaas_size[2] )
			: '';

		$image = wp_get_attachment_image(
			$media_id,
			'full',
			false,
			array_filter(
				array(
					'class'     => $class_name,
					'alt'       => $alt,
					'loading'   => $loading,
					'decoding'  => 'async',
					'style'     => '' !== $nextsaas_ratio ? 'aspect-ratio:' . $nextsaas_ratio : '',
				),
			),
		);

		if ( '' !== $image ) {
			return (string) $image;
		}
	}

	if ( '' !== $media_url ) {
		return sprintf(
			'<img class="%1$s" src="%2$s" alt="%3$s" loading="%4$s" decoding="async">',
			esc_attr( $class_name ),
			esc_url( nextsaas_asset_url( $media_url ) ),
			esc_attr( $alt ),
			esc_attr( $loading ),
		);
	}

	return nextsaas_media_placeholder( $media );
}

/**
 * The approved labeled placeholder for a section with no licensed asset.
 *
 * @param array<string, mixed> $media Row holding the placeholder keys.
 *
 * @return string Placeholder markup.
 */
function nextsaas_media_placeholder( array $media ): string {
	$read = static function ( string $key ) use ( $media ): string {
		return is_scalar( $media[ $key ] ?? '' ) ? trim( (string) $media[ $key ] ) : '';
	};

	$label  = $read( 'placeholderLabel' );
	$top    = $read( 'placeholderTop' );
	$badge  = $read( 'placeholderBadge' );
	$title  = $read( 'placeholderTitle' );
	$note   = $read( 'placeholderNote' );
	$foot   = $read( 'placeholderFoot' );
	$aspect = $read( 'placeholderAspect' );

	$style = '' !== $aspect ? ' style="aspect-ratio:' . esc_attr( $aspect ) . '"' : '';

	$markup  = '<div class="ph-img image-reserve"' . $style . ' role="img" aria-label="' . esc_attr( $label ) . '">';
	$markup .= '<div class="reserve-top">';
	$markup .= '<span>' . esc_html( $top ) . '</span>';

	if ( '' !== $badge ) {
		$markup .= '<span>' . esc_html( $badge ) . '</span>';
	}

	$markup .= '</div>';
	$markup .= '<div class="reserve-center">';
	$markup .= '<span class="reserve-cross">' . nextsaas_icon( 'plus' ) . '</span>';
	$markup .= '<strong>' . esc_html( $title ) . '</strong>';

	if ( '' !== $note ) {
		$markup .= '<span>' . esc_html( $note ) . '</span>';
	}

	$markup .= '</div>';

	if ( '' !== $foot ) {
		$markup .= '<div class="reserve-bottom"><span>' . esc_html( $foot ) . '</span>' . nextsaas_icon( 'arrow-up-right' ) . '</div>';
	}

	$markup .= '</div>';

	return $markup;
}

/**
 * Render one call to action.
 *
 * An empty URL renders a `<button type="button">` exactly as the approved render
 * does for a preview-only control; a non-empty URL renders an `<a>`. Variants map
 * onto the render's own class names so no second button vocabulary appears.
 *
 * @param array<string, mixed> $cta   Row with `label`, `url`, `variant`, `arrow`, `icon` and an optional
 *                                    `richPath` naming the in-place editing marker the label carries.
 * @param string               $extra Extra classes appended to the button classes.
 *
 * @return string CTA markup, or an empty string when it carries no label.
 */
function nextsaas_cta( array $cta, string $extra = '' ): string {
	$label = trim( nextsaas_attr( $cta, 'label' ) );

	if ( '' === $label ) {
		return '';
	}

	$url     = trim( nextsaas_attr( $cta, 'url' ) );
	$variant = nextsaas_attr( $cta, 'variant', 'primary' );
	$arrow   = ! empty( $cta['arrow'] );
	// The approved render puts an inline arrow-up-right inside a call to action
	// unless it is a chrome control (the drawer toggle, a dialog dismiss).
	$icon    = ! array_key_exists( 'icon', $cta ) || ! empty( $cta['icon'] );

	/*
	 * A caller that renders a per-row call to action — a pricing plan, for example —
	 * names the marker that row's label is edited through. Without it the marker stays
	 * the literal `ctaLabel` every other caller already used, so nothing about the
	 * existing callers changes.
	 */
	$rich_path = trim( nextsaas_attr( $cta, 'richPath' ) );
	$rich      = 'data-nextsaas-rich="' . esc_attr( '' !== $rich_path ? $rich_path : 'ctaLabel' ) . '"';

	$classes = array( 'btn', 'btn-' . sanitize_html_class( $variant ) );

	if ( $arrow ) {
		$classes[] = 'btn-arrow';
	}

	if ( '' !== $extra ) {
		$classes[] = $extra;
	}

	$class_attr = esc_attr( implode( ' ', $classes ) );
	$inner      = nextsaas_rich( $label ) . ( $icon ? nextsaas_icon( 'arrow-up-right' ) : '' );

	if ( '' === $url ) {
		return '<button type="button" class="' . $class_attr . '" ' . $rich . '>' . $inner . '</button>';
	}

	return '<a class="' . $class_attr . '" href="' . esc_url( nextsaas_resolve_url( $url ) ) . '" ' . $rich . '>' . $inner . '</a>';
}

/**
 * The post the current request is rendering.
 *
 * Query-aware sections resolve the same post: the queried object on a singular
 * request, and the global post as the fallback a block preview or an inner template
 * render needs. A null result means "no post here", which every such renderer
 * answers with its own deterministic fallback rather than a query of its own.
 *
 * @return WP_Post|null Queried post, or null when no post is in context.
 */
function nextsaas_queried_post(): ?WP_Post {
	$object = get_queried_object();
	$post   = $object instanceof WP_Post ? $object : ( $GLOBALS['post'] ?? null );

	return $post instanceof WP_Post ? $post : null;
}

/**
 * Estimate reading time in minutes for a piece of content.
 *
 * @param string $content Raw content, HTML or plain text.
 *
 * @return int Reading time in minutes, never below 1.
 */
function nextsaas_reading_time( string $content ): int {
	$words = preg_split( '/\s+/', trim( wp_strip_all_tags( $content ) ) );

	if ( false === $words ) {
		return 1;
	}

	return max( 1, (int) ceil( count( array_filter( $words ) ) / 220 ) );
}
