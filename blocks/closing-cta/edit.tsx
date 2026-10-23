/**
 * Editor for `nextsaas/closing-cta`.
 *
 * The block is dynamic — `block.json` points at `render.php` and `save()` returns
 * null — so PHP owns the only authoritative copy of the front-end markup and the
 * canvas shows that exact markup through `BlockPreview`. Every editable string is
 * edited where it appears on the canvas by `RichTextOverlay`; the two fields that
 * are not text (the link URL and the two presentation switches) live in the
 * sidebar.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { ClosingCtaAttributes } from './types';

/** Rich values edited in place on the canvas (they render unconditionally). */
const overlayTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'ctaLabel', label: __('Link label', 'nextsaas') },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<ClosingCtaAttributes>): JSX.Element {
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Link', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.ctaUrl ?? ''}
						onChange={(ctaUrl) => setAttributes({ ctaUrl })}
					/>
				</PanelBody>
				<PanelBody title={__('Presentation', 'nextsaas')} initialOpen={false}>
					<SelectControl
						label={__('Button style', 'nextsaas')}
						value={attributes.ctaVariant ?? 'primary'}
						options={[
							{ label: __('Primary', 'nextsaas'), value: 'primary' },
							{ label: __('Secondary', 'nextsaas'), value: 'secondary' },
							{ label: __('Ghost', 'nextsaas'), value: 'ghost' },
						]}
						onChange={(ctaVariant) => setAttributes({ ctaVariant })}
					/>
					<ToggleControl
						label={__('Arrow icon', 'nextsaas')}
						checked={attributes.ctaIcon ?? true}
						onChange={(ctaIcon) => setAttributes({ ctaIcon })}
					/>
				</PanelBody>
			</InspectorControls>
			<BlockPreview block="nextsaas/closing-cta" attributes={attributes} />
			<RichTextOverlay
				targets={overlayTargets}
				attributes={attributes}
				setAttributes={(next) =>
					setAttributes(next as Partial<ClosingCtaAttributes>)
				}
				isSelected={isSelected}
			/>
		</>
	);
}
