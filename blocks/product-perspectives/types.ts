export interface Perspective extends Record<string, unknown> { heading: string; bodyOne: string; bodyTwo: string; }
export interface ProductPerspectivesAttributes extends Record<string, unknown> {
	sectionId: string; eyebrow: string; heading: string; perspectives: Perspective[];
}
