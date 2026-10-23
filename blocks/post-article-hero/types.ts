export interface PostArticleHeroAttributes extends Record<string, unknown> {
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
