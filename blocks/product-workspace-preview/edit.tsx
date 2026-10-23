import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BlockPreview from '../../resources/ts/editor/block-preview';
import MediaControl from '../../resources/ts/editor/media-control';
import Repeater from '../../resources/ts/editor/repeater';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { PreviewCaption, ProductWorkspacePreviewAttributes } from './types';

export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<ProductWorkspacePreviewAttributes>): JSX.Element {
	const targets: RichTextTarget[] = [{ path: 'primaryLabel', label: __('Primary link label', 'nextsaas') }, { path: 'secondaryLabel', label: __('Secondary link label', 'nextsaas') }, ...(attributes.captions ?? []).map((_, index) => ({ path: `captions.${index}.text`, label: __('Caption', 'nextsaas') }))];
	return <><InspectorControls><PanelBody title={__('Product screenshot', 'nextsaas')} initialOpen>
		<MediaControl value={{ id: attributes.mediaId ?? 0, url: attributes.mediaUrl ?? '', alt: attributes.mediaAlt ?? '' }} labels={{ title: __('Product screenshot', 'nextsaas'), select: __('Select image', 'nextsaas'), replace: __('Replace image', 'nextsaas'), remove: __('Remove image', 'nextsaas'), alt: __('Alternative text', 'nextsaas') }} onSelect={(media) => setAttributes({ mediaId: media.id, mediaUrl: media.url, mediaAlt: media.alt })} onRemove={() => setAttributes({ mediaId: 0, mediaUrl: '', mediaAlt: '' })} onChangeAlt={(mediaAlt) => setAttributes({ mediaAlt })} />
	</PanelBody><PanelBody title={__('Caption row', 'nextsaas')} initialOpen={false}><Repeater label={__('Captions', 'nextsaas')} items={attributes.captions ?? []} fields={[{ key: 'text', label: __('Caption', 'nextsaas'), control: 'rich', tagName: 'span' }]} onChange={(captions) => setAttributes({ captions: captions as PreviewCaption[] })} createItem={() => ({ text: '' })} getItemLabel={(item, index) => String(item.text || `${__('Caption', 'nextsaas')} ${index + 1}`)} /></PanelBody><PanelBody title={__('Links', 'nextsaas')} initialOpen={false}><TextControl label={__('Primary URL', 'nextsaas')} help={urlHelpWithStructuralNote} value={attributes.primaryUrl ?? ''} onChange={(primaryUrl) => setAttributes({ primaryUrl })} /><TextControl label={__('Secondary URL', 'nextsaas')} help={urlHelpWithStructuralNote} value={attributes.secondaryUrl ?? ''} onChange={(secondaryUrl) => setAttributes({ secondaryUrl })} /></PanelBody></InspectorControls>
	<BlockPreview block="nextsaas/product-workspace-preview" attributes={attributes} /><RichTextOverlay targets={targets} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<ProductWorkspacePreviewAttributes>)} isSelected={isSelected} /></>;
}
