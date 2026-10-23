export interface FooterLink extends Record<string, unknown> {
	label: string;
	url: string;
	element: 'a' | 'button';
}

export interface FooterColumn extends Record<string, unknown> {
	heading: string;
	links: FooterLink[];
}

export interface SocialButton extends Record<string, unknown> {
	label: string;
	icon: string;
}

export interface BottomItem extends Record<string, unknown> {
	label: string;
	element: 'a' | 'button' | 'text';
	href: string;
}

export interface SiteFooterAttributes extends Record<string, unknown> {
	sectionId: string;
	newsletterHeading: string;
	newsletterLabel: string;
	newsletterPlaceholder: string;
	newsletterButtonLabel: string;
	newsletterNote: string;
	newsletterStatusText: string;
	newsletterSuccessText: string;
	newsletterAction: string;
	brandMode: 'text' | 'image';
	brandLabel: string;
	brandUrl: string;
	brandAriaLabel: string;
	brandMediaId: number;
	brandMediaUrl: string;
	brandMediaAlt: string;
	aboutLineOne: string;
	aboutLineTwo: string;
	columns: FooterColumn[];
	socialHeading: string;
	socialNote: string;
	socialAriaLabel: string;
	socialButtons: SocialButton[];
	wordmark: string;
	copyright: string;
	bottomItems: BottomItem[];
	dialogEyebrow: string;
	dialogCloseLabel: string;
	socialTitleSuffix: string;
	socialDialogBody: string;
	privacyDialogTitle: string;
	termsDialogTitle: string;
	legalDialogBody: string;
}
