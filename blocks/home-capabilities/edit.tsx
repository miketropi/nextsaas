/**
 * Editor for `nextsaas/home-capabilities`.
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
import type { HomeCapabilitiesAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
];

/** Fields of one row row. */
const itemFields: RepeaterField[] = [
	{ key: 'meta', label: __('Number', 'nextsaas') },
	{ key: 'heading', label: __('Heading', 'nextsaas') },
	{ key: 'body', label: __('Body', 'nextsaas'), control: 'textarea' },
];

/** Row fields `render.php` marks for in-place editing. */
const itemRichKeys: Array<{ key: string; label: string; multiline: boolean }> = [
	{ key: 'meta', label: __('Number', 'nextsaas'), multiline: false },
	{ key: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ key: 'body', label: __('Body', 'nextsaas'), multiline: true },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<HomeCapabilitiesAttributes>): JSX.Element {
	const items = Array.isArray(attributes.items) ? attributes.items : [];

	/*
	 * Row overlay targets are dot paths derived from the list length, so they stay
	 * correct after an insert, a delete or a reorder and always match the
	 * `data-nextsaas-rich` markers the renderer prints.
	 */
	const itemTargets: RichTextTarget[] = items.flatMap((_item, index) =>
		itemRichKeys.map((field) => ({
			path: `items.${index}.${field.key}`,
			label: `${field.label} ${index + 1}`,
			multiline: field.multiline,
		})),
	);

	/*
	 * The reserved screenshot, stored as a one-row list because the same attribute
	 * carries the labelled-placeholder strings. `render.php` falls back to the
	 * placeholder whenever no attachment is chosen.
	 */
	const media: Record<string, unknown> =
		(Array.isArray(attributes.media) ? attributes.media[0] : undefined) ?? {};
	const patchMedia = (patch: Record<string, unknown>): void => {
		setAttributes({ media: [{ ...media, ...patch }] });
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

				<PanelBody title={__('Rows', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Rows', 'nextsaas')}
						items={items}
						fields={itemFields}
						onChange={(next) => setAttributes({ items: next })}
						createItem={() => ({})}
						getItemLabel={(item, index) =>
							'string' === typeof item.heading && '' !== item.heading
								? item.heading
								: `${__('Row', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>

				<PanelBody title={__('Media', 'nextsaas')} initialOpen={false}>
					<MediaControl
						value={{
							id: 'number' === typeof media.mediaId ? media.mediaId : 0,
							url: 'string' === typeof media.mediaUrl ? media.mediaUrl : '',
							alt: 'string' === typeof media.mediaAlt ? media.mediaAlt : '',
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

			<BlockPreview block="nextsaas/home-capabilities" attributes={attributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...itemTargets]}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomeCapabilitiesAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
