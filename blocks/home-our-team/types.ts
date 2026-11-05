import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface HomeOurTeamAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	items: Array<Record<string, unknown>>;
	note: string;
}
