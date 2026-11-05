import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface HomeFieldGuidesAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	items: Array<Record<string, unknown>>;
}
