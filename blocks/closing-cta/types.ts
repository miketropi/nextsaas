export interface ClosingCtaAttributes extends Record<string, unknown> {
	/** Design anchor. The approved render uses `next-step` on three of the four pages. */
	sectionId: string;
	eyebrow: string;
	/** Rich value: `<br>` and `<em>` are part of the approved copy. */
	heading: string;
	lead: string;
	ctaLabel: string;
	ctaUrl: string;
	/** Button style. A literal union because `SelectControl` types its value. */
	ctaVariant: 'primary' | 'secondary' | 'ghost';
	/** The approved render's `btn-arrow` class, whose arrow is suppressed by CSS. */
	ctaArrow: boolean;
	/** Whether the inline arrow-up-right icon is printed inside the call to action. */
	ctaIcon: boolean;
}
