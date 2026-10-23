/**
 * Editor for `nextsaas/site-header`.
 *
 * The header is chrome: it renders a classic WordPress menu plus a handful of
 * strings, so the sidebar carries all of them and there is no in-canvas rich-text
 * overlay — the brand wordmark sits next to a decorative asterisk glyph, and an
 * overlay field would replace the whole line (glyph included) with plain text.
 *
 * The canvas shows the server's own markup through `BlockPreview`, which means the
 * `primary` menu an editor assigned is what they preview.
 */

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	Notice,
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import MediaControl from '../../resources/ts/editor/media-control';
import Repeater, { type RepeaterItem } from '../../resources/ts/editor/repeater';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { HeaderNavChild, HeaderNavItem, SiteHeaderAttributes } from './types';

/** Fields of one top-level navigation row. */
const itemFields = [
	{ key: 'label', label: __('Label', 'nextsaas') },
	{ key: 'url', label: __('URL', 'nextsaas'), help: urlHelpWithStructuralNote },
];

/** Fields of one submenu row. */
const childFields = [
	{ key: 'label', label: __('Label', 'nextsaas') },
	{ key: 'url', label: __('URL', 'nextsaas'), help: urlHelpWithStructuralNote },
	{ key: 'description', label: __('Description', 'nextsaas'), control: 'text' as const },
];

/**
 * Children of an item, read defensively out of the shared repeater's row type.
 *
 * `RepeaterItem` is an index-signature record, so a nested list has to be checked
 * before it can be handed to the inner repeater.
 *
 * @param row Row being edited.
 * @return Child rows.
 */
function readChildren(row: RepeaterItem): HeaderNavChild[] {
	const children = row.children;

	if (!Array.isArray(children)) {
		return [];
	}

	return children.filter(
		(child): child is HeaderNavChild =>
			!!child && 'object' === typeof child && 'label' in child && 'url' in child,
	);
}

export default function Edit({
	attributes,
	setAttributes,
}: EditorEditProps<SiteHeaderAttributes>): JSX.Element {
	const blockProps = useBlockProps({ className: 'nextsaas-site-header-editor' });
	const navItems = Array.isArray(attributes.navItems) ? attributes.navItems : [];
	const hasMenu = true === window.nextsaasHeader?.hasMenu;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Brand', 'nextsaas')} initialOpen>
					<TextControl
						label={__('Wordmark', 'nextsaas')}
						value={attributes.brandLabel ?? ''}
						onChange={(brandLabel) => setAttributes({ brandLabel })}
					/>
					<TextControl
						label={__('Link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.brandUrl ?? ''}
						onChange={(brandUrl) => setAttributes({ brandUrl })}
					/>
					<TextControl
						label={__('Accessible name', 'nextsaas')}
						value={attributes.brandAriaLabel ?? ''}
						onChange={(brandAriaLabel) => setAttributes({ brandAriaLabel })}
					/>
					<SelectControl
						label={__('Logo', 'nextsaas')}
						value={attributes.brandMode ?? 'text'}
						options={[
							{ label: __('Wordmark', 'nextsaas'), value: 'text' },
							{ label: __('Image', 'nextsaas'), value: 'image' },
						]}
						onChange={(value) =>
							setAttributes({ brandMode: 'image' === value ? 'image' : 'text' })
						}
					/>
					{'image' === attributes.brandMode ? (
						<MediaControl
							value={{
								id: attributes.brandMediaId ?? 0,
								url: attributes.brandMediaUrl ?? '',
								alt: attributes.brandMediaAlt ?? '',
							}}
							labels={{
								title: __('Site logo', 'nextsaas'),
								select: __('Select logo', 'nextsaas'),
								replace: __('Replace logo', 'nextsaas'),
								remove: __('Remove logo', 'nextsaas'),
								alt: __('Alternative text', 'nextsaas'),
							}}
							onSelect={(media) =>
								setAttributes({
									brandMediaId: media.id,
									brandMediaUrl: media.url,
									brandMediaAlt: media.alt,
								})
							}
							onRemove={() =>
								setAttributes({
									brandMediaId: 0,
									brandMediaUrl: '',
									brandMediaAlt: '',
								})
							}
							onChangeAlt={(brandMediaAlt) => setAttributes({ brandMediaAlt })}
						/>
					) : null}
				</PanelBody>

				<PanelBody title={__('Call to action', 'nextsaas')} initialOpen={false}>
					<TextControl
						label={__('Label', 'nextsaas')}
						value={attributes.ctaLabel ?? ''}
						onChange={(ctaLabel) => setAttributes({ ctaLabel })}
					/>
					<TextControl
						label={__('Link URL', 'nextsaas')}
						help={urlHelpWithStructuralNote}
						value={attributes.ctaUrl ?? ''}
						onChange={(ctaUrl) => setAttributes({ ctaUrl })}
					/>
					<SelectControl
						label={__('Button style', 'nextsaas')}
						value={attributes.ctaVariant ?? 'secondary'}
						options={[
							{ label: __('Primary', 'nextsaas'), value: 'primary' },
							{ label: __('Secondary', 'nextsaas'), value: 'secondary' },
							{ label: __('Ghost', 'nextsaas'), value: 'ghost' },
						]}
						onChange={(value) =>
							setAttributes({
								ctaVariant:
									'primary' === value || 'ghost' === value ? value : 'secondary',
							})
						}
					/>
				</PanelBody>

				<PanelBody title={__('Availability', 'nextsaas')} initialOpen={false}>
					<ToggleControl
						label={__('Sticky header', 'nextsaas')}
						checked={attributes.sticky ?? true}
						onChange={(sticky) => setAttributes({ sticky })}
					/>
					<TextControl
						label={__('Skip link label', 'nextsaas')}
						value={attributes.skipLinkLabel ?? ''}
						onChange={(skipLinkLabel) => setAttributes({ skipLinkLabel })}
					/>
					<TextControl
						label={__('Drawer toggle label', 'nextsaas')}
						value={attributes.menuToggleLabel ?? ''}
						onChange={(menuToggleLabel) => setAttributes({ menuToggleLabel })}
					/>
				</PanelBody>

				<PanelBody title={__('Fallback navigation', 'nextsaas')} initialOpen={false}>
					<Notice status={hasMenu ? 'info' : 'warning'} isDismissible={false}>
						{hasMenu
							? __(
									'A menu is assigned to the Primary navigation location, so it is what visitors see. These rows are only used while no menu is assigned.',
									'nextsaas'
							  )
							: __(
									'No menu is assigned to the Primary navigation location. The rows below stand in for it; assign a menu to take over.',
									'nextsaas'
							  )}
					</Notice>
					<Repeater
						label={__('Top-level entries', 'nextsaas')}
						items={navItems}
						fields={itemFields}
						onChange={(next) =>
							setAttributes({ navItems: next as SiteHeaderAttributes['navItems'] })
						}
						createItem={() => ({ label: '', url: '', children: [] })}
						getItemLabel={(item, index) =>
							'string' === typeof item.label && '' !== item.label
								? item.label
								: `${__('Entry', 'nextsaas')} ${index + 1}`
						}
						renderItemExtras={(draft, update) => (
							<Repeater
								label={__('Submenu rows', 'nextsaas')}
								items={readChildren(draft)}
								fields={childFields}
								onChange={(children) => update({ children })}
								createItem={() => ({ label: '', url: '', description: '' })}
								getItemLabel={(item, index) =>
									'string' === typeof item.label && '' !== item.label
										? item.label
										: `${__('Row', 'nextsaas')} ${index + 1}`
								}
								minimumItems={0}
							/>
						)}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<BlockPreview block="nextsaas/site-header" attributes={attributes} />
			</div>
		</>
	);
}
