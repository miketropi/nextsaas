/**
 * Editor for `nextsaas/post-article-hero`.
 *
 * Everything except the two destinations is the article's own data on the front end,
 * so the sidebar carries the breadcrumb targets, the byline wording and the
 * deterministic copy the renderer falls back to when no post is in context — which is
 * exactly what an editor sees on the canvas outside the loop.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { PostArticleHeroAttributes } from './types';

/** The labels the breadcrumb and the copy control print. */
const targets: RichTextTarget[] = [
	{ path: 'homeLabel', label: __('Breadcrumb home label', 'nextsaas') },
	{ path: 'archiveLabel', label: __('Breadcrumb archive label', 'nextsaas') },
	{ path: 'authorLabel', label: __('Byline label', 'nextsaas') },
	{ path: 'copyLabel', label: __('Copy link label', 'nextsaas') },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<PostArticleHeroAttributes>): JSX.Element {
	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Breadcrumb', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Home URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.homeUrl ?? ''}
						onChange={(homeUrl) => setAttributes({ homeUrl })}
					/>
					<TextControl
						label={__('Archive URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.archiveUrl ?? ''}
						onChange={(archiveUrl) => setAttributes({ archiveUrl })}
					/>
				</PanelBody>

				<PanelBody title={__('Byline', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Date prefix', 'nextsaas')}
						value={attributes.datePrefix ?? ''}
						onChange={(datePrefix) => setAttributes({ datePrefix })}
					/>
				</PanelBody>

				<PanelBody title={__('Preview fallback', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Category line', 'nextsaas')}
						help={__(
							'Shown only when the block renders without a post, which is what the editor canvas does.',
							'nextsaas'
						)}
						value={attributes.fallbackCategory ?? ''}
						onChange={(fallbackCategory) => setAttributes({ fallbackCategory })}
					/>
					<TextControl
						label={__('Title', 'nextsaas')}
						value={attributes.fallbackTitle ?? ''}
						onChange={(fallbackTitle) => setAttributes({ fallbackTitle })}
					/>
					<TextControl
						label={__('Lead', 'nextsaas')}
						value={attributes.fallbackLead ?? ''}
						onChange={(fallbackLead) => setAttributes({ fallbackLead })}
					/>
				</PanelBody>
			</InspectorControls>

			<BlockPreview block="nextsaas/post-article-hero" attributes={attributes} />

			<RichTextOverlay
				targets={targets}
				attributes={attributes}
				setAttributes={(next) =>
					setAttributes(next as Partial<PostArticleHeroAttributes>)
				}
				isSelected={isSelected}
			/>
		</>
	);
}
