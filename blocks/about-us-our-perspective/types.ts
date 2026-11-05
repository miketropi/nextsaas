import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface AboutUsOurPerspectiveAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	bodyOne: string;
	bodyTwo: string;
}
