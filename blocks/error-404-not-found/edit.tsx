/**
 * Editor for `nextsaas/error-404-not-found`.
 *
 * The block is dynamic, so the canvas shows the renderer's own markup through
 * `BlockPreview` and every string is edited where it appears. Only the two
 * destinations live in the sidebar: they are URLs, which have no place on the
 * canvas.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { Error404NotFoundAttributes } from './types';

/** Every value this block renders unconditionally. */
const targets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'errorNumber', label: __('Error number', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'primaryLabel', label: __('Primary link label', 'nextsaas') },
	{ path: 'secondaryLabel', label: __('Secondary link label', 'nextsaas') },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<Error404NotFoundAttributes>): JSX.Element {
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Destinations', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Primary link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.primaryUrl ?? ''}
						onChange={(primaryUrl) => setAttributes({ primaryUrl })}
					/>
					<TextControl
						label={__('Secondary link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.secondaryUrl ?? ''}
						onChange={(secondaryUrl) => setAttributes({ secondaryUrl })}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/error-404-not-found" attributes={attributes} />

			<RichTextOverlay
				targets={targets}
				attributes={attributes}
				setAttributes={(next) =>
					setAttributes(next as Partial<Error404NotFoundAttributes>)
				}
				isSelected={isSelected}
			/>
		</>
	);
}
