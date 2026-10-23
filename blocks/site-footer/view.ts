/**
 * Front-end behaviour of `nextsaas/site-footer`.
 *
 * Two progressive enhancements, both of which leave the footer complete without
 * them:
 *
 * 1. The newsletter form. The parent theme has no subscription backend and the
 *    approved render's form is explicitly a preview, so the form is only
 *    intercepted while its `action` is empty: an editor who wires it to a real
 *    endpoint gets a native POST and this handler stands aside. Nothing is sent
 *    anywhere otherwise, and every status message is read from a `data-*`
 *    attribute the renderer printed, so this bundle carries no English literal.
 * 2. The notice dialog, which this block owns because it owns the triggers (the
 *    social buttons and the two legal buttons). It is a native `<dialog>`, so
 *    Escape, the top layer and the backdrop come from the browser; the only script
 *    responsibility is returning focus to the trigger that opened it.
 */

import { bootBlocks } from '../../resources/ts/runtime/blocks';
import { readString } from '../../resources/ts/runtime/strings';

/**
 * Wire one footer.
 *
 * @param footer Footer root element.
 * @return Teardown function.
 */
function initFooter(footer: HTMLElement): () => void {
	const form = footer.querySelector<HTMLFormElement>('form[data-nextsaas-newsletter="1"]');
	const dialog = footer.querySelector<HTMLDialogElement>('dialog[data-nextsaas-notice="1"]');
	/*
	 * The heading is resolved through the dialog's own `aria-labelledby`, so the two
	 * can never point at different elements.
	 */
	const labelledBy = dialog?.getAttribute('aria-labelledby') ?? '';
	const titleNode =
		dialog && '' !== labelledBy
			? dialog.querySelector<HTMLElement>(`#${CSS.escape(labelledBy)}`)
			: null;
	const bodyNode = dialog?.querySelector<HTMLElement>('[data-notice-body]');
	const closeButton = dialog?.querySelector<HTMLButtonElement>('.dialog-close');
	let opener: HTMLElement | null = null;

	const onSubmit = (event: SubmitEvent): void => {
		if (!form || '' !== form.getAttribute('action')) {
			return;
		}

		event.preventDefault();

		// Native validation first: the status must never claim success over a field
		// the browser already flagged.
		if (!form.reportValidity()) {
			return;
		}

		const status = form.querySelector<HTMLElement>('[role="status"]');

		if (status) {
			status.textContent = readString(form, 'success-text');
		}
	};

	const openNotice = (trigger: HTMLElement, heading: string, body: string): void => {
		if (!dialog || !titleNode || !bodyNode) {
			return;
		}

		opener = trigger;
		titleNode.textContent = heading;
		bodyNode.textContent = body;
		dialog.showModal();
		closeButton?.focus();
	};

	const onClick = (event: MouseEvent): void => {
		const target =
			event.target instanceof Element
				? event.target.closest<HTMLElement>('[data-social],[data-info],.dialog-close')
				: null;

		if (!target || !footer.contains(target)) {
			return;
		}

		if (target.matches('.dialog-close')) {
			dialog?.close();
			return;
		}

		const social = target.dataset.social;

		if ('undefined' !== typeof social && '' !== social) {
			openNotice(
				target,
				social + readString(dialog, 'social-title-suffix'),
				readString(dialog, 'social-body'),
			);
			return;
		}

		const privacy = 'privacy' === target.dataset.info;

		openNotice(
			target,
			readString(dialog, privacy ? 'privacy-title' : 'terms-title'),
			readString(dialog, 'legal-body'),
		);
	};

	const onClose = (): void => {
		opener?.focus();
		opener = null;
	};

	form?.addEventListener('submit', onSubmit);
	footer.addEventListener('click', onClick);
	dialog?.addEventListener('close', onClose);

	return () => {
		form?.removeEventListener('submit', onSubmit);
		footer.removeEventListener('click', onClick);
		dialog?.removeEventListener('close', onClose);
	};
}

bootBlocks<HTMLElement>('[data-nextsaas-section="site-footer"]', initFooter);

export {};
