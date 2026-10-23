<?php

/**
 * Walker that renders the theme's own header menu markup.
 *
 * It reproduces the approved render's flattened navigation — one
 * `<div class="nav-item">` per top-level entry holding a trigger plus a
 * `<div class="submenu">` panel of child links with their descriptions — instead of
 * WordPress's default `<ul>`/`<li>` tree. Nothing calls the parent walker's
 * `start_el`, so no `<li>` can leak into the markup.
 *
 * Two behaviours make it safe to run with or without the optional mega-navigation
 * plugin:
 *
 * - A top-level item carrying `has-mega-menu` renders its trigger as an `<a>`,
 *   which is what the plugin's own `setupItem()` looks for (`:scope > a`); every
 *   other parent renders a `<button type="button">`, because a grouping item has no
 *   destination of its own.
 * - `start_lvl()` records that a panel was actually opened. The plugin suppresses
 *   that call for an item whose mega panel replaces the submenu, so when the call
 *   did not happen the child links are skipped as well — no orphan links can be
 *   emitted outside a panel, and no panel id is ever referenced before it exists.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Header navigation walker.
 *
 * Deliberately not `final`: the class is the theme's documented seam for a plugin
 * or a grandchild theme that needs to adjust the emitted markup — the optional
 * mega-navigation plugin already subclasses a host walker to suppress a submenu it
 * replaces with a panel, and the same move has to stay open here.
 */
class Nextsaas_Site_Header_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Id of the top-level item currently being rendered.
	 *
	 * @var int
	 */
	private int $current_root_id = 0;

	/**
	 * Whether the current top-level item opened a child panel.
	 *
	 * Reset on every top-level item, set by `start_lvl()`, and read before the
	 * first child is rendered.
	 *
	 * @var bool
	 */
	private bool $current_root_has_panel = false;

	/**
	 * Open the child panel of the current top-level item.
	 *
	 * @param string        $output Passed by reference. Used to append additional content.
	 * @param int           $depth  Depth of menu item. Used for padding.
	 * @param stdClass|null $args   An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ): void {
		if ( 0 !== $depth ) {
			return;
		}

		$this->current_root_has_panel = true;

		$output .= sprintf(
			'<div class="submenu" id="%s" hidden>',
			esc_attr( $this->submenu_id( $this->current_root_id ) ),
		);
	}

	/**
	 * Close the child panel of the current top-level item.
	 *
	 * @param string        $output Passed by reference. Used to append additional content.
	 * @param int           $depth  Depth of menu item. Used for padding.
	 * @param stdClass|null $args   An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ): void {
		if ( 0 !== $depth ) {
			return;
		}

		$output .= '</div>';
	}

	/**
	 * Start the markup for one menu item.
	 *
	 * @param string        $output Passed by reference. Used to append additional content.
	 * @param WP_Post       $item   Menu item data object.
	 * @param int           $depth  Depth of menu item. Used for padding.
	 * @param stdClass|null $args   An object of wp_nav_menu() arguments.
	 * @param int           $id     Current item id.
	 *
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ): void {
		if ( ! $item instanceof WP_Post ) {
			return;
		}

		$title    = apply_filters( 'the_title', $this->field( $item, 'title' ), $item->ID );
		$classes  = $this->item_classes( $item );
		$is_mega  = in_array( nextsaas_mega_nav_item_class(), $classes, true );
		$has_kids = in_array( 'menu-item-has-children', $classes, true );
		$url      = trim( $this->field( $item, 'url' ) );

		if ( 0 === $depth ) {
			$this->current_root_id         = (int) $item->ID;
			$this->current_root_has_panel  = false;

			$output .= sprintf(
				'<div class="%s">',
				esc_attr( implode( ' ', array_merge( array( 'nav-item' ), $classes ) ) ),
			);

			if ( $has_kids ) {
				$output .= $is_mega
					? $this->mega_trigger( $title, $url )
					: $this->disclosure_trigger( $title, $url, $item );
			} else {
				$output .= $this->leaf_link( $title, $url, $item, '' );
			}

			return;
		}

		/*
		 * A top-level item whose panel was suppressed by the mega-navigation plugin
		 * is rendered by that panel, so its ordinary child links are skipped here.
		 * `start_lvl()` is the signal: it is only called when an item really does
		 * open a child panel.
		 */
		if ( 1 === $depth && ! $this->current_root_has_panel ) {
			return;
		}

		$description = trim( $this->field( $item, 'description' ) );
		$markup      = '<strong>' . esc_html( $title ) . ' ' . nextsaas_icon( 'arrow-up-right' ) . '</strong>';

		if ( '' !== $description ) {
			$markup .= '<small>' . esc_html( $description ) . '</small>';
		}

		$output .= $this->leaf_link( $markup, $url, $item, '', false );
	}

	/**
	 * Close the markup for one menu item.
	 *
	 * @param string        $output Passed by reference. Used to append additional content.
	 * @param WP_Post       $item   Menu item data object.
	 * @param int           $depth  Depth of menu item. Used for padding.
	 * @param stdClass|null $args   An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ): void {
		if ( 0 !== $depth ) {
			return;
		}

		$output .= '</div>';
	}

	/**
	 * Markup for the header's navigation while no classic menu is assigned.
	 *
	 * Mirrors the walker's own structure exactly — same elements, same classes,
	 * same ARIA — so switching between the fallback and an assigned menu changes no
	 * class name and no nesting. It lives on this class rather than in `render.php`
	 * because a dynamic block's render file is included once per block instance, and
	 * a function declared there would be redeclared on the second one.
	 *
	 * @param array<string, mixed> $args Arguments `wp_nav_menu()` passed through.
	 *
	 * @return string Menu markup.
	 */
	public static function fallback( array $args ): string {
		$items = $args['nextsaas_items'] ?? array();

		if ( ! is_array( $items ) ) {
			return '';
		}

		$markup = '';

		foreach ( array_values( $items ) as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$label    = is_scalar( $item['label'] ?? '' ) ? trim( (string) $item['label'] ) : '';
			$url      = is_scalar( $item['url'] ?? '' ) ? trim( (string) $item['url'] ) : '';
			$children = is_array( $item['children'] ?? null ) ? $item['children'] : array();

			if ( '' === $label ) {
				continue;
			}

			$markup .= '<div class="nav-item">';

			if ( array() === $children ) {
				$href = '' === $url || '#' === $url ? '' : $url;

				$markup .= '' === $href
					? '<span class="nav-label">' . esc_html( $label ) . '</span>'
					: sprintf(
						'<a href="%1$s"%2$s>%3$s</a>',
						esc_url( nextsaas_resolve_url( $href ) ),
						nextsaas_link_is_current( $href ) ? ' aria-current="page"' : '',
						esc_html( $label ),
					);

				$markup .= '</div>';

				continue;
			}

			$panel_id = 'nextsaas-submenu-fallback-' . (int) $index;
			$chevron  = ' <span class="chevron">' . nextsaas_icon( 'chevron-down' ) . '</span>';

			if ( '' !== $url && '#' !== $url ) {
				$markup .= sprintf(
					'<a class="nav-trigger" href="%1$s" aria-haspopup="true" aria-expanded="false" aria-controls="%2$s"%3$s>%4$s%5$s</a>',
					esc_url( nextsaas_resolve_url( $url ) ),
					esc_attr( $panel_id ),
					nextsaas_link_is_current( $url ) ? ' aria-current="page"' : '',
					esc_html( $label ),
					$chevron,
				);
			} else {
				$markup .= sprintf(
					'<button type="button" class="nav-trigger" data-nextsaas-submenu-trigger aria-haspopup="true" aria-expanded="false" aria-controls="%1$s">%2$s%3$s</button>',
					esc_attr( $panel_id ),
					esc_html( $label ),
					$chevron,
				);
			}

			$markup .= sprintf( '<div class="submenu" id="%s" hidden>', esc_attr( $panel_id ) );

			foreach ( $children as $child ) {
				if ( ! is_array( $child ) ) {
					continue;
				}

				$child_label = is_scalar( $child['label'] ?? '' ) ? trim( (string) $child['label'] ) : '';
				$child_url   = is_scalar( $child['url'] ?? '' ) ? trim( (string) $child['url'] ) : '';
				$child_note  = is_scalar( $child['description'] ?? '' ) ? trim( (string) $child['description'] ) : '';

				if ( '' === $child_label ) {
					continue;
				}

				$markup .= sprintf(
					'<a href="%1$s"%2$s><strong>%3$s %4$s</strong>',
					esc_url( nextsaas_resolve_url( $child_url ) ),
					nextsaas_link_is_current( $child_url ) ? ' aria-current="page"' : '',
					esc_html( $child_label ),
					nextsaas_icon( 'arrow-up-right' ),
				);

				if ( '' !== $child_note ) {
					$markup .= '<small>' . esc_html( $child_note ) . '</small>';
				}

				$markup .= '</a>';
			}

			$markup .= '</div></div>';
		}

		return $markup;
	}

	/**
	 * The `id` a top-level item's child panel carries.
	 *
	 * Derived from the menu item id so a trigger and its panel can never drift
	 * apart, and so two menus on one page cannot collide.
	 *
	 * @param int $item_id Menu item id.
	 *
	 * @return string Panel id.
	 */
	private function submenu_id( int $item_id ): string {
		return 'nextsaas-submenu-' . $item_id;
	}

	/**
	 * One navigation field of a menu item.
	 *
	 * `wp_get_nav_menu_items()` returns `WP_Post` rows that `wp_setup_nav_menu_item()`
	 * decorates at runtime, so `title`, `url` and `description` are not declared on the
	 * class. Reading them through `get_object_vars()` is the same route the optional
	 * mega-navigation plugin takes, and it keeps a missing field from becoming a warning.
	 *
	 * @param WP_Post $item Menu item.
	 * @param string  $key  Field name.
	 *
	 * @return string Field value, `''` when the item does not carry it.
	 */
	private function field( WP_Post $item, string $key ): string {
		$fields = get_object_vars( $item );

		return isset( $fields[ $key ] ) && is_scalar( $fields[ $key ] ) ? (string) $fields[ $key ] : '';
	}

	/**
	 * Sanitised class list of a menu item.
	 *
	 * @param WP_Post $item Menu item.
	 *
	 * @return array<int, string> Class names.
	 */
	private function item_classes( WP_Post $item ): array {
		$raw = get_object_vars( $item )['classes'] ?? array();

		if ( ! is_array( $raw ) ) {
			return array();
		}

		$classes = array();

		foreach ( $raw as $class ) {
			if ( is_scalar( $class ) && '' !== (string) $class ) {
				$classes[] = sanitize_html_class( (string) $class );
			}
		}

		return array_values( array_unique( $classes ) );
	}

	/**
	 * A plain link.
	 *
	 * @param string  $inner  Already-escaped inner markup.
	 * @param string  $url    Menu item URL.
	 * @param WP_Post $item   Menu item.
	 * @param string  $extra  Extra attributes appended before `href`.
	 * @param bool    $escape Whether `$inner` is plain text that still needs escaping.
	 *
	 * @return string Link markup.
	 */
	private function leaf_link( string $inner, string $url, WP_Post $item, string $extra = '', bool $escape = true ): string {
		$href    = '' === $url || '#' === $url ? '' : $url;
		$content = $escape ? esc_html( $inner ) : $inner;

		if ( '' === $href ) {
			return '<span class="nav-label"' . $extra . '>' . $content . '</span>';
		}

		return sprintf(
			'<a href="%1$s"%2$s%3$s>%4$s</a>',
			esc_url( $href ),
			$this->current_attr( $url, $item ),
			$extra,
			$content,
		);
	}

	/**
	 * The trigger of a top-level item that has children and no mega panel.
	 *
	 * A grouping entry with a real destination stays a link; an entry whose URL is
	 * empty or a bare fragment becomes a button, exactly like the approved render.
	 *
	 * @param string  $title Item title.
	 * @param string  $url   Item URL.
	 * @param WP_Post $item  Menu item.
	 *
	 * @return string Trigger markup.
	 */
	private function disclosure_trigger( string $title, string $url, WP_Post $item ): string {
		$controls = sprintf(
			' aria-haspopup="true" aria-expanded="false" aria-controls="%s"',
			esc_attr( $this->submenu_id( (int) $item->ID ) ),
		);
		$inner    = esc_html( $title ) . ' <span class="chevron">' . nextsaas_icon( 'chevron-down' ) . '</span>';

		if ( '' !== $url && '#' !== $url ) {
			return $this->leaf_link(
				$inner,
				$url,
				$item,
				' class="nav-trigger"' . $controls,
				false,
			);
		}

		return '<button type="button" class="nav-trigger" data-nextsaas-submenu-trigger' . $controls . '>' . $inner . '</button>';
	}

	/**
	 * The trigger of a top-level item whose panel the mega-navigation plugin owns.
	 *
	 * The plugin's `setupItem()` looks for `:scope > a` and then sets
	 * `aria-haspopup`, `aria-expanded` and `aria-controls` itself, pointing
	 * `aria-controls` at the panel element it just rendered. Printing an
	 * `aria-controls` here would reference an id that does not exist yet, so only
	 * the two states it does not overwrite are printed.
	 *
	 * @param string $title Item title.
	 * @param string $url   Item URL.
	 *
	 * @return string Trigger markup.
	 */
	private function mega_trigger( string $title, string $url ): string {
		$href  = '' === $url ? '#' : $url;
		$inner = esc_html( $title ) . ' <span class="chevron">' . nextsaas_icon( 'chevron-down' ) . '</span>';

		return sprintf(
			'<a class="nav-trigger" href="%1$s" aria-haspopup="true" aria-expanded="false">%2$s</a>',
			esc_url( $href ),
			$inner,
		);
	}

	/**
	 * `aria-current="page"` when the item points at the current request.
	 *
	 * @param string  $url  Item URL.
	 * @param WP_Post $item Menu item.
	 *
	 * @return string Attribute, or an empty string.
	 */
	private function current_attr( string $url, WP_Post $item ): string {
		if ( '' === $url || '#' === $url ) {
			return '';
		}

		if ( self::url_is_current( $url ) ) {
			return ' aria-current="page"';
		}

		return '';
	}

	/**
	 * Whether a menu item URL matches the request being rendered.
	 *
	 * The approved render decides this client-side by comparing the last path
	 * segment; doing it server-side keeps the state correct with JavaScript off.
	 *
	 * @param string $url Item URL.
	 *
	 * @return bool True when the item addresses the current request.
	 */
	public static function url_is_current( string $url ): bool {
		$url = trim( $url );

		// An empty destination or a bare fragment is not a page, so it is never
		// "current": a fragment parses to an empty path, which would otherwise
		// equal the front page's own empty path and mark the item by accident.
		if ( '' === $url || str_starts_with( $url, '#' ) ) {
			return false;
		}

		$path = nextsaas_url_path( $url );

		if ( is_front_page() || is_home() ) {
			$home = nextsaas_url_path( home_url( '/' ) );

			return $path === $home;
		}

		if ( is_singular() ) {
			return $path === nextsaas_url_path( (string) get_permalink() );
		}

		if ( is_search() ) {
			return str_contains( $url, 's=' );
		}

		return false;
	}
}
