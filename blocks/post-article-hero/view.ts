/**
 * Front-end behaviour of `nextsaas/post-article-hero`.
 *
 * One control: "copy article link". The clipboard API is only available on a secure
 * context and needs a user gesture, which a click provides, but it can still be denied
 * — so the failure path prints the address instead of leaving the reader with a button
 * that appears to have done nothing.
 *
 * Both messages come from the localised `nextsaasNav` object rather than a `data-`
 * attribute, because the string is produced by the bundle rather than by the renderer:
 * the renderer prints only the URL it copied from.
 */

import { bootBlocks } from '../../resources/ts/runtime/blocks';

/**
 * Wire one article hero's copy control.
 *
 * @param root Block root element.
 * @return Teardown function.
 */
function initArticleHero(root: HTMLElement): () => void {
	const button = root.querySelector<HTMLButtonElement>('[data-nextsaas-copy]');

	if (!button) {
		return () => undefined;
	}

	const status = root.querySelector<HTMLElement>('[data-nextsaas-copy-status]');
	const copied = window.nextsaasNav?.copied ?? '';
	const prompt = window.nextsaasNav?.copyPrompt ?? '';

	const onClick = async (): Promise<void> => {
		const url = button.dataset.articleUrl ?? window.location.href;

		try {
			await navigator.clipboard.writeText(url);

			if (status) {
				status.textContent = copied;
			}
		} catch {
			// A denied clipboard is not an error worth announcing: the reader still
			// wants the address, so it is printed for them to copy by hand.
			if (status) {
				status.textContent = `${prompt} ${url}`;
			}
		}
	};

	button.addEventListener('click', onClick);

	return () => button.removeEventListener('click', onClick);
}

bootBlocks<HTMLElement>('[data-nextsaas-section="post-article-hero"]', initArticleHero);

export {};
