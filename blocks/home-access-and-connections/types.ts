export interface HomeAccessAndConnectionsAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	items: Array<Record<string, unknown>>;
}
