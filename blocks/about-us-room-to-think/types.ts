import type { SectionAppearanceAttributes } from '../../resources/ts/editor/types';

export interface AboutUsRoomToThinkAttributes extends Record<string, unknown>, SectionAppearanceAttributes { sectionId: string; mediaId: number; mediaUrl: string; mediaAlt: string; caption: string; credit: string; }
