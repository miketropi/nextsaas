export interface HomeOurTeamAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	items: Array<Record<string, unknown>>;
	note: string;
}
