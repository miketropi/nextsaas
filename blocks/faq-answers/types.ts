/**
 * Attributes of `nextsaas/faq-answers`.
 *
 * Repeater lists are `Array<Record<string, unknown>>` because that is what the shared
 * `Repeater` is typed over: TypeScript gives a plain interface no implicit index
 * signature, so a named item interface would need a cast at the call site.
 */
export interface FaqAnswersAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	filterLabel: string;
	/** Category filters, in source order; the first one means "all". */
	categories: Array<Record<string, unknown>>;
	noteLabel: string;
	noteLinkLabel: string;
	noteLinkUrl: string;
	searchFieldLabel: string;
	searchPlaceholder: string;
	searchSubmitLabel: string;
	/** Singular count template, `%s` replaced by the number. */
	countOne: string;
	/** Plural count template. */
	countMany: string;
	items: Array<Record<string, unknown>>;
	emptyHeading: string;
	emptyBody: string;
	resetLabel: string;
}
