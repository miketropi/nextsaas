/**
 * Additive motion for NextSaaS. Content remains visible without JavaScript or
 * IntersectionObserver, and reduced-motion changes immediately reveal everything.
 */

const REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';
const SECTION_SELECTOR = '.nextsaas-scope main .section > .container, main.nextsaas-scope .section > .container';
const WORD_SELECTOR = '.nextsaas-scope [data-nextsaas-word-reveal]';

const clamp = (value: number): number => Math.max(0, Math.min(1, value));

function bootSectionReveals(preference: MediaQueryList): () => void {
	const sections = Array.from(document.querySelectorAll<HTMLElement>(SECTION_SELECTOR));
	let observer: IntersectionObserver | undefined;

	const showAll = (): void => {
		observer?.disconnect();
		sections.forEach((section) => section.classList.remove('pending'));
	};

	if (preference.matches || !('IntersectionObserver' in window)) {
		showAll();
		return showAll;
	}

	sections.forEach((section) => section.classList.add('reveal', 'pending'));
	observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				(entry.target as HTMLElement).classList.remove('pending');
				observer?.unobserve(entry.target);
			});
		},
		{ threshold: 0.08 },
	);
	sections.forEach((section) => observer?.observe(section));
	return showAll;
}

function bootWordReveal(preference: MediaQueryList): () => void {
	const found = document.querySelector<HTMLElement>(WORD_SELECTOR);
	if (!found || typeof found.animate !== 'function') return () => undefined;

	// Bound to a const so the narrowing survives into the closures below.
	const heading: HTMLElement = found;

	const nodes: ChildNode[] = [];
	const walker = document.createTreeWalker(heading, NodeFilter.SHOW_TEXT);
	while (walker.nextNode()) nodes.push(walker.currentNode as ChildNode);

	const words: HTMLElement[] = [];
	nodes.forEach((node) => {
		const fragment = document.createDocumentFragment();
		(node.textContent ?? '').split(/(\s+)/).forEach((part) => {
			if (!part || /^\s+$/.test(part)) {
				fragment.appendChild(document.createTextNode(part));
				return;
			}
			const word = document.createElement('span');
			word.style.color = 'inherit';
			word.textContent = part;
			fragment.appendChild(word);
			words.push(word);
		});
		node.replaceWith(fragment);
	});

	const animations = words.map((word) => {
		const animation = word.animate([{ opacity: 0.4 }, { opacity: 1 }], {
			duration: 1000,
			fill: 'both',
			easing: 'linear',
		});
		animation.pause();
		return animation;
	});
	let frame = 0;
	let nearby = true;
	let observer: IntersectionObserver | undefined;

	const detach = (): void => {
		window.removeEventListener('scroll', schedule);
		window.removeEventListener('resize', schedule);
		window.removeEventListener('load', schedule);
		observer?.disconnect();
		if (frame) cancelAnimationFrame(frame);
		frame = 0;
	};
	const finish = (): void => {
		animations.forEach((animation) => {
			animation.currentTime = 1000;
		});
		detach();
	};
	function paint(): void {
		frame = 0;
		if (preference.matches) {
			finish();
			return;
		}
		const rect = heading.getBoundingClientRect();
		const progress = clamp(
			(window.innerHeight * 0.88 - rect.top) /
				Math.max(1, window.innerHeight * 0.55 + rect.height * 0.3),
		);
		animations.forEach((animation, index) => {
			const start = (index / Math.max(1, animations.length - 1)) * 0.76;
			animation.currentTime = clamp((progress - start) / 0.24) * 1000;
		});
		if (progress >= 1) finish();
	}
	function schedule(): void {
		if (!frame && nearby) frame = requestAnimationFrame(paint);
	}

	if ('IntersectionObserver' in window) {
		observer = new IntersectionObserver(
			(entries) => {
				nearby = entries[0]?.isIntersecting ?? false;
				if (nearby) schedule();
			},
			{ rootMargin: '100px 0px' },
		);
		observer.observe(heading);
	}
	window.addEventListener('scroll', schedule, { passive: true });
	window.addEventListener('resize', schedule, { passive: true });
	window.addEventListener('load', schedule, { once: true });
	paint();
	return finish;
}

export function bootMotion(): void {
	const preference = window.matchMedia(REDUCED_MOTION_QUERY);
	const finishSections = bootSectionReveals(preference);
	const finishWords = bootWordReveal(preference);
	preference.addEventListener('change', () => {
		if (preference.matches) {
			finishSections();
			finishWords();
		}
	});
}
