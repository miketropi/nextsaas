import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface HomeTestimonialAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string;
	eyebrow: string;
	note: string;
	carouselLabel: string;
	items: Array<Record<string, unknown>>;
}
