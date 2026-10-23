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
	$classes = nextsaas_scope_class( 'nextsaas-' . $slug, $extra_classes );

	$attrs = array(
		'class'                 => $classes,
		'data-nextsaas-section' => $slug,
	);

	$section_id = nextsaas_attr( $attributes, 'sectionId' );

	if ( '' !== $section_id ) {
		$attrs['id'] = $section_id;
	}

	if ( '' !== $style ) {
		$attrs['style'] = $style;
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
 * @param array<string, mixed> $cta   Row with `label`, `url`, `variant`, `arrow`, `icon`.
 * @param string               $extra Extra classes appended to the button classes.
 *
 * @return string CTA markup, or an empty string when it carries no label.
 */
function nextsaas_cta( array $cta, string $extra = '' ): string {
	$label = is_scalar( $cta['label'] ?? '' ) ? trim( (string) $cta['label'] ) : '';

	if ( '' === $label ) {
		return '';
	}

	$url     = is_scalar( $cta['url'] ?? '' ) ? trim( (string) $cta['url'] ) : '';
	$variant = is_scalar( $cta['variant'] ?? '' ) ? (string) $cta['variant'] : 'primary';
	$arrow   = ! empty( $cta['arrow'] );
	// The approved render puts an inline arrow-up-right inside a call to action
	// unless it is a chrome control (the drawer toggle, a dialog dismiss).
	$icon    = ! array_key_exists( 'icon', $cta ) || ! empty( $cta['icon'] );

	$classes = array( 'btn', 'btn-' . sanitize_html_class( $variant ) );

	if ( $arrow ) {
		$classes[] = 'btn-arrow';
	}

	if ( '' !== $extra ) {
		$classes[] = $extra;
	}

	$class_attr = esc_attr( implode( ' ', $classes ) );
	$rich       = nextsaas_rich( $label ) . ( $icon ? nextsaas_icon( 'arrow-up-right' ) : '' );

	if ( '' === $url ) {
		return '<button type="button" class="' . $class_attr . '" data-nextsaas-rich="ctaLabel">' . $rich . '</button>';
	}

	return '<a class="' . $class_attr . '" href="' . esc_url( nextsaas_resolve_url( $url ) ) . '" data-nextsaas-rich="ctaLabel">' . $rich . '</a>';
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
