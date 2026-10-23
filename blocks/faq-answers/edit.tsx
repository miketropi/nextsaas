/**
 * Editor for `nextsaas/faq-answers`.
 *
 * The questions are a repeater and the categories are a repeater of their own, because
 * the two lists are edited independently and a question refers to its category by
 * value. Nothing is edited in place: every question sits inside a `<summary>` that the
 * renderer already owns, and an overlay field there would fight the disclosure.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { FaqAnswersAttributes } from './types';

/** Fields of one question. */
const itemFields: RepeaterField[] = [
	{ key: 'category', label: __('Category value', 'nextsaas'), help: __('Must match a filter value.', 'nextsaas') },
	{ key: 'question', label: __('Question', 'nextsaas') },
	{ key: 'answer', label: __('Answer', 'nextsaas'), control: 'textarea' },
];

/** Fields of one category filter. */
const categoryFields: RepeaterField[] = [
	{ key: 'label', label: __('Label', 'nextsaas') },
	{ key: 'value', label: __('Value', 'nextsaas') },
];

export default function Edit({
	attributes,
	setAttributes,
}: EditorEditProps<FaqAnswersAttributes>): JSX.Element {
	const items = Array.isArray(attributes.items) ? attributes.items : [];
	const categories = Array.isArray(attributes.categories) ? attributes.categories : [];

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
						label={__('Filter group label', 'nextsaas')}
						value={attributes.filterLabel ?? ''}
						onChange={(filterLabel) => setAttributes({ filterLabel })}
					/>
				</PanelBody>

				<PanelBody title={__('Categories', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Categories', 'nextsaas')}
						items={categories}
						fields={categoryFields}
						onChange={(next) =>
							setAttributes({ categories: next as FaqAnswersAttributes['categories'] })
						}
						createItem={() => ({ label: '', value: '' })}
						getItemLabel={(item, index) =>
							'string' === typeof item.label && '' !== item.label
								? item.label
								: `${__('Category', 'nextsaas')} ${index + 1}`
						}
						minimumItems={1}
					/>
				</PanelBody>

				<PanelBody title={__('Search and count', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Search field label', 'nextsaas')}
						help={__('Visually hidden, and the field’s accessible name.', 'nextsaas')}
						value={attributes.searchFieldLabel ?? ''}
						onChange={(searchFieldLabel) => setAttributes({ searchFieldLabel })}
					/>
					<TextControl
						label={__('Placeholder', 'nextsaas')}
						value={attributes.searchPlaceholder ?? ''}
						onChange={(searchPlaceholder) => setAttributes({ searchPlaceholder })}
					/>
					<TextControl
						label={__('Singular count', 'nextsaas')}
						help={__('Use %s for the number.', 'nextsaas')}
						value={attributes.countOne ?? ''}
						onChange={(countOne) => setAttributes({ countOne })}
					/>
					<TextControl
						label={__('Plural count', 'nextsaas')}
						help={__('Use %s for the number.', 'nextsaas')}
						value={attributes.countMany ?? ''}
						onChange={(countMany) => setAttributes({ countMany })}
					/>
				</PanelBody>

				<PanelBody title={__('Sidebar note', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Note', 'nextsaas')}
						value={attributes.noteLabel ?? ''}
						onChange={(noteLabel) => setAttributes({ noteLabel })}
					/>
					<TextControl
						label={__('Link label', 'nextsaas')}
						value={attributes.noteLinkLabel ?? ''}
						onChange={(noteLinkLabel) => setAttributes({ noteLinkLabel })}
					/>
					<TextControl
						label={__('Link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.noteLinkUrl ?? ''}
						onChange={(noteLinkUrl) => setAttributes({ noteLinkUrl })}
					/>
				</PanelBody>

				<PanelBody title={__('Questions', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Questions', 'nextsaas')}
						items={items}
						fields={itemFields}
						onChange={(next) =>
							setAttributes({ items: next as FaqAnswersAttributes['items'] })
						}
						createItem={() => ({ category: '', question: '', answer: '' })}
						getItemLabel={(item, index) =>
							'string' === typeof item.question && '' !== item.question
								? item.question
								: `${__('Question', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/faq-answers" attributes={attributes} />
		</>
	);
}
