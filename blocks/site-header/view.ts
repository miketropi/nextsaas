/**
 * Front-end behaviour of `nextsaas/site-header`.
 *
 * The theme owns ONE compact navigation surface. At and below 1023px the header's
 * `<nav>` becomes the drawer: the toggle expands it, an overlay dims the page, the
 * body scrolls no further, focus is trapped inside the nav, and Escape or an
 * outside click closes it and returns focus to the toggle. Above 1023px the same
 * nav is the desktop bar, where the submenus need no script at all — the stylesheet
 * reveals them on `:hover` and `:focus-within`, which also covers keyboard users.
 *
 * 1024px is a deliberate choice rather than the render's own 920px/680px bands: the
 * optional mega-navigation plugin treats `>=1024px` as desktop and everything below
 * it as a host-owned compact region, and the header block prints that plugin's
 * `data-beplus-vmn-compact-mode="accordion"` opt-in. One breakpoint for both means
 * the plugin can never open a second drawer over the theme's own.
 *
 * Every path here is additive. `render.php` prints the panels and the triggers with
 * their ARIA state already correct, and the compact menu is visible by default in
 * CSS unless `html.nextsaas-js` says a script is running, so a visitor without
 * JavaScript keeps a complete, focusable navigation.
 */

const DESKTOP_QUERY = '(min-width: 1024px)';

/** Focusable descendants of the drawer, in tab order. */
const FOCUSABLE =
	'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

/** Elements the header owns. */
interface HeaderParts {
	nav: HTMLElement;
	toggle: HTMLButtonElement;
	overlay: HTMLElement;
}

/**
 * Focal elements of the header, or null when the markup is not the theme's.
 *
 * @param header Header element.
 * @return The parts, or null when any is missing.
 */
function readParts(header: HTMLElement): HeaderParts | null {
	const nav = header.querySelector<HTMLElement>('.nextsaas-nav');
	const toggle = header.querySelector<HTMLButtonElement>('.menu-toggle');
	const overlay = header.querySelector<HTMLElement>('[data-nextsaas-nav-overlay]');

	if (!nav || !toggle || !overlay) {
		return null;
	}

	return { nav, toggle, overlay };
}

/**
 * Read a submenu panel a trigger controls.
 *
 * A mega-navigation panel carries its own id and is owned by the plugin, so a
 * trigger pointing at one is left alone.
 *
 * @param trigger Trigger element.
 * @return The panel this block owns, or null.
 */
function readPanel(trigger: HTMLElement): HTMLElement | null {
	const panel = document.getElementById(trigger.getAttribute('aria-controls') ?? '');

	if (!panel || panel.classList.contains('beplus-vmn-mega-panel')) {
		return null;
	}

	return panel;
}

/**
 * Wire one header.
 *
 * @param header Header element.
 * @return Teardown function.
 */
function initHeader(header: HTMLElement): () => void {
	const parts = readParts(header);

	if (!parts) {
		return () => undefined;
	}

	const { nav, toggle, overlay } = parts;
	const desktop = window.matchMedia(DESKTOP_QUERY);
	const triggers = Array.from(
		nav.querySelectorAll<HTMLElement>('.nav-trigger[aria-controls], .nav-item > a[aria-controls]'),
	);
	let lockedScroll = 0;

	const focusables = (): HTMLElement[] =>
		Array.from(nav.querySelectorAll<HTMLElement>(FOCUSABLE)).filter(
			(element) => !element.hasAttribute('hidden') && element.offsetParent !== null,
		);

	const isOpen = (): boolean => 'true' === toggle.getAttribute('aria-expanded');

	const closeSubmenus = (): void => {
		triggers.forEach((trigger) => {
			trigger.setAttribute('aria-expanded', 'false');

			const panel = readPanel(trigger);

			if (panel) {
				panel.hidden = true;
			}
		});
	};

	const setSubmenu = (trigger: HTMLElement, open: boolean): void => {
		if (open) {
			closeSubmenus();
		}

		trigger.setAttribute('aria-expanded', String(open));

		const panel = readPanel(trigger);

		if (panel) {
			panel.hidden = !open;
		}
	};

	const lockScroll = (): void => {
		lockedScroll = window.scrollY;
		document.body.style.position = 'fixed';
		document.body.style.top = `-${lockedScroll}px`;
		document.body.style.width = '100%';
	};

	const unlockScroll = (): void => {
		if (!document.body.style.position) {
			return;
		}

		document.body.style.position = '';
		document.body.style.top = '';
		document.body.style.width = '';
		window.scrollTo({ top: lockedScroll, behavior: 'auto' });
	};

	const close = (): void => {
		if (!isOpen()) {
			return;
		}

		toggle.setAttribute('aria-expanded', 'false');
		nav.classList.remove('is-open');
		overlay.hidden = true;
		closeSubmenus();
		unlockScroll();
	};

	const open = (): void => {
		toggle.setAttribute('aria-expanded', 'true');
		nav.classList.add('is-open');
		overlay.hidden = false;
		lockScroll();
	};

	const onToggle = (): void => {
		if (isOpen()) {
			close();
			toggle.focus({ preventScroll: true });
			return;
		}

		open();
	};

	const onOverlay = (): void => close();

	const onKeydown = (event: KeyboardEvent): void => {
		if ('Escape' !== event.key) {
			if ('Tab' === event.key && isOpen()) {
				const items = focusables();

				if (0 === items.length) {
					return;
				}

				const first = items[0];
				const last = items[items.length - 1];

				if (event.shiftKey && (document.activeElement === first || document.activeElement === toggle)) {
					event.preventDefault();
					last.focus();
					return;
				}

				if (!event.shiftKey && document.activeElement === last) {
					event.preventDefault();
					first.focus();
				}
			}

			return;
		}

		if (isOpen()) {
			event.preventDefault();
			close();
			toggle.focus({ preventScroll: true });
			return;
		}

		closeSubmenus();

		/*
		 * At desktop widths the panel is revealed by `:focus-within`, so the way to
		 * close it from the keyboard is to release focus from the item that holds it.
		 */
		const active = document.activeElement;

		if (active instanceof HTMLElement && nav.contains(active)) {
			active.blur();
		}
	};

	const onDocumentClick = (event: MouseEvent): void => {
		if (!isOpen()) {
			return;
		}

		const target = event.target;

		if (!(target instanceof Node) || header.contains(target)) {
			return;
		}

		close();
	};

	/*
	 * The breakpoint can move while the drawer is open — a rotation, a window
	 * resize, a zoom change. Every path that leaves the compact range has to drop
	 * the drawer state and the inline body styles, or the page would scroll-lock
	 * itself at desktop width.
	 */
	const onBreakpoint = (): void => {
		if (!desktop.matches) {
			return;
		}

		toggle.setAttribute('aria-expanded', 'false');
		nav.classList.remove('is-open');
		overlay.hidden = true;
		closeSubmenus();
		unlockScroll();
	};

	toggle.addEventListener('click', onToggle);
	overlay.addEventListener('click', onOverlay);
	document.addEventListener('keydown', onKeydown);
	document.addEventListener('click', onDocumentClick);
	desktop.addEventListener('change', onBreakpoint);

	const disposal = triggers.map((trigger) => {
		const onClick = (event: MouseEvent): void => {
			/*
			 * Desktop submenus are CSS-driven, and a trigger that also addresses a real
			 * page must keep navigating. Only the compact accordion consumes the click.
			 */
			if (desktop.matches || !readPanel(trigger)) {
				return;
			}

			event.preventDefault();
			setSubmenu(trigger, 'false' === trigger.getAttribute('aria-expanded'));
		};

		trigger.addEventListener('click', onClick);

		return () => trigger.removeEventListener('click', onClick);
	});

	return () => {
		toggle.removeEventListener('click', onToggle);
		overlay.removeEventListener('click', onOverlay);
		document.removeEventListener('keydown', onKeydown);
		document.removeEventListener('click', onDocumentClick);
		desktop.removeEventListener('change', onBreakpoint);
		disposal.forEach((off) => off());
		unlockScroll();
	};
}

/**
 * Boot every header on the page.
 */
function boot(): void {
	document.querySelectorAll<HTMLElement>('.nextsaas-site-header').forEach((header) => {
		if (header.dataset.nextsaasHeaderReady) {
			return;
		}

		header.dataset.nextsaasHeaderReady = 'true';
		initHeader(header);
	});
}

if ('loading' === document.readyState) {
	document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
	boot();
}

export { initHeader };
