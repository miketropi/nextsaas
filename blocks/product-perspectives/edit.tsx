import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater from '../../resources/ts/editor/repeater';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { Perspective, ProductPerspectivesAttributes } from './types';

const fixedTargets: RichTextTarget[] = [{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') }, { path: 'heading', label: __('Heading', 'nextsaas'), multiline: true }];
export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<ProductPerspectivesAttributes>): JSX.Element {
	return <><InspectorControls><PanelBody title={__('Team perspectives', 'nextsaas')} initialOpen><Repeater label={__('Perspectives', 'nextsaas')} items={attributes.perspectives ?? []} fields={[
		{ key: 'heading', label: __('Heading', 'nextsaas'), control: 'rich', tagName: 'h3' },
		{ key: 'bodyOne', label: __('First paragraph', 'nextsaas'), control: 'rich', tagName: 'p', multiline: true },
		{ key: 'bodyTwo', label: __('Second paragraph', 'nextsaas'), control: 'rich', tagName: 'p', multiline: true },
	]} onChange={(perspectives) => setAttributes({ perspectives: perspectives as Perspective[] })} createItem={() => ({ heading: '', bodyOne: '', bodyTwo: '' })} getItemLabel={(item, index) => String(item.heading || `${__('Perspective', 'nextsaas')} ${index + 1}`)} /></PanelBody></InspectorControls>
	<BlockPreview block="nextsaas/product-perspectives" attributes={attributes} />
	<RichTextOverlay targets={[...fixedTargets, ...(attributes.perspectives ?? []).flatMap((_, index) => ([{ path: `perspectives.${index}.heading`, label: __('Perspective heading', 'nextsaas') }, { path: `perspectives.${index}.bodyOne`, label: __('First paragraph', 'nextsaas'), multiline: true }, { path: `perspectives.${index}.bodyTwo`, label: __('Second paragraph', 'nextsaas'), multiline: true }]))]} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<ProductPerspectivesAttributes>)} isSelected={isSelected} /></>;
}
