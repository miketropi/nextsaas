/**
 * Attributes of `nextsaas/home-capabilities`.
 *
 * Every key mirrors `block.json`, so the editor's prop type and the stored content
 * can never disagree. Repeater lists are `Array<Record<string, unknown>>` because
 * that is what the shared `Repeater` is typed over: TypeScript gives a plain
 * interface no implicit index signature, so a named item interface would need a
 * cast at the call site.
 */
export interface HomeCapabilitiesAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	heading: string;
	lead: string;
	items: Array<Record<string, unknown>>;
	media: Array<Record<string, unknown>>;
}
