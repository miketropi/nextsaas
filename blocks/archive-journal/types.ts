import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface ArchiveJournalAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
 sectionId:string; eyebrow:string; heading:string; lead:string; ctaLabel:string; ctaUrl:string;
 artLabel:string; artNumber:string; artTitle:string; artMeta:string; filterLabel:string; allLabel:string;
}
