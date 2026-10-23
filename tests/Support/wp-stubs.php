<?php

/**
 * Minimal WordPress doubles for the NextSaaS unit suite.
 *
 * The suite tests NextSaaS's own decisions — which blocks are registered, what the
 * query arguments are, how a link attribute resolves, how the header walker decides
 * `aria-current`, and whether the design manifest still covers the block layer — so
 * it only needs the handful of WordPress functions those decisions touch.
 * Everything here is a double: it records calls and returns values the test
 * controls. No WordPress core file is loaded, which is why `composer test` stays
 * fast and works without a database.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

$GLOBALS['nextsaas_test'] = array();
$GLOBALS['nextsaas_meta'] = array();

/**
 * Reset every double between tests.
 */
function nextsaas_test_reset(): void {
	$GLOBALS['nextsaas_test'] = array(
		'actions'            => array(),
		'filters'            => array(),
		'blocks'             => array(),
		'inline_scripts'     => array(),
		'localized'          => array(),
		'pattern_categories' => array(),
		'post_types'         => array( 'post', 'page' ),
		'options'            => array( 'posts_per_page' => 10, 'page_for_posts' => 0 ),
		'home_url'           => 'https://example.test',
		'permalink'          => 'https://example.test/',
		'front_page'         => false,
		'home'               => false,
		'singular'           => false,
		'search'             => false,
		'query_vars'         => array(),
		'queried_object'     => null,
		'posts'              => array(),
		'nav_menu_menus'     => array(),
		'the_title'          => null,
	);
	$GLOBALS['nextsaas_meta'] = array();
}

/**
 * Record an action registration.
 *
 * @param string   $hook     Hook name.
 * @param callable $callback Callback.
 * @param int      $priority Priority.
 */
function add_action( string $hook, $callback, int $priority = 10 ): void {
	$GLOBALS['nextsaas_test']['actions'][ $hook ][] = $callback;
}

/**
 * Record a filter registration.
 *
 * @param string   $hook     Hook name.
 * @param callable $callback Callback.
 * @param int      $priority Priority.
 * @param int      $accepted Accepted args.
 */
function add_filter( string $hook, $callback, int $priority = 10, int $accepted = 1 ): void {
	$GLOBALS['nextsaas_test']['filters'][ $hook ][] = $callback;
}

/**
 * Run the registered callbacks for a filter.
 *
 * @param string $hook    Hook name.
 * @param mixed  $value   Initial value.
 * @param mixed  ...$rest Extra arguments.
 *
 * @return mixed Filtered value.
 */
function apply_filters( string $hook, $value, ...$rest ) {
	foreach ( $GLOBALS['nextsaas_test']['filters'][ $hook ] ?? array() as $callback ) {
		$value = call_user_func_array( $callback, array_merge( array( $value ), $rest ) );
	}

	return $value;
}

/**
 * Run the registered callbacks for an action.
 *
 * @param string $hook    Hook name.
 * @param mixed  ...$args Arguments.
 */
function do_action( string $hook, ...$args ): void {
	foreach ( $GLOBALS['nextsaas_test']['actions'][ $hook ] ?? array() as $callback ) {
		call_user_func_array( $callback, $args );
	}
}

/**
 * Record a block registration.
 *
 * @param string $name Block name or directory.
 * @param array  $args Arguments.
 *
 * @return bool Always true.
 */
function register_block_type( string $name, array $args = array() ): bool {
	$GLOBALS['nextsaas_test']['blocks'][] = $name;

	return true;
}

/**
 * Record a pattern category registration.
 *
 * @param string $slug Category slug.
 * @param array  $args Arguments.
 *
 * @return bool Always true.
 */
function register_block_pattern_category( string $slug, array $args = array() ): bool {
	$GLOBALS['nextsaas_test']['pattern_categories'][ $slug ] = $args;

	return true;
}

/**
 * Record an inline script.
 *
 * @param string $handle   Script handle.
 * @param string $data     Script body.
 * @param string $position Position.
 */
function wp_add_inline_script( string $handle, string $data, string $position = 'after' ): bool {
	$GLOBALS['nextsaas_test']['inline_scripts'][] = array( $handle, $data, $position );

	return true;
}

/**
 * Record localised data.
 *
 * @param string $handle Handle.
 * @param string $name   Object name.
 * @param array  $data   Data.
 */
function wp_localize_script( string $handle, string $name, array $data = array() ): bool {
	$GLOBALS['nextsaas_test']['localized'][ $name ] = $data;

	return true;
}

/**
 * Whether a style handle is registered or enqueued.
 *
 * @param string $handle Handle.
 * @param string $list   List name.
 */
function wp_style_is( string $handle, string $list = 'enqueued' ): bool {
	return false;
}

/**
 * Whether a script handle is registered or enqueued.
 *
 * @param string $handle Handle.
 * @param string $list   List name.
 */
function wp_script_is( string $handle, string $list = 'enqueued' ): bool {
	return false;
}

/**
 * Record an enqueued style.
 */
function wp_enqueue_style( string $handle, string $src = '', array $deps = array(), $version = false ): void {}

/**
 * Record an enqueued script.
 */
function wp_enqueue_script( string $handle, string $src = '', array $deps = array(), $version = false, $args = array() ): void {}

/**
 * Record an editor stylesheet.
 */
function add_editor_style( string $stylesheet ): void {}

/**
 * Print an inline script tag.
 */
function wp_print_inline_script_tag( string $data, array $attributes = array() ): void {}

/**
 * The active theme's directory.
 */
function get_stylesheet_directory(): string {
	return NEXTSAAS_DIR;
}

/**
 * The active theme's URI.
 */
function get_stylesheet_directory_uri(): string {
	return NEXTSAAS_URI;
}

/**
 * The parent theme's directory.
 */
function get_template_directory(): string {
	return NEXTSAAS_DIR;
}

/**
 * Register navigation menu locations.
 *
 * @param array $locations Locations.
 */
function register_nav_menus( array $locations = array() ): void {
	$GLOBALS['nextsaas_test']['nav_menu_menus'] = $locations;
}

/**
 * Whether a navigation menu is assigned to a location.
 *
 * @param string $location Location.
 */
function has_nav_menu( string $location ): bool {
	return (bool) ( $GLOBALS['nextsaas_test']['assigned_menus'][ $location ] ?? false );
}

/**
 * Whether a post type is registered.
 *
 * @param string $type Post type.
 */
function post_type_exists( string $type ): bool {
	return in_array( $type, $GLOBALS['nextsaas_test']['post_types'], true );
}

/**
 * The site home URL.
 *
 * @param string $path Optional path.
 */
function home_url( string $path = '' ): string {
	return rtrim( $GLOBALS['nextsaas_test']['home_url'], '/' ) . '/' . ltrim( $path, '/' );
}

/**
 * Read an option.
 *
 * @param string $name    Option name.
 * @param mixed  $default Default.
 *
 * @return mixed
 */
function get_option( string $name, $default = false ) {
	return $GLOBALS['nextsaas_test']['options'][ $name ] ?? $default;
}

/**
 * Permalink of a post.
 *
 * @param mixed $post Post.
 */
function get_permalink( $post = 0 ): string {
	return (string) ( $GLOBALS['nextsaas_test']['permalink'] );
}

/**
 * Latest posts.
 *
 * @param array $args Arguments.
 *
 * @return array<int, object>
 */
function get_posts( array $args = array() ): array {
	return $GLOBALS['nextsaas_test']['posts'];
}

/**
 * The blog name.
 *
 * @param string $show Type.
 */
function get_bloginfo( string $show = '' ): string {
	return 'NextSaaS';
}

/**
 * Attachment metadata.
 *
 * @param int    $id  Attachment id.
 * @param string $key Meta key.
 *
 * @return mixed
 */
function get_post_meta( int $id, string $key = '', bool $single = false ) {
	return $GLOBALS['nextsaas_meta'][ $id ][ $key ] ?? '';
}

/**
 * Build an image tag for an attachment.
 *
 * @param int    $id   Attachment id.
 * @param string $size Size.
 * @param bool   $icon Icon.
 * @param array  $attr Attributes.
 *
 * @return string
 */
function wp_get_attachment_image( int $id, string $size = 'thumbnail', bool $icon = false, array $attr = array() ): string {
	$src = $GLOBALS['nextsaas_test']['attachment_src'][ $id ] ?? '';

	if ( '' === $src ) {
		return '';
	}

	return sprintf(
		'<img src="%s" alt="%s" class="%s" />',
		esc_url( $src ),
		esc_attr( (string) ( $attr['alt'] ?? '' ) ),
		esc_attr( (string) ( $attr['class'] ?? '' ) ),
	);
}

/**
 * The object the current request is for.
 *
 * @return mixed
 */
function get_queried_object() {
	return $GLOBALS['nextsaas_test']['queried_object'];
}

/**
 * Whether this is the front page.
 */
function is_front_page(): bool {
	return (bool) $GLOBALS['nextsaas_test']['front_page'];
}

/**
 * Whether this is the posts index.
 */
function is_home(): bool {
	return (bool) $GLOBALS['nextsaas_test']['home'];
}

/**
 * Whether this is a singular request.
 */
function is_singular(): bool {
	return (bool) $GLOBALS['nextsaas_test']['singular'];
}

/**
 * Whether this is a search request.
 */
function is_search(): bool {
	return (bool) $GLOBALS['nextsaas_test']['search'];
}

/**
 * Parse a URL.
 *
 * @param string $url       URL.
 * @param int    $component Component.
 *
 * @return mixed
 */
function wp_parse_url( string $url, int $component = -1 ) {
	return parse_url( $url, $component );
}

/**
 * Add a query argument to a URL.
 *
 * @param array  $args Arguments.
 * @param string $url  URL.
 */
function add_query_arg( array $args, string $url ): string {
	$separator = str_contains( $url, '?' ) ? '&' : '?';

	return $url . $separator . http_build_query( $args, '', '&', PHP_QUERY_RFC3986 );
}

/**
 * Remove a trailing slash.
 *
 * @param string $value Value.
 */
function untrailingslashit( string $value ): string {
	return rtrim( $value, '/\\' );
}

/**
 * Absolute integer.
 *
 * @param mixed $value Value.
 */
function absint( $value ): int {
	return abs( (int) $value );
}

/**
 * Sanitise an HTML class name.
 *
 * @param string $class Class.
 */
function sanitize_html_class( string $class ): string {
	return (string) preg_replace( '/[^A-Za-z0-9_-]/', '', $class );
}

/**
 * Sanitise a key.
 *
 * @param string $key Key.
 */
function sanitize_key( string $key ): string {
	return (string) preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $key ) );
}

/**
 * Sanitise a hex colour.
 *
 * @param string $color Colour.
 *
 * @return string|null
 */
function sanitize_hex_color( string $color ): ?string {
	return preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color ) ? $color : null;
}

/**
 * Escape text for HTML output.
 *
 * @param string $text Text.
 */
function esc_html( string $text ): string {
	return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Escape text for an attribute.
 *
 * @param string $text Text.
 */
function esc_attr( string $text ): string {
	return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Escape a URL.
 *
 * @param string $url URL.
 */
function esc_url( string $url ): string {
	return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
}

/**
 * Strip all tags from a value.
 *
 * @param string $value Value.
 */
function wp_strip_all_tags( string $value ): string {
	return strip_tags( $value );
}

/**
 * Allow only post-safe HTML.
 *
 * @param string $value Value.
 */
function wp_kses_post( string $value ): string {
	return strip_tags( $value, '<br><em><strong><i><b><a><span>' );
}

/**
 * Translate a string.
 *
 * @param string $text   Text.
 * @param string $domain Domain.
 */
function __( string $text, string $domain = 'default' ): string {
	return $text;
}

/**
 * Translate and escape a string.
 *
 * @param string $text   Text.
 * @param string $domain Domain.
 */
function esc_html__( string $text, string $domain = 'default' ): string {
	return esc_html( $text );
}

/**
 * Translate a plural string.
 *
 * @param string $single Single form.
 * @param string $plural Plural form.
 * @param int    $number Number.
 * @param string $domain Domain.
 */
function _n( string $single, string $plural, int $number, string $domain = 'default' ): string {
	return 1 === $number ? $single : $plural;
}

/**
 * Format a number for the current locale.
 *
 * @param mixed $number Number.
 *
 * @return string
 */
function number_format_i18n( $number ): string {
	return number_format( (float) $number );
}

/**
 * Menu item double.
 *
 * `blocks/site-header/class-nextsaas-site-header-menu-walker.php` type-hints
 * `\WP_Post` because that is what `wp_nav_menu()` hands a walker, so the suite
 * needs a real class to instantiate. Only the fields the walker and the registry
 * read are declared; the rest are dynamic, exactly as in core.
 */
class WP_Post {

	/**
	 * Construct a menu item.
	 *
	 * @param array<string, mixed> $fields Item fields.
	 */
	public function __construct( array $fields = array() ) {
		$this->ID          = (int) ( $fields['ID'] ?? 0 );
		$this->title       = (string) ( $fields['title'] ?? '' );
		$this->url         = (string) ( $fields['url'] ?? '' );
		$this->description = (string) ( $fields['description'] ?? '' );
		$this->classes     = (array) ( $fields['classes'] ?? array() );
		$this->child_items = (array) ( $fields['child_items'] ?? array() );
	}

	/** @var int */
	public $ID = 0;

	/** @var string */
	public $title = '';

	/** @var string */
	public $url = '';

	/** @var string */
	public $description = '';

	/** @var array<int, string> */
	public $classes = array();

	/**
	 * Nested items.
	 *
	 * Core resolves children from a separate `children_elements` map; the suite's
	 * `Walker` double reads them from the item instead, which is enough to exercise
	 * the walker's depth handling without reconstructing that map.
	 *
	 * @var array<int, mixed>
	 */
	public $child_items = array();
}

/**
 * Minimal `Walker` double.
 *
 * Core's `Walker::walk()` is the only part of the base class the theme's walker
 * depends on, so the double reproduces its call order exactly: `start_el`, then
 * `start_lvl`/children/`end_lvl` when the element has children and depth allows,
 * then `end_el`.
 */
class Walker {

	/**
	 * Arbitrary public state core's walkers carry.
	 *
	 * @var mixed
	 */
	public $has_children = false;

	/**
	 * Walk a flat list of elements.
	 *
	 * @param array<int, mixed> $elements  Elements.
	 * @param int               $max_depth Max depth.
	 * @param mixed             ...$args   Arguments handed to each callback.
	 *
	 * @return string Emitted markup.
	 */
	public function walk( $elements, $max_depth, ...$args ): string {
		$output = '';

		foreach ( (array) $elements as $element ) {
			$this->display_element( $element, $output, (int) $max_depth, 0, $args, array() );
		}

		return $output;
	}

	/**
	 * Emit one element and its children.
	 *
	 * @param mixed             $element   Element.
	 * @param string            $output    Output buffer, by reference.
	 * @param int               $max_depth Max depth.
	 * @param int               $depth     Current depth.
	 * @param array<int, mixed> $args      Callback arguments.
	 * @param array<int, mixed> $id        Ancestor ids.
	 */
	protected function display_element( $element, &$output, $max_depth, $depth, $args, $id ): void {
		$this->start_el( $output, $element, $depth, ...array_values( $args ) );

		$children = is_object( $element ) ? (array) ( $element->child_items ?? array() ) : array();

		if ( array() !== $children && ( 0 === $max_depth || $max_depth < 0 || $depth + 1 < $max_depth ) ) {
			$this->start_lvl( $output, $depth, ...array_values( $args ) );

			foreach ( $children as $child ) {
				$this->display_element( $child, $output, $max_depth, $depth + 1, $args, $id );
			}

			$this->end_lvl( $output, $depth, ...array_values( $args ) );
		}

		$this->end_el( $output, $element, $depth, ...array_values( $args ) );
	}

	/**
	 * Open a child level.
	 *
	 * The signature mirrors core's exactly — a fixed `$args` parameter rather than a
	 * variadic — because a theme walker declares `start_lvl( &$output, $depth, $args )`
	 * and PHP requires the override to stay compatible.
	 *
	 * @param string $output Output buffer.
	 * @param int    $depth  Depth.
	 * @param mixed  $args   Callback arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ): void {}

	/**
	 * Close a child level.
	 *
	 * @param string $output Output buffer.
	 * @param int    $depth  Depth.
	 * @param mixed  $args   Callback arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ): void {}

	/**
	 * Start an element.
	 *
	 * @param string $output  Output buffer.
	 * @param mixed  $element Element.
	 * @param int    $depth   Depth.
	 * @param mixed  $args    Callback arguments.
	 * @param int    $id      Current element id.
	 */
	public function start_el( &$output, $element, $depth = 0, $args = array(), $id = 0 ): void {}

	/**
	 * End an element.
	 *
	 * @param string $output  Output buffer.
	 * @param mixed  $element Element.
	 * @param int    $depth   Depth.
	 * @param mixed  $args    Callback arguments.
	 */
	public function end_el( &$output, $element, $depth = 0, $args = array() ): void {}
}

/**
 * `Walker_Nav_Menu` double.
 *
 * The theme's walker extends this class and overrides every method it needs, so the
 * double only has to exist and to be the class the parent chain resolves to.
 */
class Walker_Nav_Menu extends Walker {}
