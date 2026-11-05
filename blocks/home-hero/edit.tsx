/**
 * Editor for `nextsaas/home-hero`.
 *
 * The landing hero is the most copy-dense section in the theme: a kicker pair, the
 * display heading, an orbit note, the lead, two calls to action, a reserved product
 * image with its own caption pair, and a use-case list. Everything that renders
 * unconditionally is edited in place; the destinations, the media slot and the
 * use-case rows live in the sidebar.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import ColourControls from '../../resources/ts/editor/colour-controls';
import SpacingControls from '../../resources/ts/editor/spacing-controls';
import MediaControl from '../../resources/ts/editor/media-control';
import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { HomeHeroAttributes } from './types';

/** Section copy that renders unconditionally. */
const sectionTargets: RichTextTarget[] = [
	{ path: 'kicker', label: __('Kicker', 'nextsaas') },
	{ path: 'kickerSecondary', label: __('Secondary kicker', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
	{ path: 'orbitCopy', label: __('Orbit note', 'nextsaas'), multiline: true },
	{ path: 'lead', label: __('Lead paragraph', 'nextsaas'), multiline: true },
	{ path: 'primaryLabel', label: __('Primary link label', 'nextsaas') },
	{ path: 'workspaceLabel', label: __('Workspace link label', 'nextsaas') },
	{ path: 'caption', label: __('Image caption', 'nextsaas') },
	{ path: 'captionNote', label: __('Image caption note', 'nextsaas') },
	{ path: 'useCasesLabel', label: __('Use-case list label', 'nextsaas') },
];

/** Fields of one use-case link. */
const useCaseFields: RepeaterField[] = [
	{ key: 'label', label: __('Label', 'nextsaas') },
	{ key: 'url', label: __('Link URL', 'nextsaas'), help: urlHelpWithStructuralNote },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<HomeHeroAttributes>): JSX.Element {
	const useCases = Array.isArray(attributes.useCases) ? attributes.useCases : [];

	/*
	 * Each use-case label is its own overlay target, derived from the list length so
	 * it stays correct after a reorder, and matching the marker the renderer prints.
	 */
	const useCaseTargets: RichTextTarget[] = useCases.map((_useCase, index) => ({
		path: `useCases.${index}.label`,
		label: `${__('Use case', 'nextsaas')} ${index + 1}`,
	}));

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Calls to action', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Primary link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.primaryUrl ?? ''}
						onChange={(primaryUrl) => setAttributes({ primaryUrl })}
					/>
					<TextControl
						label={__('Workspace link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.workspaceUrl ?? ''}
						onChange={(workspaceUrl) => setAttributes({ workspaceUrl })}
					/>
				</PanelBody>

				<PanelBody title={__('Product image', 'nextsaas')} initialOpen={false}>
					<MediaControl
						value={{
							id: attributes.mediaId ?? 0,
							url: attributes.mediaUrl ?? '',
							alt: attributes.mediaAlt ?? '',
						}}
						labels={{
							title: __('Product image', 'nextsaas'),
							select: __('Select image', 'nextsaas'),
							replace: __('Replace image', 'nextsaas'),
							remove: __('Remove image', 'nextsaas'),
							alt: __('Alternative text', 'nextsaas'),
						}}
						onSelect={(selected) =>
							setAttributes({
								mediaId: selected.id,
								mediaUrl: selected.url,
								mediaAlt: selected.alt,
							})
						}
						onRemove={() =>
							setAttributes({ mediaId: 0, mediaUrl: '', mediaAlt: '' })
						}
						onChangeAlt={(mediaAlt) => setAttributes({ mediaAlt })}
					/>
				</PanelBody>

				<PanelBody title={__('Hero decoration', 'nextsaas')} initialOpen={false}>
					<MediaControl
						value={{
							id: attributes.decorationMediaId ?? 0,
							url: attributes.decorationMediaUrl ?? '',
							alt: attributes.decorationMediaAlt ?? '',
						}}
						labels={{
							title: __('Hero decoration', 'nextsaas'),
							select: __('Select decoration', 'nextsaas'),
							replace: __('Replace decoration', 'nextsaas'),
							remove: __('Remove decoration', 'nextsaas'),
							alt: __('Alternative text', 'nextsaas'),
						}}
						onSelect={(selected) =>
							setAttributes({
								decorationMediaId: selected.id,
								decorationMediaUrl: selected.url,
								decorationMediaAlt: selected.alt,
							})
						}
						onRemove={() =>
							setAttributes({
								decorationMediaId: 0,
								decorationMediaUrl: '',
								decorationMediaAlt: '',
							})
						}
						onChangeAlt={(decorationMediaAlt) => setAttributes({ decorationMediaAlt })}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Rotate decoration', 'nextsaas')}
						help={__(
							'The star artwork turns slowly. Turn rotation off for a decoration that should stay still.',
							'nextsaas'
						)}
						checked={attributes.decorationRotate !== false}
						onChange={(decorationRotate) => setAttributes({ decorationRotate })}
					/>
				</PanelBody>

				<PanelBody title={__('Use cases', 'nextsaas')} initialOpen={false}>
					<Repeater
						label={__('Use cases', 'nextsaas')}
						items={useCases}
						fields={useCaseFields}
						onChange={(next) =>
							setAttributes({ useCases: next as HomeHeroAttributes['useCases'] })
						}
						createItem={() => ({ label: '', url: '', pick: '' })}
						getItemLabel={(item, index) =>
							'string' === typeof item.label && '' !== item.label
								? item.label
								: `${__('Use case', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>
			<ColourControls attributes={attributes} setAttributes={setAttributes} /><SpacingControls attributes={attributes} setAttributes={setAttributes} /></InspectorControls>

			<BlockPreview block="nextsaas/home-hero" attributes={attributes} setAttributes={setAttributes} />

			<RichTextOverlay
				targets={[...sectionTargets, ...useCaseTargets]}
				attributes={attributes}
				setAttributes={(next) => setAttributes(next as Partial<HomeHeroAttributes>)}
				isSelected={isSelected}
			/>
		</>
	);
}
