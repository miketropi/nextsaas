import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BlockPreview from '../../resources/ts/editor/block-preview';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { AboutUsBuiltWithIntentionAttributes } from './types';
const targets: RichTextTarget[] = [{ path: 'eyebrow', label: 'eyebrow', multiline: true },{ path: 'heading', label: 'heading', multiline: true },{ path: 'bodyOne', label: 'bodyOne', multiline: true },{ path: 'bodyTwo', label: 'bodyTwo', multiline: true },{ path: 'primaryLabel', label: 'primaryLabel', multiline: true },{ path: 'secondaryLabel', label: 'secondaryLabel', multiline: true }];
export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<AboutUsBuiltWithIntentionAttributes>): JSX.Element { return <><InspectorControls><PanelBody title={__('Links', 'nextsaas')}><TextControl label={__('Primary URL', 'nextsaas')} value={attributes.primaryUrl ?? ''} onChange={(primaryUrl) => setAttributes({ primaryUrl })} /><TextControl label={__('Secondary URL', 'nextsaas')} value={attributes.secondaryUrl ?? ''} onChange={(secondaryUrl) => setAttributes({ secondaryUrl })} /></PanelBody></InspectorControls><BlockPreview block="nextsaas/about-us-built-with-intention" attributes={attributes} /><RichTextOverlay targets={targets} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<AboutUsBuiltWithIntentionAttributes>)} isSelected={isSelected} /></>; }
