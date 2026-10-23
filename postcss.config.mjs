/**
 * PostCSS configuration.
 *
 * Tailwind v4 produces the `theme` layer the modules sit on top of. The local
 * plugin below fixes one thing the stylesheet cannot express on its own.
 */

import tailwindcss from '@tailwindcss/postcss';

/**
 * The design's section-root class names.
 *
 * Every section of the approved render puts one of these on the `<section>` element
 * itself — `section ruled`, `inner-hero`, `error-hero`, `topnav`, `pagefoot` — and
 * every NextSaaS block root carries the scope class *and* those classes on the same
 * element. A plain descendant prefix (`.nextsaas-scope .section`) therefore never
 * matches the root it was written to style: it only matches a `.section` nested
 * inside another `.nextsaas-scope`, which no renderer produces.
 *
 * The list is the union of the manifest's `sourceClass` values plus the two chrome
 * roots, so it is a closed set: a new section class must be added here as well.
 */
const ROOT_CLASSES = new Set( [
	'automation',
	'blog-carousel',
	'closing',
	'error-hero',
	'faq',
	'guides',
	'hero',
	'impact',
	'inner-hero',
	'intro',
	'pagefoot',
	'people',
	'pricing',
	'ruled',
	'section',
	'system',
	'testimonial',
	'topnav',
	'work-section',
] );

/**
 * Also match a root-subject selector when the scope sits on the root itself.
 *
 * `.nextsaas-scope .hero h1` cannot style the hero heading, because the hero element
 * *is* the scoped element. The transform adds `.nextsaas-scope.hero h1` beside it,
 * which has the same specificity (one class versus one class plus one class on the
 * same compound is `0,2,0` either way) and therefore does not reorder the cascade.
 *
 * Both forms are kept: the descendant form still covers a section class reused on a
 * nested element, which the composed patterns allow.
 *
 * @return {import('postcss').Plugin} PostCSS plugin.
 */
function scopeRoots() {
	return {
		postcssPlugin: 'nextsaas-scope-roots',
		Rule( rule ) {
			if ( ! rule.selector.includes( '.nextsaas-scope ' ) ) {
				return;
			}

			const selectors = rule.selector.split( ',' ).map( ( selector ) => selector.trim() );
			const variants = [];

			selectors.forEach( ( selector ) => {
				variants.push( selector );

				// Every occurrence is considered, so a rule with a leading ancestor such
				// as `html.nextsaas-js .nextsaas-scope .topnav nav` is covered too.
				const rewritten = selector.replace(
					/\.nextsaas-scope \.([a-z0-9-]+)/g,
					( whole, name ) => ( ROOT_CLASSES.has( name ) ? `.nextsaas-scope.${ name }` : whole )
				);

				if ( rewritten !== selector ) {
					variants.push( rewritten );
				}

				/*
				 * `inner-page` is a *body* class the theme adds (inc/template/body-class.php),
				 * so it is always an ancestor of the scope and never inside it. Prefixed as a
				 * descendant it can never match, and the whole inner-page composition — the
				 * closing heading's size, the section rhythm, the heading measure — would be
				 * silently lost. The scope is dropped for these.
				 */
				const inner = selector.replace( /\.nextsaas-scope (\.inner-page)/g, '$1' );

				if ( inner !== selector ) {
					variants.push( inner );
				}
			} );

			rule.selector = Array.from( new Set( variants ) ).join( ', ' );
		},
	};
}

scopeRoots.postcss = true;

export default {
	plugins: [ tailwindcss(), scopeRoots() ],
};
