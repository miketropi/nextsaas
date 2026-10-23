/**
 * Editor for `nextsaas/home-faq`.
 *
 * The block is dynamic, so PHP owns the only authoritative front-end markup and the
 * canvas shows it through `BlockPreview`. The section's own copy is edited where it
 * appears; the question list is a repeater because each row is a bounded unit of
 * two fields, and its two fields are also editable in place through the dot-path
 * overlay targets `render.php` marks.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { HomeFaqAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
];

/** One question's two editable fields. */
const itemFields: RepeaterField[] = [
	{ key: 'heading', label: __('Question', 'nextsaas') },
	{ key: 'body', label: __('Answer', 'nextsaas'), control: 'textarea' },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<HomeFaqAttributes>): JSX.Element {
	const items = Array.isArray(attributes.items) ? attributes.items : [];

	/*
	 * Each question is a repeater row, so its overlay target is a dot path. The
	 * paths are derived from the list length rather than stored, which keeps them
	 * correct after an insert, a delete or a reorder.
	 */
	const itemTargets: RichTextTarget[] = items.flatMap((_item, index) => [
		{
			path: `items.${index}.heading`,
			label: `${__('Question', 'nextsaas')} ${index + 1}`,
		},
		{
			path: `items.${index}.body`,
			label: `${__('Answer', 'nextsaas')} ${index + 1}`,
			multiline: true,
		},
	]);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Section', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Section anchor', 'nextsaas')}
						help={__(
							'Used by in-page links such as the header menu. Leave empty for none.',
							'nextsaas'
						)}
						value={attributes.sectionId ?? ''}
						onChange={(sectionId) => setAttributes({ sectionId })}
					/>
				</PanelBody>

				<PanelBody title={__('Questions', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Questions', 'nextsaas')}
						items={items}
						fields={itemFields}
						onChange={(next) =>
							setAttributes({ items: next as HomeFaqAttributes['items'] })
						}
						createItem={() => ({ heading: '', body: '' })}
						getItemLabel={(item, index) =>
							'string' === typeof item.heading && '' !== item.heading
								? item.heading
								: `${__('Question', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/home-faq" attributes={attributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...itemTargets]}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomeFaqAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
