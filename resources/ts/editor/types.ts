import type { BlockEditProps } from '@wordpress/blocks';

export type EditorEditProps<Attrs extends Record<string, unknown>> = BlockEditProps<Attrs>;

/**
 * The appearance attributes every custom block declares.
 *
 * One block root carries the section's own background, its heading colour, its
 * content colour and the gap between the items of the section's main layout. All
 * four are optional strings: an empty value means "theme default" and the renderer
 * emits nothing for it, which is what keeps a section that was saved before these
 * controls existed byte-identical.
 */
export interface SectionAppearanceAttributes extends Record<string, unknown> {
	sectionBg?: string;
	sectionHeading?: string;
	sectionText?: string;
	sectionGap?: string;
}

export interface RepeaterFieldSpec {
	key: string;
	label: string;
	help?: string;
	control?: 'text' | 'textarea' | 'select' | 'toggle' | 'rich' | 'number';
	options?: Array<{ label: string; value: string }>;
	placeholder?: string;
	min?: number;
	max?: number;
	step?: number;
	tagName?: 'p' | 'h2' | 'h3' | 'span' | 'li';
	multiline?: boolean;
}

export interface MediaValue {
	mediaId: number;
	mediaUrl: string;
	mediaAlt: string;
}

export interface CtaValue {
	label: string;
	url: string;
	variant?: 'primary' | 'secondary' | 'ghost';
	arrow?: boolean;
}

export interface NavItemValue {
	label: string;
	url: string;
	children?: NavItemValue[];
}
