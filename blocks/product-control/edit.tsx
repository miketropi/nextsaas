import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater from '../../resources/ts/editor/repeater';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { ControlItem, ProductControlAttributes } from './types';

const fixedTargets: RichTextTarget[] = [{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') }, { path: 'heading', label: __('Heading', 'nextsaas'), multiline: true }, { path: 'note', label: __('Note', 'nextsaas'), multiline: true }];
export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<ProductControlAttributes>): JSX.Element {
	return <><InspectorControls><PanelBody title={__('Control items', 'nextsaas')} initialOpen><Repeater label={__('Items', 'nextsaas')} items={attributes.items ?? []} fields={[{ key: 'heading', label: __('Heading', 'nextsaas'), control: 'rich', tagName: 'h3' }, { key: 'body', label: __('Description', 'nextsaas'), control: 'rich', tagName: 'p', multiline: true }]} onChange={(items) => setAttributes({ items: items as ControlItem[] })} createItem={() => ({ heading: '', body: '' })} getItemLabel={(item, index) => String(item.heading || `${__('Item', 'nextsaas')} ${index + 1}`)} /></PanelBody></InspectorControls>
	<BlockPreview block="nextsaas/product-control" attributes={attributes} />
	<RichTextOverlay targets={[...fixedTargets, ...(attributes.items ?? []).flatMap((_, index) => ([{ path: `items.${index}.heading`, label: __('Item heading', 'nextsaas') }, { path: `items.${index}.body`, label: __('Item description', 'nextsaas'), multiline: true }]))]} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<ProductControlAttributes>)} isSelected={isSelected} /></>;
}
