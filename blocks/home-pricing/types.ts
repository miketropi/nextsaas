/**
 * Attributes of `nextsaas/home-pricing`.
 *
 * Repeater lists are `Array<Record<string, unknown>>` because that is what the shared
 * `Repeater` is typed over: TypeScript gives a plain interface no implicit index
 * signature, so a named item interface would need a cast at the call site.
 */
export interface HomePricingAttributes extends Record<string, unknown> {
	sectionId: string;
	eyebrow: string;
	/** Rich: the approved copy carries a line break and an emphasis. */
	heading: string;
	lead: string;
	/** Accessible name of the billing-period group. */
	billingLabel: string;
	monthlyLabel: string;
	yearlyLabel: string;
	offerNote: string;
	/** Live-region disclaimer while the monthly period is selected. */
	monthlyNotice: string;
	/** Live-region disclaimer while the yearly period is selected. */
	yearlyNotice: string;
	/** Note shown under a price while the yearly period is selected. */
	yearlyNote: string;
	/** Note shown under a price while the monthly period is selected. */
	monthlyNote: string;
	plans: Array<Record<string, unknown>>;
}
