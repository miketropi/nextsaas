<?php

/**
 * Server renderer for the Article content block.
 *
 * The body is the post's own content, rendered through `the_content()` exactly once
 * so block content, embeds, shortcodes and every content filter behave the way they
 * do everywhere else in WordPress. The rendered HTML is then walked once to build
 * the table of contents, which is what keeps the TOC and the article from ever
 * disagreeing about the heading list.
 *
 * @var array<string, mixed> $attributes Block attributes.
 *
 * @package NextSaaS
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nextsaas_post = nextsaas_queried_post();

/*
 * A single render pass. The content filters are applied here and the result is
 * reused for both the article and the heading scan; calling `the_content()` and then
 * scanning `$post->post_content` would show one document and index another.
 */
$nextsaas_content = null === $nextsaas_post ? '' : apply_filters( 'the_content', $nextsaas_post->post_content );

/*
 * Heading anchors are generated rather than read: the approved render's headings
 * carry ids, and a post written in the editor will not. The same slug is used for the
 * heading and for its table-of-contents entry, so the two can never drift.
 */
$nextsaas_headings = array();
$nextsaas_used     = array();

if ( '' !== $nextsaas_content ) {
	$nextsaas_content = (string) preg_replace_callback(
		'#<h([23])(\s[^>]*)?>(.*?)</h\1>#is',
		static function ( array $matches ) use ( &$nextsaas_headings, &$nextsaas_used ): string {
			$level = (int) $matches[1];
			$text  = trim( wp_strip_all_tags( $matches[3] ) );

			if ( '' === $text ) {
				return $matches[0];
			}

			$slug = sanitize_title( $text );
			$slug = '' === $slug ? 'section' : $slug;

			$suffix = 2;

			while ( in_array( $slug, $nextsaas_used, true ) ) {
				$slug = sanitize_title( $text ) . '-' . $suffix;
				$suffix++;
			}

			$nextsaas_used[]   = $slug;
			$nextsaas_headings[] = array(
				'level' => $level,
				'slug'  => $slug,
				'text'  => $text,
			);

			$attributes = (string) $matches[2];

			if ( ! preg_match( '/\sid=/', $attributes ) ) {
				$attributes .= ' id="' . esc_attr( $slug ) . '"';
			}

			return '<h' . $level . $attributes . '>' . $matches[3] . '</h' . $level . '>';
		},
		$nextsaas_content,
	);
}

// One entry is not a table of contents; the render only shows the aside with two.
$nextsaas_show_toc = count( $nextsaas_headings ) > 1;
?>
<section <?php
	echo nextsaas_section_attrs( 'post-article-content', $attributes, array( 'section', 'ruled' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
?>>
	<div class="container">
		<div class="article-layout">
			<?php if ( $nextsaas_show_toc ) : ?>
				<aside class="article-toc" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'tocAriaLabel' ) ); ?>">
					<p class="meta" data-nextsaas-rich="tocLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'tocLabel' ) ); ?></p>
					<?php foreach ( $nextsaas_headings as $nextsaas_heading ) : ?>
						<a href="#<?php echo esc_attr( $nextsaas_heading['slug'] ); ?>"><?php echo esc_html( $nextsaas_heading['text'] ); ?></a>
					<?php endforeach; ?>
				</aside>
			<?php endif; ?>
			<article class="article-body" id="article-body">
				<?php
				if ( '' === $nextsaas_content ) {
					// Editor preview, or a template rendered outside the loop: a deterministic
					// placeholder beats an empty article with a table of contents above it.
					?>
					<p><?php echo esc_html__( 'This story’s content will appear here on the front end.', 'nextsaas' ); ?></p>
					<?php
				} else {
					echo $nextsaas_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered content, printed as WordPress prints it.
				}
				?>
				<div class="page-actions">
					<a class="text-link" href="<?php echo esc_url( nextsaas_resolve_url( nextsaas_attr( $attributes, 'primaryUrl' ) ) ); ?>" data-nextsaas-rich="primaryLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'primaryLabel' ) ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
					<a class="text-link" href="<?php echo esc_url( nextsaas_resolve_url( nextsaas_attr( $attributes, 'secondaryUrl' ) ) ); ?>" data-nextsaas-rich="secondaryLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'secondaryLabel' ) ); ?> <?php echo nextsaas_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme helper.?></a>
				</div>
			</article>
		</div>
	</div>
</section>
