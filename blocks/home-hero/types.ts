/**
 * Attributes of `nextsaas/home-hero`.
 *
 * Every key mirrors `block.json`, so the editor's prop type and the stored content
 * can never disagree. Repeater lists are `Array<Record<string, unknown>>` because
 * that is what the shared `Repeater` is typed over: TypeScript gives a plain
 * interface no implicit index signature, so a named item interface would need a
 * cast at the call site.
 */
export interface HomeHeroAttributes extends Record<string, unknown> {
	sectionId: string;
	kicker: string;
	kickerSecondary: string;
	heading: string;
	orbitCopy: string;
	lead: string;
	primaryLabel: string;
	primaryUrl: string;
	workspaceLabel: string;
	workspaceUrl: string;
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
	caption: string;
	captionNote: string;
	useCasesLabel: string;
	useCases: Array<Record<string, unknown>>;
}
