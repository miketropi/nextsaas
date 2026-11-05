import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface PreviewCaption extends Record<string, unknown> { text: string; }
export interface ProductWorkspacePreviewAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
	sectionId: string; mediaId: number; mediaUrl: string; mediaAlt: string;
	placeholderLabel: string; placeholderTop: string; placeholderBadge: string;
	placeholderTitle: string; placeholderNote: string; placeholderFoot: string; placeholderAspect: string;
	captions: PreviewCaption[]; primaryLabel: string; primaryUrl: string;
	primaryVariant: 'primary' | 'secondary' | 'ghost'; secondaryLabel: string; secondaryUrl: string;
}
