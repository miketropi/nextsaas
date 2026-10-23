/**
 * Editor for `nextsaas/search-site-search`.
 *
 * The results the front end shows come from the template's own `core/query`, so the
 * canvas preview shows this block on its own — the form, the type filter and the
 * default state — which is exactly the part this block owns. Every control is an
 * input or a link rendered by PHP, so the sidebar only carries the strings and the
 * quick-link list.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { Notice, PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { SearchSiteSearchAttributes } from './types';

/** The one value the block renders with markup. */
const targets: RichTextTarget[] = [{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') }];

/** Fields of one starting-point link. */
const linkFields: RepeaterField[] = [
	{ key: 'label', label: __('Label', 'nextsaas') },
	{ key: 'url', label: __('Link URL', 'nextsaas'), help: urlHelpWithStructuralNote },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<SearchSiteSearchAttributes>): JSX.Element {
	const quickLinks = Array.isArray(attributes.quickLinks) ? attributes.quickLinks : [];

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Form', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Field label', 'nextsaas')}
						help={__(
							'Visually hidden, and the field’s accessible name.',
							'nextsaas'
						)}
						value={attributes.fieldLabel ?? ''}
						onChange={(fieldLabel) => setAttributes({ fieldLabel })}
					/>
					<TextControl
						label={__('Placeholder', 'nextsaas')}
						value={attributes.placeholder ?? ''}
						onChange={(placeholder) => setAttributes({ placeholder })}
					/>
					<TextControl
						label={__('Submit label', 'nextsaas')}
						value={attributes.submitLabel ?? ''}
						onChange={(submitLabel) => setAttributes({ submitLabel })}
					/>
				</PanelBody>

				<PanelBody title={__('Type filter', 'nextsaas')} initialOpen={false}>
					<Notice status="info" isDismissible={false}>
						{__(
							'The filter is rendered as links so a search can be narrowed, shared and bookmarked without JavaScript. Each one keeps the entered term.',
							'nextsaas'
						)}
					</Notice>
					<TextControl
						label={__('Group label', 'nextsaas')}
						value={attributes.filterLabel ?? ''}
						onChange={(filterLabel) => setAttributes({ filterLabel })}
					/>
					<TextControl
						label={__('All label', 'nextsaas')}
						value={attributes.allLabel ?? ''}
						onChange={(allLabel) => setAttributes({ allLabel })}
					/>
					<TextControl
						label={__('Pages label', 'nextsaas')}
						value={attributes.pagesLabel ?? ''}
						onChange={(pagesLabel) => setAttributes({ pagesLabel })}
					/>
					<TextControl
						label={__('Articles label', 'nextsaas')}
						value={attributes.articlesLabel ?? ''}
						onChange={(articlesLabel) => setAttributes({ articlesLabel })}
					/>
				</PanelBody>

				<PanelBody title={__('Starting points', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Eyebrow', 'nextsaas')}
						help={__('Shown only while no search term is entered.', 'nextsaas')}
						value={attributes.eyebrow ?? ''}
						onChange={(eyebrow) => setAttributes({ eyebrow })}
					/>
					<Repeater
						label={__('Quick links', 'nextsaas')}
						items={quickLinks}
						fields={linkFields}
						onChange={(next) =>
							setAttributes({ quickLinks: next as SearchSiteSearchAttributes['quickLinks'] })
						}
						createItem={() => ({ label: '', url: '' })}
						getItemLabel={(item, index) =>
							'string' === typeof item.label && '' !== item.label
								? item.label
								: `${__('Link', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/search-site-search" attributes={attributes} />

			<RichTextOverlay
				targets={targets}
				attributes={attributes}
				setAttributes={(next) =>
					setAttributes(next as Partial<SearchSiteSearchAttributes>)
				}
				isSelected={isSelected}
			/>
		</>
	);
}
