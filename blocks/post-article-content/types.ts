import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface PostArticleContentAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
 sectionId: string;
 tocLabel: string;
 tocAriaLabel: string;
 primaryLabel: string;
 primaryUrl: string;
 secondaryLabel: string;
 secondaryUrl: string;
}
