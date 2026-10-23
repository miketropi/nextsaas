export interface ControlItem extends Record<string, unknown> { heading: string; body: string; }
export interface ProductControlAttributes extends Record<string, unknown> {
	sectionId: string; eyebrow: string; heading: string; items: ControlItem[]; note: string;
}
