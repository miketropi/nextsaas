export interface SearchSiteSearchAttributes extends Record<string, unknown> {
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
