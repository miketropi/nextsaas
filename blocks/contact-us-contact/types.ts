/**
 * Attributes of `nextsaas/contact-us-contact`.
 *
 * The contact routes are `Array<Record<string, unknown>>` because that is what the
 * shared `Repeater` is typed over: TypeScript gives a plain interface no implicit index
 * signature, so a named item interface would need a cast at the call site.
 */
export interface ContactUsContactAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	/** Rich: the approved copy carries a line break and an emphasis. */
	heading: string;
	routes: Array<Record<string, unknown>>;
}
