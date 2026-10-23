/**
 * Editor for `nextsaas/product-capabilities`.
 *
 * Each panel owns a tab label, a heading, a body, a feature list and a reserved
 * screenshot, so the panel list is a repeater whose rows expand into a nested
 * feature repeater and a media picker. That keeps the whole section editable from
 * one place instead of forcing an editor to hunt through a flat attribute list.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import MediaControl from '../../resources/ts/editor/media-control';
import Repeater, {
	type RepeaterField,
	type RepeaterItem,
} from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { ProductCapabilitiesAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
];

/** Fields of one panel row. */
const panelFields: RepeaterField[] = [
	{ key: 'tabLabel', label: __('Tab label', 'nextsaas') },
	{ key: 'heading', label: __('Panel heading', 'nextsaas') },
	{ key: 'body', label: __('Panel body', 'nextsaas'), control: 'textarea' },
];

/** Fields of one feature row inside a panel. */
const featureFields: RepeaterField[] = [
	{ key: 'feature', label: __('Feature', 'nextsaas') },
];

/**
 * Feature strings of a panel row.
 *
 * The repeater row is an index-signature record, so the nested list has to be
 * checked before it can be handed to the inner repeater.
 *
 * @param row Panel row.
 * @return Feature rows.
 */
function readFeatures(row: RepeaterItem): Array<Record<string, unknown>> {
	const features = row.features;

	if (!Array.isArray(features)) {
		return [];
	}

	return features
		.filter((feature): feature is string => 'string' === typeof feature)
		.map((feature) => ({ feature }));
}

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<ProductCapabilitiesAttributes>): JSX.Element {
	const panels = Array.isArray(attributes.panels) ? attributes.panels : [];

	const panelTargets: RichTextTarget[] = panels.flatMap((_panel, index) => [
		{
			path: `panels.${index}.heading`,
			label: `${__('Panel heading', 'nextsaas')} ${index + 1}`,
		},
		{
			path: `panels.${index}.body`,
			label: `${__('Panel body', 'nextsaas')} ${index + 1}`,
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
					<TextControl
						label={__('Tab list name', 'nextsaas')}
						help={__('Accessible name of the tab list.', 'nextsaas')}
						value={attributes.tabListLabel ?? ''}
						onChange={(tabListLabel) => setAttributes({ tabListLabel })}
					/>
				</PanelBody>

				<PanelBody title={__('Panels', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Panels', 'nextsaas')}
						items={panels}
						fields={panelFields}
						onChange={(next) =>
							setAttributes({ panels: next as ProductCapabilitiesAttributes['panels'] })
						}
						createItem={() => ({
							tabLabel: '',
							heading: '',
							body: '',
							features: [],
							mediaId: 0,
							mediaUrl: '',
							mediaAlt: '',
							placeholderLabel: '',
							placeholderTop: '',
							placeholderBadge: '',
							placeholderTitle: '',
							placeholderNote: '',
							placeholderFoot: '',
							placeholderAspect: '',
						})}
						getItemLabel={(item, index) =>
							'string' === typeof item.tabLabel && '' !== item.tabLabel
								? item.tabLabel
								: `${__('Panel', 'nextsaas')} ${index + 1}`
						}
						renderItemExtras={(draft, update) => (
							<>
								<Repeater
									label={__('Features', 'nextsaas')}
									items={readFeatures(draft)}
									fields={featureFields}
									onChange={(rows) =>
										update({
											features: rows.map((row) =>
												'string' === typeof row.feature ? row.feature : ''
											),
										})
									}
									createItem={() => ({ feature: '' })}
									getItemLabel={(row, index) =>
										'string' === typeof row.feature && '' !== row.feature
											? row.feature
											: `${__('Feature', 'nextsaas')} ${index + 1}`
									}
									minimumItems={0}
								/>
								<MediaControl
									value={{
										id:
											'number' === typeof draft.mediaId ? draft.mediaId : 0,
										url:
											'string' === typeof draft.mediaUrl ? draft.mediaUrl : '',
										alt:
											'string' === typeof draft.mediaAlt ? draft.mediaAlt : '',
									}}
									labels={{
										title: __('Panel screenshot', 'nextsaas'),
										select: __('Select screenshot', 'nextsaas'),
										replace: __('Replace screenshot', 'nextsaas'),
										remove: __('Remove screenshot', 'nextsaas'),
										alt: __('Alternative text', 'nextsaas'),
									}}
									onSelect={(media) =>
										update({
											mediaId: media.id,
											mediaUrl: media.url,
											mediaAlt: media.alt,
										})
									}
									onRemove={() =>
										update({ mediaId: 0, mediaUrl: '', mediaAlt: '' })
									}
									onChangeAlt={(mediaAlt) => update({ mediaAlt })}
								/>
							</>
						)}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/product-capabilities" attributes={attributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...panelTargets]}
				attributes={attributes}
				setAttributes={(next) =>
					setAttributes(next as Partial<ProductCapabilitiesAttributes>)
				}
				isSelected={isSelected}
			/>
		</>
	);
}
