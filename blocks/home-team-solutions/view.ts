import { bootBlocks } from '../../resources/ts/runtime/blocks';

bootBlocks('[data-nextsaas-section="home-team-solutions"]', (root) => {
	const tabs = Array.from(root.querySelectorAll<HTMLButtonElement>('[role="tab"]'));
	const panels = Array.from(root.querySelectorAll<HTMLElement>('[role="tabpanel"]'));
	const select = (index: number): void => {
		tabs.forEach((tab, current) => { const active = current === index; tab.setAttribute('aria-selected', String(active)); tab.tabIndex = active ? 0 : -1; });
		panels.forEach((panel, current) => { panel.hidden = current !== index; });
	};
	tabs.forEach((tab, index) => {
		tab.addEventListener('click', () => select(index));
		tab.addEventListener('keydown', (event) => {
			let target = index;
			if ('ArrowRight' === event.key) target = (index + 1) % tabs.length;
			else if ('ArrowLeft' === event.key) target = (index - 1 + tabs.length) % tabs.length;
			else if ('Home' === event.key) target = 0;
			else if ('End' === event.key) target = tabs.length - 1;
			else return;
			event.preventDefault(); select(target); tabs[target]?.focus();
		});
	});
});
