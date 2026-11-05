import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface ControlItem extends Record<string, unknown> { heading: string; body: string; }
export interface ProductControlAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string; eyebrow: string; heading: string; items: ControlItem[]; note: string;
}
