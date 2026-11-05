import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface SearchSiteSearchAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
 sectionId: string;
 fieldLabel: string;
 placeholder: string;
 submitLabel: string;
 filterLabel: string;
 allLabel: string;
 pagesLabel: string;
 articlesLabel: string;
 eyebrow: string;
 quickLinks: Array<Record<string, unknown>>;
}
