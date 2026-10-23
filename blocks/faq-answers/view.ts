/**
 * Front-end behaviour of `nextsaas/faq-answers`.
 *
 * The filter and the search only narrow a list the renderer already printed in full,
 * so the no-JavaScript state is every question, openable, with the correct count. The
 * bundle never hides content it cannot restore, never re-renders a question, and
 * reads every user-facing string from a `data-*` attribute — including the singular
 * and plural count templates, which is why the count is composed here rather than
 * printed by the renderer once and for all.
 */

import { bootBlocks } from '../../resources/ts/runtime/blocks';
import { formatTemplate, readString } from '../../resources/ts/runtime/strings';

/**
 * Wire one FAQ block.
 *
 * @param root Block root element.
 * @return Teardown function.
 */
function initFaq(root: HTMLElement): () => void {
	const search = root.querySelector<HTMLFormElement>('[data-nextsaas-faq-search]');
	const query = root.querySelector<HTMLInputElement>('[data-nextsaas-faq-query]');
	const count = root.querySelector<HTMLElement>('[data-nextsaas-faq-count]');
	const empty = root.querySelector<HTMLElement>('[data-nextsaas-faq-empty]');
	const items = Array.from(root.querySelectorAll<HTMLDetailsElement>('[data-nextsaas-faq-items] details'));
	const filters = Array.from(root.querySelectorAll<HTMLButtonElement>('[data-faq-category]'));
	const reset = root.querySelector<HTMLButtonElement>('[data-nextsaas-faq-reset]');

	if (items.length === 0) {
		return () => undefined;
	}

	/*
	 * The approved render's first filter is the unrestricted one ("All questions"),
	 * and it is a filter like any other: its value matches no question's category. It
	 * therefore has to be treated as "no restriction" by value, not by position, or
	 * every question would be hidden the moment the list is painted.
	 */
	const allCategory = filters.length > 0 ? filters[0].dataset.faqCategory ?? '' : '';
	let category = allCategory;

	/**
	 * Write the visible question count.
	 *
	 * @param matches Number of matching questions.
	 */
	const paintCount = (matches: number): void => {
		if (!count) {
			return;
		}

		const template =
			1 === matches
				? readString(count, 'faq-count-one')
				: readString(count, 'faq-count-many');

		count.textContent = formatTemplate(template, matches);
	};

	/** Apply the current category and search term to the list. */
	const apply = (): void => {
		const term = (query?.value ?? '').trim().toLocaleLowerCase();
		let shown = 0;

		items.forEach((item) => {
			const group = item.dataset.category ?? '';
			const inCategory = '' === category || category === allCategory || group === category;
			const matchesTerm = '' === term || item.textContent.toLocaleLowerCase().includes(term);
			const visible = inCategory && matchesTerm;

			item.hidden = !visible;

			if (visible) {
				shown++;
			}
		});

		paintCount(shown);

		if (empty) {
			empty.hidden = 0 !== shown;
		}
	};

	const onFilter = (event: Event): void => {
		const target = event.target;

		if (!(target instanceof Element)) {
			return;
		}

		const button = target.closest<HTMLButtonElement>('[data-faq-category]');

		if (!button) {
			return;
		}

		category = button.dataset.faqCategory ?? '';

		filters.forEach((other) => {
			other.setAttribute('aria-pressed', String(other === button));
		});

		apply();
	};

	const onSubmit = (event: Event): void => {
		// A search form inside a page would otherwise navigate away and lose the filter.
		event.preventDefault();
		apply();
	};

	const onQuery = (): void => apply();

	const onReset = (): void => {
		if (query) {
			query.value = '';
		}

		category = allCategory;

		filters.forEach((button, index) => {
			button.setAttribute('aria-pressed', String(0 === index));
		});

		apply();
	};

	root.addEventListener('click', onFilter);
	search?.addEventListener('submit', onSubmit);
	query?.addEventListener('input', onQuery);
	reset?.addEventListener('click', onReset);

	// The count is already correct in the initial markup; repainting on load keeps the
	// singular template honest when the block was saved with a single question.
	paintCount(items.length);

	return () => {
		root.removeEventListener('click', onFilter);
		search?.removeEventListener('submit', onSubmit);
		query?.removeEventListener('input', onQuery);
		reset?.removeEventListener('click', onReset);
	};
}

bootBlocks<HTMLElement>('[data-nextsaas-section="faq-answers"]', initFaq);

export {};
