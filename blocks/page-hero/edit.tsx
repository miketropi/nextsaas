import { InspectorControls } from '@wordpress/block-editor';
import BlockPreview from '../../resources/ts/editor/block-preview';
import ColourControls from '../../resources/ts/editor/colour-controls';
import SpacingControls from '../../resources/ts/editor/spacing-controls';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { PageHeroAttributes } from './types';

const targets: RichTextTarget[] = [
	{ path: 'breadcrumbHomeLabel', label: 'Breadcrumb home label' },
	{ path: 'breadcrumbCurrentLabel', label: 'Breadcrumb current label' },
	{ path: 'eyebrow', label: 'Eyebrow' },
	{ path: 'heading', label: 'Heading', multiline: true },
	{ path: 'lead', label: 'Lead', multiline: true },
];
export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<PageHeroAttributes>): JSX.Element {
	return <><InspectorControls><ColourControls attributes={attributes} setAttributes={setAttributes} /><SpacingControls attributes={attributes} setAttributes={setAttributes} /></InspectorControls><BlockPreview block="nextsaas/page-hero" attributes={attributes} setAttributes={setAttributes} /><RichTextOverlay targets={targets} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<PageHeroAttributes>)} isSelected={isSelected} /></>;
}
