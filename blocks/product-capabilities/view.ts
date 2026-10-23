/**
 * Front-end behaviour of `nextsaas/product-capabilities`.
 *
 * The tab pattern is rendered completely in PHP, so this bundle only moves the
 * selection. It implements the WAI-ARIA tabs keyboard contract — Arrow keys wrap,
 * Home and End jump to the ends, the keypress moves focus with the selection — and
 * it changes nothing that a visitor without JavaScript would miss, because the
 * first panel is already the visible one.
 */

/** Selector for one tab, in document order. */
const TAB_SELECTOR = '[data-nextsaas-tab]';

/**
 * Wire one tab list.
 *
 * @param list Tab list element.
 * @return Teardown function.
 */
function initTabs(list: HTMLElement): () => void {
	const tabs = Array.from(list.querySelectorAll<HTMLButtonElement>(TAB_SELECTOR));

	if (tabs.length < 2) {
		return () => undefined;
	}

	const select = (index: number, moveFocus: boolean): void => {
		const target = (index + tabs.length) % tabs.length;

		tabs.forEach((tab, position) => {
			const selected = position === target;

			tab.setAttribute('aria-selected', String(selected));
			tab.tabIndex = selected ? 0 : -1;

			const panel = document.getElementById(tab.getAttribute('aria-controls') ?? '');

			if (panel) {
				panel.hidden = !selected;
			}
		});

		if (moveFocus) {
			tabs[target].focus();
		}
	};

	const onClick = (event: Event): void => {
		const target = event.target;

		if (!(target instanceof Element)) {
			return;
		}

		const tab = target.closest<HTMLButtonElement>(TAB_SELECTOR);

		if (tab) {
			select(tabs.indexOf(tab), false);
		}
	};

	const onKeydown = (event: KeyboardEvent): void => {
		const target = event.target;

		if (!(target instanceof HTMLElement) || !target.matches(TAB_SELECTOR)) {
			return;
		}

		const current = tabs.indexOf(target as HTMLButtonElement);

		switch (event.key) {
			case 'ArrowRight':
				select(current + 1, true);
				break;
			case 'ArrowLeft':
				select(current - 1, true);
				break;
			case 'Home':
				select(0, true);
				break;
			case 'End':
				select(tabs.length - 1, true);
				break;
			default:
				return;
		}

		event.preventDefault();
	};

	list.addEventListener('click', onClick);
	list.addEventListener('keydown', onKeydown);

	return () => {
		list.removeEventListener('click', onClick);
		list.removeEventListener('keydown', onKeydown);
	};
}

/**
 * Boot every tab list on the page.
 */
function boot(): void {
	document.querySelectorAll<HTMLElement>('.product-tabs[role="tablist"]').forEach((list) => {
		if (list.dataset.nextsaasTabsReady) {
			return;
		}

		list.dataset.nextsaasTabsReady = 'true';
		initTabs(list);
	});
}

if ('loading' === document.readyState) {
	document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
	boot();
}

export { initTabs };
