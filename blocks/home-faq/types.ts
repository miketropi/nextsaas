/**
 * Attributes of `nextsaas/home-faq`.
 *
 * The item list is `Array<Record<string, unknown>>` because that is what the shared
 * `Repeater` is typed over: TypeScript gives a plain interface no implicit index
 * signature, so a named item interface would need a cast at the call site.
 */
export interface HomeFaqAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	/** Rich: the approved copy carries a line break and an emphasis. */
	heading: string;
	items: Array<Record<string, unknown>>;
}
