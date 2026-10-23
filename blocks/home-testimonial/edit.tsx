/**
 * Editor for `nextsaas/home-testimonial`.
 *
 * The block is dynamic, so PHP owns the only authoritative front-end markup and the
 * canvas shows it through `BlockPreview`. Section copy is edited where it appears;
 * the repeatable list is a repeater in the sidebar and, where `render.php` marks a
 * row field, also in place through a dot-path overlay target.
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
import type { HomeTestimonialAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'note', label: __('Note', 'nextsaas'), multiline: true },
];

/** Fields of one repeater row. */
const itemFields: RepeaterField[] = [
	{ key: 'meta', label: __('Credit line', 'nextsaas') },
	{ key: 'heading', label: __('Quote', 'nextsaas'), control: 'textarea' },
];

/** Row fields `render.php` marks for in-place editing, as attribute keys. */
const itemRichKeys: Array<{ key: string; label: string; multiline: boolean }> = [
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<HomeTestimonialAttributes>): JSX.Element {
	const items = Array.isArray(attributes.items) ? attributes.items : [];

	/*
	 * Row overlay targets are dot paths derived from the list length, so they stay
	 * correct after an insert, a delete or a reorder, and they always match the
	 * `data-nextsaas-rich` markers the renderer prints.
	 */
	const itemTargets: RichTextTarget[] = items.flatMap((_item, index) =>
		itemRichKeys.map((field) => ({
			path: `items.${index}.${field.key}`,
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
				</PanelBody>
				<PanelBody title={__('Carousel', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Carousel label', 'nextsaas')}
						help={__(
							'Accessible name of the slide list, announced by screen readers.',
							'nextsaas'
						)}
						value={attributes.carouselLabel ?? ''}
						onChange={(carouselLabel) => setAttributes({ carouselLabel })}
					/>
				</PanelBody>
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
				<PanelBody title={__('Stories', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Stories', 'nextsaas')}
						items={items}
						fields={itemFields}
						onChange={(next) => setAttributes({ items: next })}
						createItem={() => ({})}
						getItemLabel={(item, index) =>
							'string' === typeof item.heading && '' !== item.heading
								? item.heading
								: `${__('Story', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/home-testimonial" attributes={attributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...itemTargets]}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomeTestimonialAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
