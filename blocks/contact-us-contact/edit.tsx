/**
 * Editor for `nextsaas/contact-us-contact`.
 *
 * The canvas renders `InnerBlocks` rather than a server preview, unlike every other
 * NextSaaS section, because the form slot holds another block: an editor has to be able
 * to select that block, open its own sidebar and type into its fields. A
 * `ServerSideRender` preview would draw the parent form as inert markup and the parent
 * block would be unreachable.
 *
 * The slot is locked to `nextora/contact-form` and preconfigured with the NextSaaS
 * labels, so the parent block stays the single owner of submission, nonce, REST, email
 * and anti-spam.
 */

import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Notice, PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Repeater, { type RepeaterField } from '../../resources/ts/editor/repeater';
import RichTextOverlay, {
	type RichTextTarget,
} from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { ContactUsContactAttributes } from './types';

/** Accessible name of the form block, kept in one place for the template and the copy. */
const CONTACT_FORM_BLOCK = 'nextora/contact-form';

/**
 * The parent form's attributes for this section.
 *
 * Its own heading and description are switched off because the section supplies the
 * heading, and the labels are set to the NextSaaS wording so the reused block speaks
 * the design's language without a fork.
 */
const CONTACT_FORM_ATTRIBUTES = {
	showHeading: false,
	showDescription: false,
	enablePhoneField: false,
	fullNameLabel: 'Full name',
	emailLabel: 'Work email',
	messageLabel: 'What’s on your mind?',
	messagePlaceholder:
		'A little context helps. Tell us about your team and what you’d like to change.',
	buttonLabel: 'Preview your message',
};

/** Section copy that renders unconditionally. */
const targets: RichTextTarget[] = [
	{ path: 'eyebrow', label: __('Eyebrow', 'nextsaas') },
	{ path: 'heading', label: __('Heading', 'nextsaas'), multiline: true },
];

/** Fields of one contact route. */
const routeFields: RepeaterField[] = [
	{ key: 'heading', label: __('Heading', 'nextsaas') },
	{ key: 'body', label: __('Body', 'nextsaas'), control: 'textarea' },
	{ key: 'linkLabel', label: __('Link label', 'nextsaas') },
	{ key: 'linkUrl', label: __('Link URL', 'nextsaas'), help: urlHelpWithStructuralNote },
];

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
}: EditorEditProps<ContactUsContactAttributes>): JSX.Element {
	const blockProps = useBlockProps({
		className: 'nextsaas-scope nextsaas-contact-us-contact section ruled',
	});
	const routes = Array.isArray(attributes.routes) ? attributes.routes : [];

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

				<PanelBody title={__('Contact routes', 'nextsaas')} initialOpen={false}>
					<Notice status="info" isDismissible={false}>
						{__(
							'The form itself is the parent theme’s contact-form block, so its submission, nonce, email and anti-spam behaviour are maintained in one place. Select it inside this section to edit its fields.',
							'nextsaas'
						)}
					</Notice>
					<Repeater
						label={__('Contact routes', 'nextsaas')}
						items={routes}
						fields={routeFields}
						onChange={(next) =>
							setAttributes({ routes: next as ContactUsContactAttributes['routes'] })
						}
						createItem={() => ({ heading: '', body: '', linkLabel: '', linkUrl: '' })}
						getItemLabel={(item, index) =>
							'string' === typeof item.heading && '' !== item.heading
								? item.heading
								: `${__('Route', 'nextsaas')} ${index + 1}`
						}
					/>
				</PanelBody>
			</InspectorControls>

			<section {...blockProps}>
				<div className="container">
					<div className="contact-layout">
						<aside className="contact-aside">
							<p className="eyebrow" data-nextsaas-rich="eyebrow">
								{attributes.eyebrow ?? ''}
							</p>
							<h2 data-nextsaas-rich="heading">{attributes.heading ?? ''}</h2>
							{routes.map((route, index) => (
								<div className="contact-route" key={index}>
									<h3>{'string' === typeof route.heading ? route.heading : ''}</h3>
									<p>{'string' === typeof route.body ? route.body : ''}</p>
								</div>
							))}
						</aside>
						<InnerBlocks
							allowedBlocks={[CONTACT_FORM_BLOCK]}
							template={[[CONTACT_FORM_BLOCK, CONTACT_FORM_ATTRIBUTES]]}
							templateLock="insert"
						/>
					</div>
				</div>
			</section>

			<RichTextOverlay
				targets={targets}
				attributes={attributes}
				setAttributes={(next) =>
					setAttributes(next as Partial<ContactUsContactAttributes>)
				}
				isSelected={isSelected}
			/>
		</>
	);
}
