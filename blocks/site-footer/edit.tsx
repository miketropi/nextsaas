/** Editor controls for the site-wide footer's bounded content model. */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import BlockPreview from '../../resources/ts/editor/block-preview';
import MediaControl from '../../resources/ts/editor/media-control';
import Repeater, { type RepeaterItem } from '../../resources/ts/editor/repeater';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { BottomItem, FooterColumn, FooterLink, SiteFooterAttributes, SocialButton } from './types';

const overlayTargets: RichTextTarget[] = [
	{ path: 'newsletterHeading', label: __('Newsletter heading', 'nextsaas'), multiline: true },
	{ path: 'newsletterLabel', label: __('Newsletter label', 'nextsaas') },
	{ path: 'newsletterNote', label: __('Newsletter note', 'nextsaas'), multiline: true },
	{ path: 'brandLabel', label: __('Brand label', 'nextsaas') },
	{ path: 'aboutLineOne', label: __('First about line', 'nextsaas'), multiline: true },
	{ path: 'aboutLineTwo', label: __('Second about line', 'nextsaas'), multiline: true },
	{ path: 'socialHeading', label: __('Social heading', 'nextsaas') },
	{ path: 'socialNote', label: __('Social note', 'nextsaas') },
	{ path: 'wordmark', label: __('Wordmark', 'nextsaas') },
	{ path: 'copyright', label: __('Copyright', 'nextsaas') },
];

const linkFields = [
	{ key: 'label', label: __('Label', 'nextsaas') },
	{ key: 'url', label: __('URL', 'nextsaas'), help: urlHelpWithStructuralNote },
	{ key: 'element', label: __('Element', 'nextsaas'), control: 'select' as const, options: [ { label: __('Link', 'nextsaas'), value: 'a' }, { label: __('Notice button', 'nextsaas'), value: 'button' } ] },
];

export default function Edit({ attributes, setAttributes, isSelected }: EditorEditProps<SiteFooterAttributes>): JSX.Element {
	return <>
		<InspectorControls>
			<PanelBody title={__('Newsletter', 'nextsaas')} initialOpen>
				<TextControl label={__('Input placeholder', 'nextsaas')} value={attributes.newsletterPlaceholder ?? ''} onChange={(newsletterPlaceholder) => setAttributes({ newsletterPlaceholder })} />
				<TextControl label={__('Submit button label', 'nextsaas')} value={attributes.newsletterButtonLabel ?? ''} onChange={(newsletterButtonLabel) => setAttributes({ newsletterButtonLabel })} />
				<TextControl label={__('Initial status text', 'nextsaas')} value={attributes.newsletterStatusText ?? ''} onChange={(newsletterStatusText) => setAttributes({ newsletterStatusText })} />
				<TextControl label={__('Preview success text', 'nextsaas')} value={attributes.newsletterSuccessText ?? ''} onChange={(newsletterSuccessText) => setAttributes({ newsletterSuccessText })} />
				<TextControl label={__('Form action', 'nextsaas')} help={__('Leave empty for the local preview-only validation. A URL submits normally.', 'nextsaas')} value={attributes.newsletterAction ?? ''} onChange={(newsletterAction) => setAttributes({ newsletterAction })} />
			</PanelBody>
			<PanelBody title={__('Brand', 'nextsaas')} initialOpen={false}>
				<SelectControl label={__('Brand mode', 'nextsaas')} value={attributes.brandMode ?? 'text'} options={[ { label: __('Text', 'nextsaas'), value: 'text' }, { label: __('Image', 'nextsaas'), value: 'image' } ]} onChange={(brandMode) => setAttributes({ brandMode })} />
				<TextControl label={__('Brand URL', 'nextsaas')} help={urlHelpWithStructuralNote} value={attributes.brandUrl ?? ''} onChange={(brandUrl) => setAttributes({ brandUrl })} />
				<TextControl label={__('Brand accessible label', 'nextsaas')} value={attributes.brandAriaLabel ?? ''} onChange={(brandAriaLabel) => setAttributes({ brandAriaLabel })} />
				<MediaControl value={{ id: attributes.brandMediaId ?? 0, url: attributes.brandMediaUrl ?? '', alt: attributes.brandMediaAlt ?? '' }} labels={{ title: __('Brand image', 'nextsaas'), select: __('Select image', 'nextsaas'), replace: __('Replace image', 'nextsaas'), remove: __('Remove image', 'nextsaas'), alt: __('Alternative text', 'nextsaas') }} onSelect={(media) => setAttributes({ brandMediaId: media.id, brandMediaUrl: media.url, brandMediaAlt: media.alt })} onRemove={() => setAttributes({ brandMediaId: 0, brandMediaUrl: '', brandMediaAlt: '' })} onChangeAlt={(brandMediaAlt) => setAttributes({ brandMediaAlt })} />
			</PanelBody>
			<PanelBody title={__('Footer columns', 'nextsaas')} initialOpen={false}>
				<Repeater label={__('Columns', 'nextsaas')} items={attributes.columns as RepeaterItem[]} fields={[{ key: 'heading', label: __('Heading', 'nextsaas') }]} onChange={(columns) => setAttributes({ columns: columns as FooterColumn[] })} createItem={() => ({ heading: '', links: [] })} getItemLabel={(item, index) => String(item.heading || `${__('Column', 'nextsaas')} ${index + 1}`)} minimumItems={3} renderItemExtras={(draft, update) => <Repeater label={__('Links', 'nextsaas')} items={(draft.links as RepeaterItem[]) ?? []} fields={linkFields} onChange={(links) => update({ links: links as FooterLink[] })} createItem={() => ({ label: '', url: '', element: 'a' })} getItemLabel={(item) => String(item.label || __('Link', 'nextsaas'))} />} />
			</PanelBody>
			<PanelBody title={__('Social row', 'nextsaas')} initialOpen={false}>
				<TextControl label={__('Navigation accessible label', 'nextsaas')} value={attributes.socialAriaLabel ?? ''} onChange={(socialAriaLabel) => setAttributes({ socialAriaLabel })} />
				<Repeater label={__('Social buttons', 'nextsaas')} items={attributes.socialButtons as RepeaterItem[]} fields={[{ key: 'label', label: __('Label', 'nextsaas') }, { key: 'icon', label: __('Icon name', 'nextsaas') }]} onChange={(socialButtons) => setAttributes({ socialButtons: socialButtons as SocialButton[] })} createItem={() => ({ label: '', icon: 'github' })} getItemLabel={(item) => String(item.label || __('Social button', 'nextsaas'))} />
			</PanelBody>
			<PanelBody title={__('Bottom row and notices', 'nextsaas')} initialOpen={false}>
				<Repeater label={__('Bottom items', 'nextsaas')} items={attributes.bottomItems as RepeaterItem[]} fields={[{ key: 'label', label: __('Label', 'nextsaas') }, { key: 'element', label: __('Element', 'nextsaas'), control: 'select', options: [{ label: __('Text', 'nextsaas'), value: 'text' }, { label: __('Link', 'nextsaas'), value: 'a' }, { label: __('Notice button', 'nextsaas'), value: 'button' }] }, { key: 'href', label: __('URL', 'nextsaas') }]} onChange={(bottomItems) => setAttributes({ bottomItems: bottomItems as BottomItem[] })} createItem={() => ({ label: '', element: 'text', href: '' })} getItemLabel={(item) => String(item.label || __('Bottom item', 'nextsaas'))} />
				<TextControl label={__('Dialog eyebrow', 'nextsaas')} value={attributes.dialogEyebrow ?? ''} onChange={(dialogEyebrow) => setAttributes({ dialogEyebrow })} />
				<TextControl label={__('Dialog close label', 'nextsaas')} value={attributes.dialogCloseLabel ?? ''} onChange={(dialogCloseLabel) => setAttributes({ dialogCloseLabel })} />
				<TextControl label={__('Social title suffix', 'nextsaas')} value={attributes.socialTitleSuffix ?? ''} onChange={(socialTitleSuffix) => setAttributes({ socialTitleSuffix })} />
				<TextControl label={__('Social notice body', 'nextsaas')} value={attributes.socialDialogBody ?? ''} onChange={(socialDialogBody) => setAttributes({ socialDialogBody })} />
				<TextControl label={__('Privacy notice title', 'nextsaas')} value={attributes.privacyDialogTitle ?? ''} onChange={(privacyDialogTitle) => setAttributes({ privacyDialogTitle })} />
				<TextControl label={__('Terms notice title', 'nextsaas')} value={attributes.termsDialogTitle ?? ''} onChange={(termsDialogTitle) => setAttributes({ termsDialogTitle })} />
				<TextControl label={__('Legal notice body', 'nextsaas')} value={attributes.legalDialogBody ?? ''} onChange={(legalDialogBody) => setAttributes({ legalDialogBody })} />
			</PanelBody>
		</InspectorControls>
		<BlockPreview block="nextsaas/site-footer" attributes={attributes} />
		<RichTextOverlay targets={overlayTargets} attributes={attributes} setAttributes={(next) => setAttributes(next as Partial<SiteFooterAttributes>)} isSelected={isSelected} />
	</>;
}
