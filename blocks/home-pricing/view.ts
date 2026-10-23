/**
 * Front-end behaviour of `nextsaas/home-pricing`.
 *
 * The billing-period toggle only swaps values the renderer already printed into
 * `data-monthly`/`data-yearly` attributes, so the section is complete — and correct —
 * with JavaScript off: it shows the monthly offer, and every figure and note is real
 * text rather than an empty placeholder waiting for a script.
 *
 * No user-facing string is composed here. Everything the toggle writes was printed by
 * `render.php`, which is what keeps the bundle translatable without a second copy of
 * the pricing copy.
 */

import { bootBlocks } from '../../resources/ts/runtime/blocks';

/** Periods the toggle can select. */
type Period = 'monthly' | 'yearly';

/**
 * Read a period from an element, defaulting to monthly.
 *
 * @param value Candidate value.
 * @return A valid period.
 */
function toPeriod(value: string | undefined): Period {
	return 'yearly' === value ? 'yearly' : 'monthly';
}

/**
 * Wire one pricing block.
 *
 * @param root Block root element.
 * @return Teardown function.
 */
function initPricing(root: HTMLElement): () => void {
	const toggles = Array.from(root.querySelectorAll<HTMLButtonElement>('[data-billing]'));

	if (toggles.length === 0) {
		return () => undefined;
	}

	/**
	 * Write one period into the section.
	 *
	 * @param period Selected period.
	 */
	const paint = (period: Period): void => {
		toggles.forEach((toggle) => {
			toggle.setAttribute('aria-pressed', String(toPeriod(toggle.dataset.billing) === period));
		});

		root.querySelectorAll<HTMLElement>('[data-nextsaas-price]').forEach((price) => {
			const value = price.dataset[period];

			if ('string' === typeof value && '' !== value) {
				price.textContent = value;
			}
		});

		root.querySelectorAll<HTMLElement>('[data-nextsaas-price-note]').forEach((note) => {
			const value = note.dataset[period];

			if ('string' === typeof value && '' !== value) {
				note.textContent = value;
			}
		});

		// The polite live region announces the new period's disclaimer, which is the
		// only place the section says the rates are illustrative.
		root.querySelectorAll<HTMLElement>('[data-nextsaas-billing-note]').forEach((note) => {
			const value = note.dataset[period];

			if ('string' === typeof value && '' !== value) {
				note.textContent = value;
			}
		});
	};

	const onClick = (event: Event): void => {
		const target = event.target;

		if (!(target instanceof Element)) {
			return;
		}

		const toggle = target.closest<HTMLButtonElement>('[data-billing]');

		if (!toggle) {
			return;
		}

		paint(toPeriod(toggle.dataset.billing));
	};

	root.addEventListener('click', onClick);

	return () => root.removeEventListener('click', onClick);
}

bootBlocks<HTMLElement>('[data-nextsaas-section="home-pricing"]', initPricing);

export {};
