import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BlockPreview from '../../resources/ts/editor/block-preview';
import MediaControl from '../../resources/ts/editor/media-control';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { AboutUsRoomToThinkAttributes } from './types';
const targets: RichTextTarget[] = [{ path: 'caption', label: __('Caption', 'nextsaas') }, { path: 'credit', label: __('Credit', 'nextsaas') }];
export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<AboutUsRoomToThinkAttributes>): JSX.Element { return <><InspectorControls><PanelBody title={__('Photograph', 'nextsaas')}><MediaControl value={{ id: attributes.mediaId ?? 0, url: attributes.mediaUrl ?? '', alt: attributes.mediaAlt ?? '' }} labels={{ title: __('Workspace photograph', 'nextsaas'), select: __('Select image', 'nextsaas'), replace: __('Replace image', 'nextsaas'), remove: __('Remove image', 'nextsaas'), alt: __('Alternative text', 'nextsaas') }} onSelect={(media) => setAttributes({ mediaId: media.id, mediaUrl: media.url, mediaAlt: media.alt })} onRemove={() => setAttributes({ mediaId: 0, mediaUrl: '', mediaAlt: '' })} onChangeAlt={(mediaAlt) => setAttributes({ mediaAlt })} /></PanelBody></InspectorControls><BlockPreview block="nextsaas/about-us-room-to-think" attributes={attributes} /><RichTextOverlay targets={targets} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<AboutUsRoomToThinkAttributes>)} isSelected={isSelected} /></>; }
