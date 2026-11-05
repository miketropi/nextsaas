import { InspectorControls } from '@wordpress/block-editor';
import BlockPreview from '../../resources/ts/editor/block-preview';
import ColourControls from '../../resources/ts/editor/colour-controls';
import SpacingControls from '../../resources/ts/editor/spacing-controls';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { AboutUsOurPerspectiveAttributes } from './types';
const targets: RichTextTarget[] = [{ path: 'eyebrow', label: 'eyebrow', multiline: true },{ path: 'heading', label: 'heading', multiline: true },{ path: 'lead', label: 'lead', multiline: true },{ path: 'bodyOne', label: 'bodyOne', multiline: true },{ path: 'bodyTwo', label: 'bodyTwo', multiline: true }];
export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<AboutUsOurPerspectiveAttributes>): JSX.Element { return <><InspectorControls><ColourControls attributes={attributes} setAttributes={setAttributes} /><SpacingControls attributes={attributes} setAttributes={setAttributes} /></InspectorControls><BlockPreview block="nextsaas/about-us-our-perspective" attributes={attributes} setAttributes={setAttributes} /><RichTextOverlay targets={targets} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<AboutUsOurPerspectiveAttributes>)} isSelected={isSelected} /></>; }
