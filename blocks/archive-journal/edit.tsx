import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BlockPreview from '../../resources/ts/editor/block-preview';
import RichTextOverlay, { type RichTextTarget } from '../../resources/ts/editor/rich-text-overlay';
import { urlHelpWithStructuralNote } from '../../resources/ts/editor/url-help';
import type { EditorEditProps } from '../../resources/ts/editor/types';
import type { ArchiveJournalAttributes } from './types';
const targets:RichTextTarget[]=['eyebrow','heading','lead','ctaLabel','artLabel','artNumber','artTitle','artMeta','allLabel'].map((path)=>({path,label:__(path,'nextsaas')}));
export default function Edit({attributes,setAttributes,isSelected}:EditorEditProps<ArchiveJournalAttributes>):JSX.Element{return <><InspectorControls><PanelBody title={__('Featured story link','nextsaas')}><TextControl label={__('URL','nextsaas')} help={urlHelpWithStructuralNote} value={attributes.ctaUrl??''} onChange={(ctaUrl)=>setAttributes({ctaUrl})}/><TextControl label={__('Filter accessible label','nextsaas')} value={attributes.filterLabel??''} onChange={(filterLabel)=>setAttributes({filterLabel})}/></PanelBody></InspectorControls><BlockPreview block="nextsaas/archive-journal" attributes={attributes}/><RichTextOverlay targets={targets} attributes={attributes} setAttributes={(v)=>setAttributes(v as Partial<ArchiveJournalAttributes>)} isSelected={isSelected}/></>}
