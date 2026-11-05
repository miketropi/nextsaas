import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface PrincipleItem extends Record<string, unknown> { meta: string; heading: string; body: string; }
export interface AboutUsPrinciplesAttributes extends Record<string, unknown>, SectionAppearanceAttributes { sectionId: string; eyebrow: string; heading: string; lead: string; items: PrincipleItem[]; }
