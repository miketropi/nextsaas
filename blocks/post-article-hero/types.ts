import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface PostArticleHeroAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
 sectionId: string;
 homeLabel: string;
 homeUrl: string;
 archiveLabel: string;
 archiveUrl: string;
 fallbackCategory: string;
 fallbackTitle: string;
 fallbackLead: string;
 authorLabel: string;
 datePrefix: string;
 copyLabel: string;
}
