import type { ReactElement } from 'react';

declare global {
	interface Window {
		wp: Record<string, unknown>;

		/**
		 * Strings localised onto `nextsaas-main` by
		 * `inc/assets/enqueue.php`. Optional because a view bundle can be served
		 * while the enqueue never ran — for example from a cached page or when a
		 * plugin strips the footer script.
		 */
		nextsaasNav?: {
			openMenu: string;
			closeMenu: string;
			openSubmenu: string;
			closeSubmenu: string;
			copied: string;
			copyPrompt: string;
		};

		/**
		 * Editor-only context localised onto the header block's editor script by
		 * `blocks/site-header/register-editor.php`. Optional for the same reason as
		 * `nextsaasNav`: the block script can load without its own localisation.
		 */
		nextsaasHeader?: {
			hasMenu: boolean;
		};
	}

	namespace JSX {
		type Element = ReactElement;
		interface IntrinsicElements {
			[name: string]: Record<string, unknown>;
		}
	}
}

export {};
