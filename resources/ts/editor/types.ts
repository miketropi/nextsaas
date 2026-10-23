import type { BlockEditProps } from '@wordpress/blocks';

export type EditorEditProps<Attrs extends Record<string, unknown>> = BlockEditProps<Attrs>;

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
