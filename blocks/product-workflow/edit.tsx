import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import Repeater from '../../resources/ts/editor/repeater';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { ProductWorkflowAttributes, WorkflowStep } from './types';

const overlayTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'note', label: __('Workflow note', 'nextsaas'), multiline: true },
];

export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<ProductWorkflowAttributes>): JSX.Element {
	return <>
		<InspectorControls>
			<PanelBody title={__('Workflow steps', 'nextsaas')} initialOpen>
				<Repeater label={__('Steps', 'nextsaas')} items={attributes.steps ?? []} fields={[
					{ key: 'meta', label: __('Number', 'nextsaas') },
					{ key: 'heading', label: __('Heading', 'nextsaas'), control: 'rich', tagName: 'h3' },
					{ key: 'body', label: __('Description', 'nextsaas'), control: 'rich', tagName: 'p', multiline: true },
				]} onChange={(steps) => setAttributes({ steps: steps as WorkflowStep[] })} createItem={() => ({ meta: '', heading: '', body: '' })} getItemLabel={(item, index) => String(item.heading || `${__('Step', 'nextsaas')} ${index + 1}`)} />
			</PanelBody>
		</InspectorControls>
		<BlockPreview block="nextsaas/product-workflow" attributes={attributes} />
		<RichTextOverlay targets={[...overlayTargets, ...(attributes.steps ?? []).flatMap((_, index) => ([
			{ path: `steps.${index}.heading`, label: __('Step heading', 'nextsaas') },
			{ path: `steps.${index}.body`, label: __('Step description', 'nextsaas'), multiline: true },
		]))]} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<ProductWorkflowAttributes>)} isSelected={isSelected} />
	</>;
}
