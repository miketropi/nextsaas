/**
 * Attributes of `nextsaas/home-team-solutions`.
 *
 * Every key mirrors `block.json`, so the editor's prop type and the stored content
 * can never disagree. Repeater lists are `Array<Record<string, unknown>>` because
 * that is what the shared `Repeater` is typed over: TypeScript gives a plain
 * interface no implicit index signature, so a named item interface would need a
 * cast at the call site.
 */
export interface HomeTeamSolutionsAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	tablistLabel: string;
	panels: Array<Record<string, unknown>>;
	ctaLabel: string;
	ctaUrl: string;
	mediaId: number;
	mediaUrl: string;
	mediaAlt: string;
	placeholderLabel: string;
	placeholderTop: string;
	placeholderBadge: string;
	placeholderTitle: string;
	placeholderNote: string;
	placeholderFoot: string;
	placeholderAspect: string;
}
