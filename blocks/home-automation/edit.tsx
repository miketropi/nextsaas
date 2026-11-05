/**
 * Editor for `nextsaas/home-automation`.
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
import ColourControls from '../../resources/ts/editor/colour-controls';
import SpacingControls from '../../resources/ts/editor/spacing-controls';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import MediaControl from '../../resources/ts/editor/media-control';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { HomeAutomationAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'note', label: __('Note', 'nextsaas'), multiline: true },
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
}: EditorEditProps<HomeAutomationAttributes>): JSX.Element {
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
					<TextControl
						label={__('Flow label', 'nextsaas')}
						value={attributes.flowLabel ?? ''}
						onChange={(flowLabel) => setAttributes({ flowLabel })}
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
						renderItemExtras={(draft, update) => (
							<MediaControl
								value={{
									id: 'number' === typeof draft.mediaId ? draft.mediaId : 0,
									url: 'string' === typeof draft.mediaUrl ? draft.mediaUrl : '',
									alt: 'string' === typeof draft.mediaAlt ? draft.mediaAlt : '',
								}}
								labels={{
									title: __('Icon image', 'nextsaas'),
									select: __('Select icon image', 'nextsaas'),
									replace: __('Replace icon image', 'nextsaas'),
									remove: __('Remove icon image', 'nextsaas'),
									alt: __('Alternative text', 'nextsaas'),
								}}
								onSelect={(selected) =>
									update({
										mediaId: selected.id,
										mediaUrl: selected.url,
										mediaAlt: selected.alt,
									})
								}
								onRemove={() => update({ mediaId: 0, mediaUrl: '', mediaAlt: '' })}
								onChangeAlt={(mediaAlt) => update({ mediaAlt })}
							/>
						)}
					/>
				</PanelBody>
			<ColourControls attributes={attributes} setAttributes={setAttributes} /><SpacingControls attributes={attributes} setAttributes={setAttributes} /></InspectorControls>

			<BlockPreview block="nextsaas/home-automation" attributes={attributes} setAttributes={setAttributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...itemTargets]}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomeAutomationAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
