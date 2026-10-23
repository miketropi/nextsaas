/**
 * Editor for `nextsaas/home-workflow`.
 *
 * The block is dynamic, so PHP owns the only authoritative front-end markup and the
 * canvas shows it through `BlockPreview`. Section copy is edited where it appears;
 * the repeatable list is a repeater in the sidebar and its marked fields are also
 * editable in place through dot-path overlay targets.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import MediaControl from '../../resources/ts/editor/media-control';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { HomeWorkflowAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'photoLabel', label: __('Photo label', 'nextsaas'), multiline: true },
	{ path: 'photoCredit', label: __('Photo credit', 'nextsaas') },
];

/** Fields of one step row. */
const itemFields: RepeaterField[] = [
	{ key: 'meta', label: __('Number', 'nextsaas') },
	{ key: 'heading', label: __('Heading', 'nextsaas') },
	{ key: 'body', label: __('Body', 'nextsaas'), control: 'textarea' },
];

/** Step fields `render.php` marks for in-place editing. */
const itemRichKeys: Array<{ key: string; label: string; multiline: boolean }> = [
	{ key: 'meta', label: __('Number', 'nextsaas'), multiline: false },
	{ key: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ key: 'body', label: __('Body', 'nextsaas'), multiline: true },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<HomeWorkflowAttributes>): JSX.Element {
	const items = Array.isArray(attributes.steps) ? attributes.steps : [];

	/*
	 * Row overlay targets are dot paths derived from the list length, so they stay
	 * correct after an insert, a delete or a reorder and always match the
	 * `data-nextsaas-rich` markers the renderer prints.
	 */
	const itemTargets: RichTextTarget[] = items.flatMap((_item, index) =>
		itemRichKeys.map((field) => ({
			path: `steps.${index}.${field.key}`,
			label: `${field.label} ${index + 1}`,
			multiline: field.multiline,
		})),
	);

	/*
	 * The reserved screenshot. Its keys are flat attributes rather than a nested row,
	 * because the same block also carries the labelled-placeholder strings the render
	 * falls back to whenever no attachment has been chosen.
	 */
	const patchMedia = (patch: Record<string, unknown>): void => {
		setAttributes(patch);
	};

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

				<PanelBody title={__('Steps', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Steps', 'nextsaas')}
						items={items}
						fields={itemFields}
						onChange={(next) => setAttributes({ steps: next })}
						createItem={() => ({})}
						getItemLabel={(item, index) =>
							'string' === typeof item.heading && '' !== item.heading
								? item.heading
								: `${__('Step', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>

				<PanelBody title={__('Media', 'nextsaas')} initialOpen={false}>
					<MediaControl
						value={{
							id: attributes.mediaId ?? 0,
							url: attributes.mediaUrl ?? '',
							alt: attributes.mediaAlt ?? '',
						}}
						labels={{
							title: __('Screenshot', 'nextsaas'),
							select: __('Select screenshot', 'nextsaas'),
							replace: __('Replace screenshot', 'nextsaas'),
							remove: __('Remove screenshot', 'nextsaas'),
							alt: __('Alternative text', 'nextsaas'),
						}}
						onSelect={(selected) =>
							patchMedia({
								mediaId: selected.id,
								mediaUrl: selected.url,
								mediaAlt: selected.alt,
							})
						}
						onRemove={() => patchMedia({ mediaId: 0, mediaUrl: '', mediaAlt: '' })}
						onChangeAlt={(mediaAlt) => patchMedia({ mediaAlt })}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/home-workflow" attributes={attributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...itemTargets]}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomeWorkflowAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
