import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface PageHeroAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string;
	breadcrumbHomeLabel: string;
	breadcrumbCurrentLabel: string;
	eyebrow: string;
	/** Rich value: the approved heading includes `<br>` and `<em>`. */
	heading: string;
	lead: string;
}
