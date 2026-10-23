export interface PageHeroAttributes extends Record<string, unknown> {
	sectionId: string;
	breadcrumbHomeLabel: string;
	breadcrumbCurrentLabel: string;
	eyebrow: string;
	/** Rich value: the approved heading includes `<br>` and `<em>`. */
	heading: string;
	lead: string;
}
