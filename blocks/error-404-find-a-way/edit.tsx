/**
 * Editor for `nextsaas/error-404-find-a-way`.
 *
 * The cards are hand-authored copy rather than generated, so they are a repeater in
 * the sidebar and there is no in-canvas rich-text overlay: every value the renderer
 * prints is inside a row this list already owns.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { Error404FindAWayAttributes } from './types';

/** Fields of one recovery card. */
const itemFields: RepeaterField[] = [
	{ key: 'heading', label: __('Heading', 'nextsaas') },
	{ key: 'body', label: __('Supporting line', 'nextsaas'), control: 'textarea' },
	{ key: 'url', label: __('Link URL', 'nextsaas'), help: urlHelpWithStructuralNote },
];

export default function Edit({
	attributes,
	setAttributes,
}: EditorEditProps<Error404FindAWayAttributes>): JSX.Element {
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

				<PanelBody title={__('Recovery cards', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Recovery cards', 'nextsaas')}
						items={items}
						fields={itemFields}
						onChange={(next) =>
							setAttributes({ items: next as Error404FindAWayAttributes['items'] })
						}
						createItem={() => ({ heading: '', body: '', url: '' })}
						getItemLabel={(item, index) =>
							'string' === typeof item.heading && '' !== item.heading
								? item.heading
								: `${__('Card', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/error-404-find-a-way" attributes={attributes} />
		</>
	);
}
