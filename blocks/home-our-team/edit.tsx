/**
 * Editor for `nextsaas/home-our-team`.
 *
 * The block is dynamic, so PHP owns the only authoritative front-end markup and the
 * canvas shows it through `BlockPreview`. Section copy is edited where it appears;
 * the team list is a repeater whose rows own their own portrait and placeholder
 * strings, so each card can be filled in independently.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import MediaControl from '../../resources/ts/editor/media-control';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { HomeOurTeamAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'note', label: __('Note', 'nextsaas'), multiline: true },
];

/** Fields of one person row. */
const itemFields: RepeaterField[] = [
	{ key: 'meta', label: __('Credit line', 'nextsaas') },
	{ key: 'heading', label: __('Name', 'nextsaas') },
	{ key: 'body', label: __('Role and description', 'nextsaas'), control: 'textarea' },
];

/**
 * Portrait of a person row.
 *
 * The repeater row is an index-signature record, so its media keys have to be read
 * defensively before they can be handed to the picker.
 *
 * @param row Person row.
 * @return Media value for the picker.
 */
function readPortrait(row: Record<string, unknown>): { id: number; url: string; alt: string } {
	return {
		id: 'number' === typeof row.mediaId ? row.mediaId : 0,
		url: 'string' === typeof row.mediaUrl ? row.mediaUrl : '',
		alt: 'string' === typeof row.mediaAlt ? row.mediaAlt : '',
	};
}

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<HomeOurTeamAttributes>): JSX.Element {
	const items = Array.isArray(attributes.items) ? attributes.items : [];

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

				<PanelBody title={__('People', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('People', 'nextsaas')}
						items={items}
						fields={itemFields}
						onChange={(next) => setAttributes({ items: next })}
						createItem={() => ({})}
						getItemLabel={(item, index) =>
							'string' === typeof item.heading && '' !== item.heading
								? item.heading
								: `${__('Person', 'nextsaas')} ${index + 1}`
						}
						renderItemExtras={(draft, update) => (
							<MediaControl
								value={readPortrait(draft)}
								labels={{
									title: __('Portrait', 'nextsaas'),
									select: __('Select portrait', 'nextsaas'),
									replace: __('Replace portrait', 'nextsaas'),
									remove: __('Remove portrait', 'nextsaas'),
									alt: __('Alternative text', 'nextsaas'),
								}}
								onSelect={(selected) =>
									update({
										mediaId: selected.id,
										mediaUrl: selected.url,
										mediaAlt: selected.alt,
									})
								}
								onRemove={() =>
									update({ mediaId: 0, mediaUrl: '', mediaAlt: '' })
								}
								onChangeAlt={(mediaAlt) => update({ mediaAlt })}
							/>
						)}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/home-our-team" attributes={attributes} />

			<RichTextOverlay
				targets={sectionTargets}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomeOurTeamAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
