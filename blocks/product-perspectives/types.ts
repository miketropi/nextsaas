import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface Perspective extends Record<string, unknown> { heading: string; bodyOne: string; bodyTwo: string; }
export interface ProductPerspectivesAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string; eyebrow: string; heading: string; perspectives: Perspective[];
}
