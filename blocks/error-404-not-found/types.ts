import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface Error404NotFoundAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
 sectionId: string;
 eyebrow: string;
 errorNumber: string;
 heading: string;
 lead: string;
 primaryLabel: string;
 primaryUrl: string;
 secondaryLabel: string;
 secondaryUrl: string;
}
