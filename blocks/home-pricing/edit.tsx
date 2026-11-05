/**
 * Editor for `nextsaas/home-pricing`.
 *
 * A plan owns eight fields, so the tier list is a repeater and each row is edited
 * there; the two figures the toggle swaps are edited as a pair, because a plan with one
 * period filled in and the other empty would show a stale note on the front end.
 * Features are a nested repeater of plain strings.
 */

import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextareaControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import ColourControls from '../../resources/ts/editor/colour-controls';
import SpacingControls from '../../resources/ts/editor/spacing-controls';
import Repeater, {
	type RepeaterField,
	type RepeaterItem,
} from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { HomePricingAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'offerNote', label: __('Offer note', 'nextsaas') },
];

/** Fields of one plan row. */
const planFields: RepeaterField[] = [
	{ key: 'meta', label: __('Number and kicker', 'nextsaas') },
	{ key: 'name', label: __('Plan name', 'nextsaas') },
	{ key: 'description', label: __('Description', 'nextsaas'), control: 'textarea' },
	{ key: 'monthlyLabel', label: __('Monthly price', 'nextsaas'), help: __('Shown as entered, e.g. $18.', 'nextsaas') },
	{ key: 'yearlyLabel', label: __('Yearly price', 'nextsaas'), help: __('The monthly equivalent shown for yearly billing.', 'nextsaas') },
	{ key: 'unit', label: __('Price unit', 'nextsaas') },
	{ key: 'note', label: __('Period note', 'nextsaas') },
	{ key: 'ctaLabel', label: __('Link label', 'nextsaas') },
	{ key: 'ctaUrl', label: __('Link URL', 'nextsaas'), help: urlHelpWithStructuralNote },
];

/**
 * Feature lines of a plan row.
 *
 * The plan row is an index-signature record, so the flat list has to be checked before
 * it can be handed to the textarea. Blank lines are kept: a line the author is still
 * typing is their work in progress, and the renderer skips a blank entry rather than
 * printing an empty list item.
 *
 * @param row Plan row.
 * @return Feature lines.
 */
function readFeatures(row: RepeaterItem): string[] {
	const features = row.features;

	return Array.isArray(features)
		? features.filter((feature): feature is string => 'string' === typeof feature)
		: [];
}

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<HomePricingAttributes>): JSX.Element {
	const plans = Array.isArray(attributes.plans) ? attributes.plans : [];

	const planTargets: RichTextTarget[] = plans.flatMap((_plan, index) => [
		{
			path: `plans.${index}.ctaLabel`,
			label: `${__('Link label', 'nextsaas')} ${index + 1}`,
		},
		{
			path: `plans.${index}.monthlyLabel`,
			label: `${__('Monthly price', 'nextsaas')} ${index + 1}`,
		},
	]);

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

				<PanelBody title={__('Billing period', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Group label', 'nextsaas')}
						value={attributes.billingLabel ?? ''}
						onChange={(billingLabel) => setAttributes({ billingLabel })}
					/>
					<TextControl
						label={__('Monthly label', 'nextsaas')}
						value={attributes.monthlyLabel ?? ''}
						onChange={(monthlyLabel) => setAttributes({ monthlyLabel })}
					/>
					<TextControl
						label={__('Yearly label', 'nextsaas')}
						value={attributes.yearlyLabel ?? ''}
						onChange={(yearlyLabel) => setAttributes({ yearlyLabel })}
					/>
					<TextControl
						label={__('Monthly period note', 'nextsaas')}
						help={__('Used by a plan whose own note is empty.', 'nextsaas')}
						value={attributes.monthlyNote ?? ''}
						onChange={(monthlyNote) => setAttributes({ monthlyNote })}
					/>
					<TextControl
						label={__('Yearly period note', 'nextsaas')}
						value={attributes.yearlyNote ?? ''}
						onChange={(yearlyNote) => setAttributes({ yearlyNote })}
					/>
				</PanelBody>

				<PanelBody title={__('Plans', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Plans', 'nextsaas')}
						items={plans}
						fields={planFields}
						onChange={(next) =>
							setAttributes({ plans: next as HomePricingAttributes['plans'] })
						}
						createItem={() => ({
							meta: '',
							name: '',
							description: '',
							monthlyLabel: '',
							yearlyLabel: '',
							unit: '',
							note: '',
							ctaLabel: '',
							ctaUrl: '',
							features: [],
							featured: false,
						})}
						getItemLabel={(item, index) =>
							'string' === typeof item.name && '' !== item.name
								? item.name
								: `${__('Plan', 'nextsaas')} ${index + 1}`
						}
						renderItemExtras={(draft, update) => (
							<>
								<TextareaControl
									__nextHasNoMarginBottom
									label={__('Features', 'nextsaas')}
									help={__('One feature per line. No item limit.', 'nextsaas')}
									value={readFeatures(draft).join('\n')}
									onChange={(text) =>
										update({ features: text.split(/\r?\n/) })
									}
								/>
								<ToggleControl
									label={__('Feature this plan', 'nextsaas')}
									help={__(
										'Adds the accent treatment the approved render gives the middle tier.',
										'nextsaas'
									)}
									checked={true === draft.featured}
									onChange={(featured) => update({ featured })}
								/>
							</>
						)}
					/>
				</PanelBody>
			<ColourControls attributes={attributes} setAttributes={setAttributes} /><SpacingControls attributes={attributes} setAttributes={setAttributes} /></InspectorControls>

			<BlockPreview block="nextsaas/home-pricing" attributes={attributes} setAttributes={setAttributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...planTargets]}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomePricingAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
