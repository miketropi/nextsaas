export interface HomeTestimonialAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	note: string;
	carouselLabel: string;
	items: Array<Record<string, unknown>>;
}
