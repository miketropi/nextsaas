/**
 * Navigation rows of the header's fallback menu.
 *
 * Both interfaces extend `Record<string, unknown>` on purpose: they are handed to
 * the shared `Repeater`, whose item type is an index-signature record. TypeScript
 * does not give a plain interface an implicit index signature, so without the
 * extension the array would need a cast at the call site.
 */
export interface HeaderNavChild extends Record<string, unknown> {
	label: string;
	url: string;
	description?: string;
}

export interface HeaderNavItem extends Record<string, unknown> {
	label: string;
	url: string;
	children?: HeaderNavChild[];
}

export interface SiteHeaderAttributes extends Record<string, unknown> {
	sticky: boolean;
	skipLinkLabel: string;
	skipLinkTarget: string;
	/** `text` keeps the designed wordmark; `image` swaps in a media-library logo. */
	brandMode: 'text' | 'image';
	brandLabel: string;
	brandUrl: string;
	brandAriaLabel: string;
	brandMediaId: number;
	brandMediaUrl: string;
	brandMediaAlt: string;
	navAriaLabel: string;
	menuToggleLabel: string;
	/**
	 * The designed navigation, used while no classic menu is assigned to the
	 * `primary` location. Once a menu is assigned WordPress's own menu wins, so
	 * these rows only feed the rendered header of a site with no menu at all.
	 */
	navItems: HeaderNavItem[];
	ctaLabel: string;
	ctaUrl: string;
	ctaVariant: 'primary' | 'secondary' | 'ghost';
	ctaArrow: boolean;
}
