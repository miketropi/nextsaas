import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface AboutUsBuiltWithIntentionAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string;
	eyebrow: string;
	heading: string;
	bodyOne: string;
	bodyTwo: string;
	primaryLabel: string;
	primaryUrl: string;
	secondaryLabel: string;
	secondaryUrl: string;
}
