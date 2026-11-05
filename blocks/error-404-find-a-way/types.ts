import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface Error404FindAWayAttributes extends Record<string, unknown>, SectionAppearanceAttributes {
 sectionId: string;
 items: Array<Record<string, unknown>>;
}
